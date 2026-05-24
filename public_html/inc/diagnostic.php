<?php
/**
 * Diagnostic engine: build a quiz, grade it, compute strong/weak topics,
 * persist results and skill scores, and seed a learning path.
 */
declare(strict_types=1);

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/ai.php';
require_once __DIR__ . '/learning.php';

const DIAGNOSTIC_SIZE = 10;
const DIAGNOSTIC_PASS = 70.0; // topic mastery >= this is "strong"

/** Pick MCQ questions for a subject, spread across its topics. */
function diagnostic_questions(int $subjectId, int $limit = DIAGNOSTIC_SIZE): array
{
    return db_all(
        'SELECT q.id, q.topic_id, q.question_text, q.marks
         FROM questions q
         WHERE q.subject_id = ? AND q.type = "mcq" AND q.status = "active"
         ORDER BY RAND() LIMIT ?',
        [$subjectId, $limit]
    );
}

function diagnostic_options(int $questionId): array
{
    return db_all('SELECT id, label, option_text FROM question_options WHERE question_id = ? ORDER BY sort_order', [$questionId]);
}

/** Ensure a reusable diagnostic_tests row exists for the subject. */
function ensure_diagnostic_test(int $subjectId): int
{
    $row = db_one('SELECT id FROM diagnostic_tests WHERE subject_id = ? AND status = "active" LIMIT 1', [$subjectId]);
    if ($row) {
        return (int) $row['id'];
    }
    $subject = db_one('SELECT name FROM subjects WHERE id = ?', [$subjectId]);
    return db_exec(
        'INSERT INTO diagnostic_tests (subject_id, title, description) VALUES (?,?,?)',
        [$subjectId, ($subject['name'] ?? 'Subject') . ' Diagnostic', 'Auto-generated diagnostic quiz']
    );
}

/**
 * Grade a submitted diagnostic.
 *
 * @param array $answers Map of question_id => selected option_id.
 * @return int The diagnostic_attempts id.
 */
function grade_diagnostic(int $userId, int $subjectId, array $answers): int
{
    $testId    = ensure_diagnostic_test($subjectId);
    $attemptId = db_exec(
        'INSERT INTO diagnostic_attempts (test_id, user_id, status) VALUES (?,?,?)',
        [$testId, $userId, 'in_progress']
    );

    $perTopic = []; // topic_id => ['total'=>, 'correct'=>]
    $totalQ = 0;
    $totalCorrect = 0;

    foreach ($answers as $qid => $optId) {
        $qid   = (int) $qid;
        $optId = (int) $optId;
        $q = db_one('SELECT id, topic_id FROM questions WHERE id = ? AND subject_id = ?', [$qid, $subjectId]);
        if (!$q) {
            continue;
        }
        $opt = db_one('SELECT is_correct FROM question_options WHERE id = ? AND question_id = ?', [$optId, $qid]);
        $isCorrect = $opt ? (bool) $opt['is_correct'] : false;

        db_exec(
            'INSERT INTO diagnostic_answers (attempt_id, question_id, answer_text, is_correct) VALUES (?,?,?,?)',
            [$attemptId, $qid, (string) $optId, $isCorrect ? 1 : 0]
        );

        $tid = (int) ($q['topic_id'] ?? 0);
        if ($tid) {
            $perTopic[$tid] ??= ['total' => 0, 'correct' => 0];
            $perTopic[$tid]['total']++;
            $perTopic[$tid]['correct'] += $isCorrect ? 1 : 0;
        }
        $totalQ++;
        $totalCorrect += $isCorrect ? 1 : 0;
    }

    $score = $totalQ ? round($totalCorrect / $totalQ * 100, 2) : 0.0;

    // Classify topics and update topic stats.
    $strong = [];
    $weak   = [];
    foreach ($perTopic as $tid => $agg) {
        $mastery = $agg['total'] ? round($agg['correct'] / $agg['total'] * 100, 2) : 0.0;
        $name = db_one('SELECT name FROM topics WHERE id = ?', [$tid])['name'] ?? ('Topic ' . $tid);
        if ($mastery >= DIAGNOSTIC_PASS) {
            $strong[] = $name;
        } else {
            $weak[] = ['id' => $tid, 'name' => $name, 'mastery' => $mastery];
        }
        db_exec(
            'INSERT INTO student_topic_stats (user_id, topic_id, total_attempts, total_correct, mastery)
             VALUES (?, ?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE
               total_attempts = total_attempts + VALUES(total_attempts),
               total_correct  = total_correct + VALUES(total_correct),
               mastery        = ROUND(total_correct / total_attempts * 100, 2)',
            [$userId, $tid, $agg['total'], $agg['correct'], $mastery]
        );
    }

    $weakNames = array_map(fn($w) => $w['name'], $weak);
    $recommendation = diagnostic_recommendation($score, $strong, $weakNames);

    db_exec(
        'INSERT INTO diagnostic_results (attempt_id, strong_topics, weak_topics, recommendation) VALUES (?,?,?,?)',
        [$attemptId, implode(', ', $strong), implode(', ', $weakNames), $recommendation]
    );
    db_exec(
        'UPDATE diagnostic_attempts SET score = ?, status = "completed", completed_at = NOW() WHERE id = ?',
        [$score, $attemptId]
    );

    // Seed an adaptive learning path from the weak topics.
    if ($weak) {
        generate_learning_path($userId, $subjectId, $weak);
    }

    return $attemptId;
}

/** Produce an encouraging recommendation, via AI when available. */
function diagnostic_recommendation(float $score, array $strong, array $weak): string
{
    $tpl = ai_prompt('diagnostic');
    $summary = sprintf(
        "Diagnostic score: %.0f%%. Strong topics: %s. Weak topics: %s. Write a short, encouraging summary and a prioritised practice plan.",
        $score,
        $strong ? implode(', ', $strong) : 'none yet',
        $weak ? implode(', ', $weak) : 'none'
    );
    $reply = ai_chat($tpl['system_prompt'], [['role' => 'user', 'content' => $summary]], (float) $tpl['temperature'], $tpl['model']);

    // ai_chat already falls back gracefully; if it returned the demo notice, build a concise local plan instead.
    if (str_contains($reply, 'demo mode')) {
        $plan = $weak
            ? 'Focus this week on: ' . implode(', ', array_slice($weak, 0, 3)) . '. Aim for 15 minutes of targeted practice daily.'
            : 'Great work — keep practising to maintain your mastery and try harder questions.';
        return sprintf('You scored %.0f%%. %s', $score, $plan);
    }
    return $reply;
}
