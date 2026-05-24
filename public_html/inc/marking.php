<?php
/**
 * Snap & Check: store answer uploads, run AI marking, expose results.
 *
 * OCR is integration-ready: if no OCR provider is wired, marking uses the
 * student's typed answer text. A multimodal model could read the image
 * directly in a future iteration.
 */
declare(strict_types=1);

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/ai.php';
require_once __DIR__ . '/notifications.php';

/**
 * Store an uploaded answer (image optional, typed text optional).
 * @return int|null upload id, or null on validation failure.
 */
function store_answer_upload(int $userId, ?int $questionId, array $file, string $typedText = ''): ?int
{
    $path = '';
    if (isset($file['error']) && $file['error'] === UPLOAD_ERR_OK) {
        $stored = store_upload($file, 'answers');
        if ($stored === null) {
            return null; // invalid file
        }
        $path = $stored;
    } elseif (trim($typedText) === '') {
        return null; // nothing submitted at all
    }

    // OCR hook (integration-ready). For now the typed text is the OCR text.
    $ocr = trim($typedText) !== '' ? $typedText : null;

    return db_exec(
        'INSERT INTO answer_uploads (user_id, question_id, file_path, ocr_text, status) VALUES (?,?,?,?,?)',
        [$userId, $questionId ?: null, $path, $ocr, 'uploaded']
    );
}

/** Run AI marking for an upload and persist the result. */
function run_ai_marking(int $uploadId): array
{
    $upload = db_one('SELECT * FROM answer_uploads WHERE id = ?', [$uploadId]);
    if (!$upload) {
        throw new RuntimeException('Upload not found.');
    }

    $question = $upload['question_id']
        ? db_one('SELECT q.*, t.name AS topic FROM questions q LEFT JOIN topics t ON t.id = q.topic_id WHERE q.id = ?', [$upload['question_id']])
        : null;
    $scheme = $upload['question_id']
        ? (db_one('SELECT scheme_text FROM question_marking_schemes WHERE question_id = ? LIMIT 1', [$upload['question_id']])['scheme_text'] ?? '')
        : '';

    $studentAnswer = $upload['ocr_text'] ?: '(handwritten image submitted; OCR not available in this environment)';

    $tpl = ai_prompt('marking');
    $prompt = "Question: " . ($question['question_text'] ?? 'See attached image') . "\n"
        . ($question['marks'] ?? 1) . " mark(s).\n"
        . ($scheme ? "Marking scheme: {$scheme}\n" : "")
        . ($question['explanation'] ?? '' ? "Model explanation: {$question['explanation']}\n" : "")
        . "Student answer: {$studentAnswer}\n\n"
        . "Respond ONLY with a JSON object using these keys: "
        . "score (number out of the marks above), correct_parts (string), mistakes (string), "
        . "correction (string), topic_weakness (string), next_recommendation (string), confidence (0-1).";

    $reply  = ai_chat($tpl['system_prompt'], [['role' => 'user', 'content' => $prompt]], (float) $tpl['temperature'], $tpl['model']);
    $parsed = parse_marking_json($reply);

    if ($parsed === null) {
        // Fallback (e.g. demo mode or non-JSON reply): store the raw guidance.
        $parsed = [
            'score'               => null,
            'correct_parts'       => '',
            'mistakes'            => '',
            'correction'          => $reply,
            'topic_weakness'      => $question['topic'] ?? '',
            'next_recommendation' => 'Review the explanation and try a similar question.',
            'confidence'          => null,
        ];
    }

    $resultId = db_exec(
        'INSERT INTO ai_marking_results
            (upload_id, score, correct_parts, mistakes, correction, topic_weakness, next_recommendation, confidence)
         VALUES (?,?,?,?,?,?,?,?)',
        [
            $uploadId,
            $parsed['score'],
            (string) $parsed['correct_parts'],
            (string) $parsed['mistakes'],
            (string) $parsed['correction'],
            (string) $parsed['topic_weakness'],
            (string) $parsed['next_recommendation'],
            $parsed['confidence'],
        ]
    );
    db_exec('UPDATE answer_uploads SET status = "marked" WHERE id = ?', [$uploadId]);

    notify(
        (int) $upload['user_id'],
        'Your answer was marked',
        $parsed['score'] !== null ? ('Score: ' . $parsed['score']) : 'AI feedback is ready.',
        'marking'
    );

    return ['id' => $resultId] + $parsed;
}

/** Extract a JSON object from an AI reply, tolerating surrounding prose. */
function parse_marking_json(string $reply): ?array
{
    $start = strpos($reply, '{');
    $end   = strrpos($reply, '}');
    if ($start === false || $end === false || $end <= $start) {
        return null;
    }
    $json = substr($reply, $start, $end - $start + 1);
    $data = json_decode($json, true);
    if (!is_array($data)) {
        return null;
    }
    return [
        'score'               => isset($data['score']) && is_numeric($data['score']) ? (float) $data['score'] : null,
        'correct_parts'       => $data['correct_parts'] ?? '',
        'mistakes'            => $data['mistakes'] ?? '',
        'correction'          => $data['correction'] ?? '',
        'topic_weakness'      => $data['topic_weakness'] ?? '',
        'next_recommendation' => $data['next_recommendation'] ?? '',
        'confidence'          => isset($data['confidence']) && is_numeric($data['confidence']) ? (float) $data['confidence'] : null,
    ];
}

function marking_result(int $uploadId): ?array
{
    return db_one('SELECT * FROM ai_marking_results WHERE upload_id = ? ORDER BY id DESC LIMIT 1', [$uploadId]);
}
