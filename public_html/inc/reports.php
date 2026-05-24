<?php
/**
 * Weekly AI parent report generation.
 */
declare(strict_types=1);

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/ai.php';

/** Build (or refresh) this week's AI report for a student. */
function generate_weekly_report(int $studentId): array
{
    $weekStart = date('Y-m-d', strtotime('monday this week'));

    $name    = db_one('SELECT name FROM users WHERE id = ?', [$studentId])['name'] ?? 'The student';
    $summaryRow = db_one('SELECT total_questions, total_correct, avg_score FROM student_progress_summary WHERE user_id = ?', [$studentId])
        ?? ['total_questions' => 0, 'total_correct' => 0, 'avg_score' => 0];
    $streak  = (int) (db_one('SELECT current_streak FROM student_streaks WHERE user_id = ?', [$studentId])['current_streak'] ?? 0);
    $weekCount = (int) (db_one(
        'SELECT COUNT(*) c FROM question_attempts WHERE user_id = ? AND created_at >= ?',
        [$studentId, $weekStart . ' 00:00:00']
    )['c'] ?? 0);

    $weak = db_all(
        'SELECT t.name, ts.mastery FROM student_topic_stats ts JOIN topics t ON t.id = ts.topic_id
         WHERE ts.user_id = ? ORDER BY ts.mastery ASC LIMIT 3',
        [$studentId]
    );
    $strong = db_all(
        'SELECT t.name FROM student_topic_stats ts JOIN topics t ON t.id = ts.topic_id
         WHERE ts.user_id = ? ORDER BY ts.mastery DESC LIMIT 3',
        [$studentId]
    );

    $weakNames   = array_map(fn($w) => $w['name'] . ' (' . round((float) $w['mastery']) . '%)', $weak);
    $strongNames = array_map(fn($s) => $s['name'], $strong);

    $facts = sprintf(
        "Student: %s. This week answered %d questions (lifetime %d, avg %.0f%%). Current streak: %d days. Strong topics: %s. Weak topics: %s. Write a warm weekly report for the parent with one concrete recommended action.",
        $name, $weekCount, (int) $summaryRow['total_questions'], (float) $summaryRow['avg_score'], $streak,
        $strongNames ? implode(', ', $strongNames) : 'building up',
        $weakNames ? implode(', ', $weakNames) : 'none yet'
    );

    $tpl   = ai_prompt('parent_report');
    $reply = ai_chat($tpl['system_prompt'], [['role' => 'user', 'content' => $facts]], (float) $tpl['temperature'], $tpl['model']);

    if (str_contains($reply, 'demo mode')) {
        $reply = sprintf(
            '%s answered %d questions this week with a %.0f%% average and a %d-day streak. %s',
            $name, $weekCount, (float) $summaryRow['avg_score'], $streak,
            $weakNames ? 'Focus area: ' . $weakNames[0] . '. A short daily practice session would help most.' : 'Keep up the consistent effort!'
        );
    }

    $recommendation = $weakNames
        ? 'Recommended: 15 minutes daily on ' . $weak[0]['name'] . ' this week.'
        : 'Recommended: keep the daily streak going.';

    // One report per student per week.
    $existing = db_one('SELECT id FROM weekly_ai_reports WHERE student_user_id = ? AND week_start = ?', [$studentId, $weekStart]);
    if ($existing) {
        db_exec('UPDATE weekly_ai_reports SET summary = ?, recommendation = ? WHERE id = ?', [$reply, $recommendation, $existing['id']]);
        $id = (int) $existing['id'];
    } else {
        $id = db_exec(
            'INSERT INTO weekly_ai_reports (student_user_id, week_start, summary, recommendation) VALUES (?,?,?,?)',
            [$studentId, $weekStart, $reply, $recommendation]
        );
    }

    return ['id' => $id, 'summary' => $reply, 'recommendation' => $recommendation, 'week_start' => $weekStart];
}
