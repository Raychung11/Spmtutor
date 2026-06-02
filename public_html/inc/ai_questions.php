<?php
/**
 * AI question generation with per-subject prompts + two-pass critique.
 *
 * - subject_ai_default(subject) composes a default prompt from the subject's
 *   structured fields (exam board, language, type, notes).
 * - subject_ai_effective(subject) returns the subject's saved ai_prompt
 *   override if set, otherwise the default.
 * - generate_questions_for_topic($topicId, $count, $difficulty) calls the
 *   LLM in two passes (generate + critique) and inserts the survivors as
 *   `pending` rows in `questions` (+ options for MCQs).
 */
declare(strict_types=1);

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/ai.php';

/**
 * Are the phase9 per-subject AI columns present? Cached for the request.
 * Returns false when the production DB hasn't had migrations applied yet,
 * so callers can fall back to a minimal SELECT.
 */
function subject_ai_columns_present(): bool
{
    static $present = null;
    if ($present !== null) {
        return $present;
    }
    try {
        $row = db_one(
            "SELECT COUNT(*) AS n FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'subjects' AND COLUMN_NAME = 'ai_prompt'"
        );
        $present = (int) ($row['n'] ?? 0) > 0;
    } catch (Throwable $e) {
        $present = false;
    }
    return $present;
}

/**
 * Fetch a topic joined with subject AI fields, tolerating an unmigrated DB.
 * Always returns these keys (NULL when phase9 cols are missing): subject_id,
 * subject, ai_prompt, ai_subject_type, ai_exam_board, ai_language, ai_notes.
 */
function fetch_topic_with_subject_ai(int $topicId): ?array
{
    if (subject_ai_columns_present()) {
        return db_one(
            'SELECT t.*, s.id AS subject_id, s.name AS subject,
                    s.ai_prompt, s.ai_subject_type, s.ai_exam_board, s.ai_language, s.ai_notes
             FROM topics t JOIN subjects s ON s.id = t.subject_id WHERE t.id = ?',
            [$topicId]
        );
    }
    $row = db_one(
        'SELECT t.*, s.id AS subject_id, s.name AS subject
         FROM topics t JOIN subjects s ON s.id = t.subject_id WHERE t.id = ?',
        [$topicId]
    );
    if ($row) {
        $row['ai_prompt']       = null;
        $row['ai_subject_type'] = null;
        $row['ai_exam_board']   = null;
        $row['ai_language']     = null;
        $row['ai_notes']        = null;
    }
    return $row;
}

/** Default subject types the structured editor recognises. */
function subject_ai_types(): array
{
    return [
        'math'       => 'Mathematics',
        'science'    => 'Science (Physics / Chemistry / Biology)',
        'language'   => 'Language',
        'history'    => 'History (Sejarah / Geografi)',
        'religious'  => 'Religious / Moral',
        'commerce'   => 'Commerce / Accounting',
        'technology' => 'Technology / Computer Science',
        'general'    => 'General',
    ];
}

/** Compose the default per-subject system prompt from its structured fields. */
function subject_ai_default(array $subject): string
{
    $name  = $subject['name'] ?? 'this subject';
    $board = trim((string) ($subject['ai_exam_board'] ?? '')) ?: 'Malaysian SPM (KSSM syllabus)';
    $lang  = trim((string) ($subject['ai_language'] ?? '')) ?: 'English';
    $type  = $subject['ai_subject_type'] ?? 'general';
    $notes = trim((string) ($subject['ai_notes'] ?? ''));

    $rules = match ($type) {
        'math'       => "Double-check every calculation step. Show working in the explanation. Use standard exam notation (^, fractions, units). Every option must be plausible (use common mistakes as distractors), and exactly one must be unambiguously correct.",
        'science'    => "Use SI units. Stick to facts from the official syllabus. Avoid speculative or unverified claims. Cite the relevant formula in the explanation when applicable.",
        'language'   => "Test grammar, vocabulary or comprehension as in the official syllabus. Keep options grammatically parallel. Avoid culturally insensitive phrasing.",
        'history'    => "Use historically accurate dates, names and events from the syllabus chapter. Avoid speculation. Reference the chapter/topic in the explanation.",
        'religious'  => "Stay strictly within the official DSKP/syllabus framing. Use respectful, neutral wording. Avoid sectarian or controversial interpretations.",
        'commerce'   => "Use standard accounting / business definitions and Malaysian context where relevant. State assumptions clearly.",
        'technology' => "Use current standard terminology. Avoid platform-specific quirks unless the syllabus calls them out.",
        default      => "Be precise, factual and aligned with the syllabus.",
    };

    $prompt  = "You are an expert {$board} question writer for {$name}. Write questions in {$lang}.\n";
    $prompt .= "Stay strictly within the official {$name} syllabus and the specific topic/skills the user gives you. ";
    $prompt .= $rules . "\n";
    $prompt .= "Never invent topics or facts outside the named topic. If unsure, prefer a simpler, syllabus-aligned question.";
    if ($notes !== '') {
        $prompt .= "\nAdditional rules for this subject: " . $notes;
    }
    return $prompt;
}

