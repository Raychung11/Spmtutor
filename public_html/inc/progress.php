<?php
/**
 * Progress tracking, streaks and XP helpers.
 */
declare(strict_types=1);

require_once __DIR__ . '/db.php';

/** Record a practice attempt and update all rollup stats. */
function record_attempt(int $userId, array $question, ?int $selectedOptionId, ?string $answerText, ?bool $isCorrect): void
{
    $score = $isCorrect ? (float) $question['marks'] : 0.0;
    db_exec(
        'INSERT INTO question_attempts (user_id, question_id, answer_text, selected_option_id, is_correct, score)
         VALUES (?,?,?,?,?,?)',
        [$userId, $question['id'], $answerText, $selectedOptionId, $isCorrect === null ? null : (int) $isCorrect, $score]
    );

    // Overall summary.
    db_exec(
        'INSERT INTO student_progress_summary (user_id, total_questions, total_correct, avg_score)
         VALUES (?, 1, ?, ?)
         ON DUPLICATE KEY UPDATE
           total_questions = total_questions + 1,
           total_correct   = total_correct + VALUES(total_correct),
           avg_score       = ROUND(total_correct / total_questions * 100, 2)',
        [$userId, $isCorrect ? 1 : 0, $isCorrect ? 100 : 0]
    );

    // Subject stats.
    db_exec(
        'INSERT INTO student_subject_stats (user_id, subject_id, total_attempts, total_correct, avg_score)
         VALUES (?, ?, 1, ?, ?)
         ON DUPLICATE KEY UPDATE
           total_attempts = total_attempts + 1,
           total_correct  = total_correct + VALUES(total_correct),
           avg_score      = ROUND(total_correct / total_attempts * 100, 2)',
        [$userId, $question['subject_id'], $isCorrect ? 1 : 0, $isCorrect ? 100 : 0]
    );

    // Topic stats / mastery.
    if (!empty($question['topic_id'])) {
        db_exec(
            'INSERT INTO student_topic_stats (user_id, topic_id, total_attempts, total_correct, mastery)
             VALUES (?, ?, 1, ?, ?)
             ON DUPLICATE KEY UPDATE
               total_attempts = total_attempts + 1,
               total_correct  = total_correct + VALUES(total_correct),
               mastery        = ROUND(total_correct / total_attempts * 100, 2)',
            [$userId, $question['topic_id'], $isCorrect ? 1 : 0, $isCorrect ? 100 : 0]
        );
    }

    award_xp($userId, $isCorrect ? 10 : 3, $isCorrect ? 'Correct answer' : 'Practice attempt');
    touch_streak($userId);
    check_badges($userId);
}

function award_xp(int $userId, int $amount, string $reason): void
{
    db_exec('INSERT INTO xp_logs (user_id, amount, reason) VALUES (?,?,?)', [$userId, $amount, $reason]);
    db_exec('UPDATE student_profiles SET xp = xp + ?, level = GREATEST(1, FLOOR((xp + ?) / 500) + 1) WHERE user_id = ?', [$amount, $amount, $userId]);
}

/** Update the daily streak. Counts consecutive active days. */
function touch_streak(int $userId): void
{
    $today = date('Y-m-d');
    db_exec('INSERT IGNORE INTO streak_logs (user_id, log_date) VALUES (?, ?)', [$userId, $today]);

    $row = db_one('SELECT current_streak, longest_streak, last_active_date FROM student_streaks WHERE user_id = ?', [$userId]);
    if (!$row) {
        db_exec('INSERT INTO student_streaks (user_id, current_streak, longest_streak, last_active_date) VALUES (?,1,1,?)', [$userId, $today]);
        return;
    }
    if ($row['last_active_date'] === $today) {
        return; // already counted today
    }
    $yesterday = date('Y-m-d', strtotime('-1 day'));
    $current   = ($row['last_active_date'] === $yesterday) ? (int) $row['current_streak'] + 1 : 1;
    $longest   = max($current, (int) $row['longest_streak']);
    db_exec(
        'UPDATE student_streaks SET current_streak = ?, longest_streak = ?, last_active_date = ? WHERE user_id = ?',
        [$current, $longest, $today, $userId]
    );
}

/** Award streak/volume badges where earned. */
function check_badges(int $userId): void
{
    $streak = (int) (db_one('SELECT current_streak FROM student_streaks WHERE user_id = ?', [$userId])['current_streak'] ?? 0);
    if ($streak >= 3) {
        grant_badge($userId, 'streak_3');
    }
    if ($streak >= 7) {
        grant_badge($userId, 'streak_7');
    }
    $mathCount = (int) (db_one(
        'SELECT COUNT(*) c FROM question_attempts qa JOIN questions q ON q.id = qa.question_id
         JOIN subjects s ON s.id = q.subject_id WHERE qa.user_id = ? AND s.slug = "mathematics"',
        [$userId]
    )['c'] ?? 0);
    if ($mathCount >= 50) {
        grant_badge($userId, 'math_warrior');
    }
}

function grant_badge(int $userId, string $code): void
{
    $badge = db_one('SELECT id FROM badges WHERE code = ?', [$code]);
    if ($badge) {
        db_exec('INSERT IGNORE INTO student_badges (user_id, badge_id) VALUES (?, ?)', [$userId, $badge['id']]);
    }
}

/** Ensure today's daily mission exists; return it. */
function ensure_daily_mission(int $userId): array
{
    $today = date('Y-m-d');
    $m = db_one('SELECT * FROM daily_missions WHERE user_id = ? AND mission_date = ?', [$userId, $today]);
    if (!$m) {
        db_exec(
            'INSERT INTO daily_missions (user_id, mission_date, description, target) VALUES (?,?,?,?)',
            [$userId, $today, 'Answer 5 practice questions today', 5]
        );
        $m = db_one('SELECT * FROM daily_missions WHERE user_id = ? AND mission_date = ?', [$userId, $today]);
    }
    // Recompute progress from today's attempts.
    $count = (int) (db_one(
        'SELECT COUNT(*) c FROM question_attempts WHERE user_id = ? AND DATE(created_at) = ?',
        [$userId, $today]
    )['c'] ?? 0);
    $done = $count >= (int) $m['target'] ? 1 : 0;
    db_exec('UPDATE daily_missions SET progress = ?, completed = ? WHERE id = ?', [$count, $done, $m['id']]);
    $m['progress'] = $count;
    $m['completed'] = $done;
    return $m;
}