/** Return the saved prompt if set, else the composed default. */
function subject_ai_effective(array $subject): string
{
    $custom = trim((string) ($subject['ai_prompt'] ?? ''));
    return $custom !== '' ? $custom : subject_ai_default($subject);
}

/**
 * Generate N MCQ drafts for a topic and insert them as `pending` questions.
 *
 * @return array ['inserted'=>int, 'flagged'=>int, 'errors'=>string[]]
 */
function generate_questions_for_topic(int $topicId, int $count, string $difficulty = 'mixed', bool $critique = true): array
{
    $count = max(1, min(20, $count));
    $topic = fetch_topic_with_subject_ai($topicId);
    if (!$topic) {
        return ['inserted' => 0, 'flagged' => 0, 'errors' => ['Topic not found.']];
    }
    if (!ai_enabled()) {
        return ['inserted' => 0, 'flagged' => 0, 'errors' => ['AI key is not configured. Go to Admin → AI Settings.']];
    }

    $skills = db_all('SELECT name, difficulty FROM skills WHERE topic_id = ? AND status = "active"', [$topicId]);
    $skillsList = $skills
        ? implode("\n", array_map(fn($s) => '- ' . $s['name'] . ' (' . $s['difficulty'] . ')', $skills))
        : '(no specific skills listed)';

    $system = subject_ai_effective($topic);

    $mixSpec = match ($difficulty) {
        'easy'   => 'all easy',
        'medium' => 'all medium',
        'hard'   => 'all hard',
        default  => 'a roughly even mix of easy / medium / hard',
    };

    $userPrompt = "Write {$count} multiple-choice questions for the topic \"{$topic['name']}\" "
        . "(subject: {$topic['subject']}). Use {$mixSpec} difficulty.\n\n"
        . ($topic['description'] ? "Topic description: " . $topic['description'] . "\n" : '')
        . "Skills under this topic:\n{$skillsList}\n\n"
        . "Return ONLY a JSON array. Each element must be an object with these exact keys:\n"
        . "  - question (string)\n"
        . "  - options (array of exactly 4 strings)\n"
        . "  - correct_index (integer 0-3)\n"
        . "  - explanation (string, 1-3 sentences explaining why the correct answer is correct)\n"
        . "  - difficulty (\"easy\", \"medium\" or \"hard\")\n"
        . "No prose before or after the JSON.";

    try {
        $raw = ai_chat($system, [['role' => 'user', 'content' => $userPrompt]], 0.4);
    } catch (Throwable $e) {
        return ['inserted' => 0, 'flagged' => 0, 'errors' => ['AI call failed: ' . $e->getMessage()]];
    }

    $draft = parse_questions_json($raw);
    if (!$draft) {
        $len = mb_strlen($raw);
        $head = mb_substr($raw, 0, 200);
        $tail = $len > 400 ? '… ' . mb_substr($raw, $len - 200) : '';
        $jsonErr = json_last_error_msg();
        return ['inserted' => 0, 'flagged' => 0, 'errors' => [
            "AI returned unparseable JSON ({$len} chars). json_decode said: {$jsonErr}",
            "Head: {$head}{$tail}",
            'Tip: if the output ends mid-text, raise Max output tokens in Admin → AI Settings. If json_decode complains about a syntax error, the model may have included unescaped control chars or trailing commas — try regenerating, or generate fewer questions per run.',
        ]];
    }

    // Optional second pass: critique each draft and keep a flag on suspect ones.
    $flagged = [];
    if ($critique) {
        foreach ($draft as $idx => $q) {
            $critic = critique_question($system, $q);
            if ($critic['ok'] === false) {
                $flagged[$idx] = $critic['reason'];
            }
        }
    }

    $inserted = 0;
    $errors   = [];
    foreach ($draft as $idx => $q) {
        if (!isset($q['question'], $q['options']) || !is_array($q['options']) || count($q['options']) !== 4) {
            $errors[] = "Draft #$idx skipped: missing/invalid fields.";
            continue;
        }
        $correct = (int) ($q['correct_index'] ?? 0);
        if ($correct < 0 || $correct > 3) {
            $errors[] = "Draft #$idx skipped: correct_index out of range.";
            continue;
        }
        $diff = in_array($q['difficulty'] ?? '', ['easy', 'medium', 'hard'], true) ? $q['difficulty'] : 'medium';
        $expl = (string) ($q['explanation'] ?? '');
        if (isset($flagged[$idx])) {
            $expl .= "\n\n[AI critique flagged this draft: " . $flagged[$idx] . "]";
        }

        $qid = db_exec(
            'INSERT INTO questions (subject_id, topic_id, type, difficulty, question_text, explanation, marks, status)
             VALUES (?,?,?,?,?,?,?,?)',
            [(int) $topic['subject_id'], $topicId, 'mcq', $diff, (string) $q['question'], $expl, 1, 'pending']
        );
        foreach (array_values($q['options']) as $i => $opt) {
            db_exec(
                'INSERT INTO question_options (question_id, label, option_text, is_correct, sort_order)
                 VALUES (?,?,?,?,?)',
                [$qid, chr(65 + $i), (string) $opt, $i === $correct ? 1 : 0, $i + 1]
            );
        }
        $inserted++;
    }

    return ['inserted' => $inserted, 'flagged' => count($flagged), 'errors' => $errors];
}

/** Second-pass AI critic. Asks the model to verify the answer & options. */
function critique_question(string $subjectSystem, array $draft): array
{
    $payload = json_encode([
        'question'      => $draft['question']      ?? '',
        'options'       => $draft['options']       ?? [],
        'correct_index' => $draft['correct_index'] ?? null,
        'explanation'   => $draft['explanation']   ?? '',
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

    $criticSystem = $subjectSystem . "\n\nYou are now acting as a strict, skeptical reviewer. Your job is to spot errors, ambiguities, and syllabus mismatches in a draft question. Never invent context; rely only on standard syllabus knowledge.";
    $criticUser = "Review this draft MCQ. Verify the maths/facts, that exactly one option is unambiguously correct, that distractors are plausible, and that the explanation is consistent.\n\nDraft:\n{$payload}\n\n"
        . "Reply ONLY with a JSON object: {\"ok\": true} if the draft is good, or {\"ok\": false, \"reason\": \"short reason\"} if not.";

    try {
        $resp = ai_chat($criticSystem, [['role' => 'user', 'content' => $criticUser]], 0.0);
    } catch (Throwable $e) {
        return ['ok' => false, 'reason' => 'critic call failed: ' . $e->getMessage()];
    }
    $data = parse_first_json_object($resp);
    if (!is_array($data)) {
        return ['ok' => false, 'reason' => 'critic returned unparseable JSON'];
    }
    return [
        'ok'     => (bool) ($data['ok'] ?? false),
        'reason' => (string) ($data['reason'] ?? 'unspecified'),
    ];
}

/**
 * Map a subject slug to a sensible (type, language) pair, used by the
 * "Apply defaults to all subjects" action so each subject gets a
 * tailored default prompt without manual editing.
 */
function subject_default_traits(string $slug): array
{
    return match ($slug) {
        'mathematics', 'add-maths'                            => ['math',       'English'],
        'physics', 'chemistry', 'biology'                     => ['science',    'English'],
        'sains'                                               => ['science',    'Bahasa Melayu'],
        'english'                                             => ['language',   'English'],
        'bahasa-melayu'                                       => ['language',   'Bahasa Melayu'],
        'bahasa-cina'                                         => ['language',   'Chinese'],
        'bahasa-tamil'                                        => ['language',   'Tamil'],
        'bahasa-arab'                                         => ['language',   'Arabic'],
        'sejarah'                                             => ['history',    'Bahasa Melayu'],
        'geografi'                                            => ['history',    'Bahasa Melayu'],
        'pendidikan-islam', 'tasawwur-islam', 'pqs', 'psi'    => ['religious',  'Bahasa Melayu'],
        'pendidikan-moral'                                    => ['religious',  'Bahasa Melayu'],
        'perakaunan', 'perniagaan', 'ekonomi'                 => ['commerce',   'Bahasa Melayu'],
        'sains-komputer'                                      => ['technology', 'English'],
        'kepintaran-buatan'                                   => ['technology', 'English'],
        'rbt'                                                 => ['technology', 'Bahasa Melayu'],
        'psv'                                                 => ['general',    'Bahasa Melayu'],
        default                                               => ['general',    'English'],
    };
}

/**
 * Apply the composed default prompt to every subject. Skips subjects that
 * already have a custom ai_prompt unless $force is true.
 *
 * @return array ['updated'=>int, 'kept'=>int]
 */
function apply_default_subject_prompts(bool $force = false): array
{
    $updated = 0;
    $kept    = 0;
    foreach (db_all('SELECT * FROM subjects') as $s) {
        if (!$force && trim((string) ($s['ai_prompt'] ?? '')) !== '') {
            $kept++;
            continue;
        }
        [$type, $lang] = subject_default_traits((string) $s['slug']);
        $row = array_merge($s, [
            'ai_subject_type' => $type,
            'ai_language'     => $lang,
            'ai_exam_board'   => $s['ai_exam_board'] ?: 'Malaysian SPM (KSSM)',
        ]);
        $prompt = subject_ai_default($row);
        db_exec(
            'UPDATE subjects SET ai_subject_type = ?, ai_exam_board = ?, ai_language = ?, ai_prompt = ? WHERE id = ?',
            [$type, $row['ai_exam_board'], $lang, $prompt, (int) $s['id']]
        );
        $updated++;
    }
    return ['updated' => $updated, 'kept' => $kept];
}

/** Generate skills for a topic and insert them. Returns counts. */
function generate_skills_for_topic(int $topicId, int $count): array
{
    $count = max(1, min(15, $count));
    $topic = fetch_topic_with_subject_ai($topicId);
    if (!$topic) {
        return ['inserted' => 0, 'errors' => ['Topic not found.']];
    }
    if (!ai_enabled()) {
        return ['inserted' => 0, 'errors' => ['AI key is not configured. Go to Admin → AI Settings.']];
    }
    $system = subject_ai_effective($topic);
    $userPrompt = "List {$count} concrete, testable skills students need to master the topic \"{$topic['name']}\" "
        . "(subject: {$topic['subject']}). Each skill should be specific enough to test in one question.\n\n"
        . ($topic['description'] ? "Topic description: {$topic['description']}\n" : '')
        . "Return ONLY a JSON array. Each element must be an object with these exact keys:\n"
        . "  - name (string, 3-80 chars, action-style e.g. \"Solve linear equations\")\n"
        . "  - difficulty (\"easy\", \"medium\" or \"hard\")\n"
        . "  - description (string, 1 sentence)\n"
        . "No prose before or after the JSON.";

    try {
        $raw = ai_chat($system, [['role' => 'user', 'content' => $userPrompt]], 0.3);
    } catch (Throwable $e) {
        return ['inserted' => 0, 'errors' => ['AI call failed: ' . $e->getMessage()]];
    }
    $skills = parse_questions_json($raw);
    if (!$skills) {
        $len = mb_strlen($raw);
        $head = mb_substr($raw, 0, 200);
        $tail = $len > 400 ? '… ' . mb_substr($raw, $len - 200) : '';
        $jsonErr = json_last_error_msg();
        return ['inserted' => 0, 'errors' => [
            "AI returned unparseable JSON ({$len} chars). json_decode said: {$jsonErr}",
            "Head: {$head}{$tail}",
            'Tip: if the output ends mid-text, raise Max output tokens in Admin → AI Settings.',
        ]];
    }

    $inserted = 0;
    $errors   = [];
    foreach ($skills as $i => $sk) {
        $name = trim((string) ($sk['name'] ?? ''));
        if ($name === '') {
            $errors[] = "Draft #$i skipped: empty name.";
            continue;
        }
        if (db_one('SELECT id FROM skills WHERE topic_id = ? AND name = ?', [$topicId, $name])) {
            continue; // dedupe
        }
        $diff = in_array($sk['difficulty'] ?? '', ['easy', 'medium', 'hard'], true) ? $sk['difficulty'] : 'medium';
        $desc = (string) ($sk['description'] ?? '');
        db_exec(
            'INSERT INTO skills (topic_id, name, difficulty, description) VALUES (?,?,?,?)',
            [$topicId, mb_substr($name, 0, 190), $diff, $desc]
        );
        $inserted++;
    }
    return ['inserted' => $inserted, 'errors' => $errors];
}

/** Strip markdown code fences (```json … ```) and surrounding chatter. */
function strip_code_fences(string $text): string
{
    $t = trim($text);
    // Strip leading ```json or ``` and trailing ```.
    $t = preg_replace('/^```(?:json|JSON)?\s*\n?/u', '', $t);
    $t = preg_replace('/\n?```\s*$/u', '', $t);
    return trim($t);
}

/**
 * Repair common LLM-JSON issues that make json_decode choke even when the
 * structure looks correct:
 *  - smart quotes / curly quotes → straight quotes
 *  - unicode minus / en-dash inside numbers → ascii hyphen
 *  - literal newline / tab / CR inside string values → \n / \t / \r
 *  - trailing commas before ] or }
 *  - BOM at the start
 */
function repair_llm_json(string $json): string
{
    // Strip UTF-8 BOM.
    if (str_starts_with($json, "\xEF\xBB\xBF")) {
        $json = substr($json, 3);
    }

    // Convert curly quotes outside-strings is unsafe to detect; do a blanket
    // replacement (curly quotes inside copy paragraphs are very rare in
    // generated MCQs and a straight quote is always safer for json_decode).
    $json = strtr($json, [
        "\u{201C}" => '"', "\u{201D}" => '"',
        "\u{2018}" => "'", "\u{2019}" => "'",
    ]);

    // Walk char-by-char so we only modify content INSIDE JSON strings.
    $out = '';
    $len = strlen($json);
    $inString = false;
    $escape   = false;
    for ($i = 0; $i < $len; $i++) {
        $ch = $json[$i];
        if ($inString) {
            if ($escape) {
                $out   .= $ch;
                $escape = false;
                continue;
            }
            if ($ch === '\\') {
                $out   .= $ch;
                $escape = true;
                continue;
            }
            if ($ch === '"') {
                $out      .= $ch;
                $inString  = false;
                continue;
            }
            // Inside a string: escape raw control chars that json_decode rejects.
            if ($ch === "\n") { $out .= '\\n'; continue; }
            if ($ch === "\r") { $out .= '\\r'; continue; }
            if ($ch === "\t") { $out .= '\\t'; continue; }
            if (ord($ch) < 0x20) { $out .= sprintf('\\u%04x', ord($ch)); continue; }
            $out .= $ch;
        } else {
            if ($ch === '"') {
                $inString = true;
            }
            $out .= $ch;
        }
    }

    // Strip trailing commas before ] or } (a very common LLM mistake).
    $out = preg_replace('/,\s*([\]\}])/u', '$1', $out);

    return $out;
}

/** Try every parse strategy in order. Returns the decoded array or null. */
function try_decode_json_array(string $candidate): ?array
{
    $d = json_decode($candidate, true);
    if (is_array($d)) {
        return $d;
    }
    $repaired = repair_llm_json($candidate);
    if ($repaired !== $candidate) {
        $d = json_decode($repaired, true);
        if (is_array($d)) {
            return $d;
        }
    }
    return null;
}

/** Extract the first JSON object from a free-form AI reply. */
function parse_first_json_object(string $text): ?array
{
    $cleaned = strip_code_fences($text);

    $direct = try_decode_json_array($cleaned);
    if ($direct !== null) {
        return $direct;
    }

    $start = strpos($cleaned, '{');
    $end   = strrpos($cleaned, '}');
    if ($start === false || $end === false || $end <= $start) {
        return null;
    }
    return try_decode_json_array(substr($cleaned, $start, $end - $start + 1));
}

/**
 * Extract a JSON array (of question / skill objects) from a free-form AI reply.
 * Tolerates markdown code fences, smart quotes, unescaped control chars,
 * trailing commas, and tries to salvage truncated output by trimming back to
 * the last complete object when the array is unterminated.
 */
function parse_questions_json(string $text): ?array
{
    $cleaned = strip_code_fences($text);

    $direct = try_decode_json_array($cleaned);
    if ($direct !== null) {
        return $direct;
    }

    $start = strpos($cleaned, '[');
    if ($start === false) {
        return null;
    }
    $end = strrpos($cleaned, ']');
    if ($end !== false && $end > $start) {
        $candidate = substr($cleaned, $start, $end - $start + 1);
        $data = try_decode_json_array($candidate);
        if ($data !== null) {
            return $data;
        }
    }

    // Truncated output: response ends mid-object. Walk back to the last `},`
    // and synthesise a closing `]` so we can at least keep the complete items.
    $tail = substr($cleaned, $start);
    $lastClose = strrpos($tail, '},');
    if ($lastClose === false) {
        return null;
    }
    $salvaged = substr($tail, 0, $lastClose + 1) . ']';
    return try_decode_json_array($salvaged);
}
