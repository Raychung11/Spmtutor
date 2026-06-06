<?php
/**
 * AI Karangan / Rumusan / Tatabahasa Marker.
 *
 * Wraps ai_chat() with language-aware SPM rubric prompts and returns
 * structured marking data: rubric breakdown, strengths, weaknesses,
 * suggestions, errors. Results are stored in essay_submissions for
 * student history and teacher review.
 */
declare(strict_types=1);

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/ai.php';
require_once __DIR__ . '/ai_questions.php';  // parse_first_json_object, strip_code_fences

/** Count words for both Latin and Cyrillic/CJK scripts (approx). */
function marker_word_count(string $text): int
{
    $text = trim($text);
    if ($text === '') return 0;
    // Split on whitespace; CJK chars without spaces handled by mb_strlen fallback.
    $parts = preg_split('/\s+/u', $text, -1, PREG_SPLIT_NO_EMPTY) ?: [];
    return count($parts);
}

/**
 * Mark a karangan (BM or English essay).
 *
 * @param string $submission The student's essay text.
 * @param string $prompt     The question / title.
 * @param string $language   'bm' or 'en'.
 * @return array See structure below.
 */
function mark_karangan(string $submission, string $prompt, string $language = 'bm'): array
{
    $submission = trim($submission);
    if ($submission === '') {
        return ['ok' => false, 'error' => 'Karangan kosong / Empty submission.'];
    }
    if (!ai_enabled()) {
        return ['ok' => false, 'error' => 'AI key is not configured. Set it in Admin → AI Settings.'];
    }

    $wordCount = marker_word_count($submission);
    $maxScore  = $language === 'en' ? 30 : 100;

    if ($language === 'en') {
        $system = "You are an experienced SPM 1119 English examiner. Mark the student's essay against the band descriptors for Continuous Writing (Paper 1, Section C — 50 marks for Continuous Writing in the new format; we'll scale your scoring to a 30-mark rubric for consistency).\n\n"
            . "Rubric (total 30 marks):\n"
            . "  - Content (out of 12): relevance, idea development, examples\n"
            . "  - Language (out of 10): grammar, vocabulary, sentence variety, spelling\n"
            . "  - Organisation (out of 5): structure, paragraphing, cohesion\n"
            . "  - Style (out of 3): tone, register, voice\n\n"
            . "Map the total to a band: 27-30 = Band 6, 23-26 = Band 5, 18-22 = Band 4, 13-17 = Band 3, 8-12 = Band 2, 0-7 = Band 1.\n"
            . "Identify specific grammar/spelling errors with the exact wrong phrase and a correction.";
    } else {
        $system = "Anda ialah pemeriksa SPM Bahasa Melayu (1103) yang berpengalaman. Periksa karangan murid berdasarkan rubrik Karangan Respons Terbuka (Bahagian B, 100 markah).\n\n"
            . "Rubrik (jumlah 100 markah):\n"
            . "  - Isi (30 markah): kerelevanan dengan tajuk, kelengkapan idea, contoh & bukti\n"
            . "  - Bahasa (30 markah): tatabahasa, ejaan, kosa kata, struktur ayat\n"
            . "  - Pengolahan (20 markah): pendahuluan-isi-penutup, penanda wacana, paragraf\n"
            . "  - Gaya Bahasa (20 markah): peribahasa, ungkapan menarik, kata bunga, suara penulis\n\n"
            . "Tahap: 80-100 cemerlang, 65-79 baik, 50-64 memuaskan, 30-49 lemah, <30 sangat lemah.\n"
            . "Kenal pasti kesalahan ejaan / imbuhan / kata / ayat dengan petikan asal dan pembetulan.";
    }

    $instr = $language === 'en' ? 'Reply with ONLY a JSON object matching this exact schema.' : 'Pulangkan respons sebagai SATU objek JSON sahaja mengikut skema ini dengan tepat.';

    $schema = <<<JSON
{
  "score": <integer total score>,
  "max_score": $maxScore,
  "band": "<string band label e.g. 'Band 5' or 'Cemerlang' or 'Baik'>",
  "rubric": {
    "<rubric_key>": { "score": <int>, "max": <int>, "comment": "<one line>" }
  },
  "strengths": ["<2-4 short bullet strings>"],
  "weaknesses": ["<2-4 short bullet strings>"],
  "suggestions": ["<2-4 actionable bullet strings>"],
  "errors": [
    { "original": "<exact phrase from essay>", "corrected": "<correction>", "rule": "<short rule>" }
  ]
}
JSON;

    $user = ($language === 'en'
        ? "Essay prompt: " . ($prompt !== '' ? $prompt : '(student did not provide a title)') . "\n\nWord count: $wordCount\n\nStudent's essay:\n\"\"\"\n$submission\n\"\"\"\n\n$instr\n\n$schema"
        : "Tajuk karangan: " . ($prompt !== '' ? $prompt : '(murid tidak nyatakan tajuk)') . "\n\nBilangan perkataan: $wordCount\n\nKarangan murid:\n\"\"\"\n$submission\n\"\"\"\n\n$instr\n\n$schema"
    );

    try {
        $raw = ai_chat($system, [['role' => 'user', 'content' => $user]], 0.3);
    } catch (Throwable $e) {
        return ['ok' => false, 'error' => 'AI call failed: ' . $e->getMessage()];
    }

    $data = parse_first_json_object($raw);
    if (!$data) {
        return ['ok' => false, 'error' => 'AI returned unparseable JSON. Head: ' . mb_substr($raw, 0, 200), 'raw' => $raw];
    }

    return [
        'ok'          => true,
        'word_count'  => $wordCount,
        'score'       => (int) ($data['score'] ?? 0),
        'max_score'   => (int) ($data['max_score'] ?? $maxScore),
        'band'        => (string) ($data['band'] ?? ''),
        'rubric'      => is_array($data['rubric'] ?? null) ? $data['rubric'] : [],
        'strengths'   => is_array($data['strengths'] ?? null) ? $data['strengths'] : [],
        'weaknesses'  => is_array($data['weaknesses'] ?? null) ? $data['weaknesses'] : [],
        'suggestions' => is_array($data['suggestions'] ?? null) ? $data['suggestions'] : [],
        'errors'      => is_array($data['errors'] ?? null) ? $data['errors'] : [],
        'raw'         => $raw,
    ];
}

/**
 * Mark a rumusan (BM summary). Requires the source passage so the AI can
 * verify isi tersurat / tersirat extraction.
 */
function mark_rumusan(string $submission, string $sourcePassage): array
{
    $submission = trim($submission);
    if ($submission === '') {
        return ['ok' => false, 'error' => 'Rumusan kosong.'];
    }
    if (!ai_enabled()) {
        return ['ok' => false, 'error' => 'AI key is not configured.'];
    }

    $wordCount = marker_word_count($submission);

    $system = "Anda ialah pemeriksa SPM Bahasa Melayu (1103) yang berpengalaman. Periksa rumusan murid berdasarkan rubrik Rumusan (Bahagian A, 30 markah).\n\n"
        . "Rubrik (jumlah 30 markah):\n"
        . "  - Isi tersurat (12 markah): 4 isi × 3 markah\n"
        . "  - Isi tersirat (8 markah): 2 isi × 4 markah\n"
        . "  - Bahasa & Olahan (10 markah): tatabahasa, ringkas, ayat tersendiri\n\n"
        . "Periksa setiap isi yang murid tulis terhadap petikan asal. Jika murid menyalin ayat secara langsung tanpa olahan, kurangkan markah bahasa.\n"
        . "Had perkataan ideal: 120 perkataan. Lebih daripada 130 atau kurang daripada 110 dikenakan penalti.";

    $schema = <<<JSON
{
  "score": <int>,
  "max_score": 30,
  "band": "<Cemerlang | Baik | Memuaskan | Lemah>",
  "rubric": {
    "isi_tersurat":  { "score": <0-12>, "max": 12, "comment": "<senarai isi tersurat yang ditemui>" },
    "isi_tersirat":  { "score": <0-8>,  "max": 8,  "comment": "<senarai isi tersirat yang ditemui>" },
    "bahasa_olahan": { "score": <0-10>, "max": 10, "comment": "<komen ringkas>" }
  },
  "strengths": ["<2-3 bullet>"],
  "weaknesses": ["<2-3 bullet>"],
  "suggestions": ["<2-3 bullet>"],
  "errors": []
}
JSON;

    $user = "Petikan asal:\n\"\"\"\n$sourcePassage\n\"\"\"\n\nRumusan murid (bilangan perkataan: $wordCount):\n\"\"\"\n$submission\n\"\"\"\n\nPulangkan SATU objek JSON sahaja:\n\n$schema";

    try {
        $raw = ai_chat($system, [['role' => 'user', 'content' => $user]], 0.2);
    } catch (Throwable $e) {
        return ['ok' => false, 'error' => 'AI call failed: ' . $e->getMessage()];
    }

    $data = parse_first_json_object($raw);
    if (!$data) {
        return ['ok' => false, 'error' => 'AI returned unparseable JSON. Head: ' . mb_substr($raw, 0, 200), 'raw' => $raw];
    }

    return [
        'ok'          => true,
        'word_count'  => $wordCount,
        'score'       => (int) ($data['score'] ?? 0),
        'max_score'   => (int) ($data['max_score'] ?? 30),
        'band'        => (string) ($data['band'] ?? ''),
        'rubric'      => is_array($data['rubric'] ?? null) ? $data['rubric'] : [],
        'strengths'   => is_array($data['strengths'] ?? null) ? $data['strengths'] : [],
        'weaknesses'  => is_array($data['weaknesses'] ?? null) ? $data['weaknesses'] : [],
        'suggestions' => is_array($data['suggestions'] ?? null) ? $data['suggestions'] : [],
        'errors'      => is_array($data['errors'] ?? null) ? $data['errors'] : [],
        'raw'         => $raw,
    ];
}

/**
 * Check a sentence / paragraph for tatabahasa errors (BM or English).
 */
function mark_tatabahasa(string $submission, string $language = 'bm'): array
{
    $submission = trim($submission);
    if ($submission === '') {
        return ['ok' => false, 'error' => 'Empty submission.'];
    }
    if (!ai_enabled()) {
        return ['ok' => false, 'error' => 'AI key is not configured.'];
    }

    if ($language === 'en') {
        $system = "You are a strict English grammar teacher. Check the student's text for spelling, grammar, punctuation, and word-choice errors. List each error with the exact wrong phrase, the correction, and a one-line rule. Also produce a fully corrected version of the text.\n\nBe thorough but only flag genuine errors — do not rewrite for style unless the original is grammatically wrong. If there are no errors, return an empty 'errors' array and a 'verdict' of 'No errors found.'";
    } else {
        $system = "Anda ialah guru tatabahasa Bahasa Melayu yang teliti. Semak teks murid untuk kesalahan ejaan, imbuhan, kata, struktur ayat, dan tanda baca. Senaraikan setiap kesalahan dengan petikan asal, pembetulan, dan satu baris peraturan. Hasilkan juga versi yang telah dibetulkan.\n\nFokus pada kesalahan sebenar sahaja, bukan gaya bahasa. Jika tiada kesalahan, pulangkan 'errors' kosong dan 'verdict' 'Tiada kesalahan ditemui.'";
    }

    $schema = <<<JSON
{
  "verdict": "<one-line summary>",
  "corrected_text": "<the fully corrected version>",
  "errors": [
    { "original": "<exact wrong phrase>", "corrected": "<correction>", "type": "<ejaan | imbuhan | kata | ayat | tanda_baca | grammar | spelling | punctuation>", "rule": "<one-line rule>" }
  ]
}
JSON;

    $user = ($language === 'en' ? "Text:\n\"\"\"\n" : "Teks:\n\"\"\"\n") . $submission . "\n\"\"\"\n\n"
        . ($language === 'en' ? 'Reply with ONLY this JSON:' : 'Pulangkan SATU objek JSON sahaja:') . "\n\n$schema";

    try {
        $raw = ai_chat($system, [['role' => 'user', 'content' => $user]], 0.1);
    } catch (Throwable $e) {
        return ['ok' => false, 'error' => 'AI call failed: ' . $e->getMessage()];
    }

    $data = parse_first_json_object($raw);
    if (!$data) {
        return ['ok' => false, 'error' => 'AI returned unparseable JSON.', 'raw' => $raw];
    }

    $errors = is_array($data['errors'] ?? null) ? $data['errors'] : [];

    return [
        'ok'             => true,
        'word_count'     => marker_word_count($submission),
        'verdict'        => (string) ($data['verdict'] ?? ''),
        'corrected_text' => (string) ($data['corrected_text'] ?? ''),
        'errors'         => $errors,
        // For tatabahasa, "score" = % of words that were not flagged (rough).
        'score'          => max(0, marker_word_count($submission) - count($errors)),
        'max_score'      => marker_word_count($submission),
        'raw'            => $raw,
    ];
}

/**
 * Suggest stronger word/phrase swaps for a paragraph using the
 * subject's reference bank (english_vocabulary / bm_peribahasa) as
 * preferred recommendations.
 *
 * Returns a list of upgrade objects: original, suggestion, reason,
 * category. Stored into essay_submissions.errors_json so the Writing
 * Marker can render them with the same upgrade UI.
 */
function upgrade_vocabulary(string $submission, string $language = 'en'): array
{
    $submission = trim($submission);
    if ($submission === '') {
        return ['ok' => false, 'error' => 'Empty submission.'];
    }
    if (!ai_enabled()) {
        return ['ok' => false, 'error' => 'AI key is not configured.'];
    }

    // Pull the reference bank so the model can preferentially recommend from it.
    $bank = [];
    if ($language === 'en') {
        try {
            $rows = db_all(
                "SELECT word, part_of_speech, meaning FROM english_vocabulary
                 WHERE status = 'active' ORDER BY level DESC, word LIMIT 80"
            );
            foreach ($rows as $r) {
                $bank[] = $r['word'] . ' (' . $r['part_of_speech'] . ') — ' . mb_substr((string) $r['meaning'], 0, 80);
            }
        } catch (Throwable $e) {}
    } else {
        try {
            $rows = db_all(
                "SELECT expression, meaning FROM bm_peribahasa
                 WHERE status = 'active' ORDER BY id LIMIT 50"
            );
            foreach ($rows as $r) {
                $bank[] = $r['expression'] . ' — ' . mb_substr((string) $r['meaning'], 0, 80);
            }
        } catch (Throwable $e) {}
    }
    $bankStr = $bank ? implode("\n  ", $bank) : '(no bank available — use general SPM-level vocabulary)';

    if ($language === 'en') {
        $system = "You are an expert SPM 1119 English tutor helping a student elevate the vocabulary in their writing. "
            . "Scan the student's paragraph for weak / overused words and short phrases (e.g. 'very good', 'a lot of', 'really nice') "
            . "and propose 5-10 specific swaps using the band-5 vocabulary bank below WHERE APPROPRIATE. "
            . "If the bank doesn't contain a fitting replacement, you may suggest other strong SPM-appropriate words.\n\n"
            . "Vocabulary bank (suggest these by preference):\n  " . $bankStr . "\n\n"
            . "Rules:\n"
            . "  - Only suggest swaps that genuinely improve clarity, precision or register.\n"
            . "  - Do NOT rewrite the whole paragraph — surgical swaps only.\n"
            . "  - Keep each 'original' to the exact phrase as it appears in the student's text (1-5 words).\n"
            . "  - Mark the source: 'bank' if from the bank above, 'general' if a different recommendation.\n"
            . "  - If there are no genuine improvements to make, return an empty 'upgrades' array.";
    } else {
        $system = "Anda ialah tutor SPM Bahasa Melayu (1103) yang membantu murid menaikkan tahap kosa kata dalam karangan. "
            . "Imbas perenggan murid untuk perkataan/frasa biasa-biasa (e.g. 'sangat baik', 'sangat penting') "
            . "dan cadangkan 5-10 pertukaran spesifik. Jika sesuai, masukkan peribahasa dari bank di bawah "
            . "untuk menggantikan ayat biasa dengan ungkapan menarik (cara biasa SPM).\n\n"
            . "Bank peribahasa (cadangkan ini secara keutamaan):\n  " . $bankStr . "\n\n"
            . "Peraturan:\n"
            . "  - Cadangkan pertukaran yang benar-benar mengangkat darjah penulisan.\n"
            . "  - JANGAN tulis semula keseluruhan perenggan — pertukaran berfokus sahaja.\n"
            . "  - 'original' mestilah frasa yang sama persis dalam karangan murid (1-5 patah perkataan).\n"
            . "  - Tandai sumber: 'bank' jika dari bank di atas, 'general' jika cadangan lain.";
    }

    $schema = <<<JSON
{
  "upgrades": [
    {
      "original": "<exact phrase from the student's text>",
      "suggestion": "<your replacement>",
      "reason": "<one short line: why this is better>",
      "source": "bank | general",
      "category": "vocabulary | peribahasa | phrase | intensifier"
    }
  ],
  "overall_comment": "<one short paragraph on the vocabulary quality>"
}
JSON;

    $user = ($language === 'en' ? "Student's paragraph:\n\"\"\"\n" : "Karangan murid:\n\"\"\"\n") . $submission . "\n\"\"\"\n\n"
        . ($language === 'en' ? 'Reply with ONLY a JSON object matching this schema:' : 'Pulangkan SATU objek JSON sahaja:') . "\n\n$schema";

    try {
        $raw = ai_chat($system, [['role' => 'user', 'content' => $user]], 0.3);
    } catch (Throwable $e) {
        return ['ok' => false, 'error' => 'AI call failed: ' . $e->getMessage()];
    }

    $data = parse_first_json_object($raw);
    if (!$data) {
        return ['ok' => false, 'error' => 'AI returned unparseable JSON.', 'raw' => $raw];
    }

    $upgrades = is_array($data['upgrades'] ?? null) ? $data['upgrades'] : [];
    return [
        'ok'              => true,
        'word_count'      => marker_word_count($submission),
        'score'           => count($upgrades),
        'max_score'       => 10,
        'band'            => count($upgrades) === 0
            ? ($language === 'en' ? 'No upgrades needed' : 'Sudah baik')
            : ($language === 'en' ? count($upgrades) . ' suggested upgrades' : count($upgrades) . ' cadangan'),
        'upgrades'        => $upgrades,
        // Reuse errors_json slot so existing renderer + storage works.
        'errors'          => array_map(fn($u) => [
            'original'  => (string) ($u['original']   ?? ''),
            'corrected' => (string) ($u['suggestion'] ?? ''),
            'type'      => (string) ($u['category']   ?? 'vocabulary'),
            'rule'      => (string) ($u['reason']     ?? ''),
        ], $upgrades),
        'overall_comment' => (string) ($data['overall_comment'] ?? ''),
        'raw'             => $raw,
    ];
}

/**
 * Mark a prompt-engineering attempt — grades the student's prompt against
 * the 7-criterion rubric used in the Asas Kepintaran Buatan syllabus
 * (Clarity, Role, Context, Constraints, Output Format, Examples,
 * Robustness). Returns a rewritten "improved prompt" plus anti-patterns
 * spotted. $taskContext (optional) describes what the prompt is meant to
 * achieve so the AI can judge fit-for-purpose.
 */
function mark_prompt(string $submission, string $taskContext = '', string $language = 'en'): array
{
    $submission = trim($submission);
    if ($submission === '') {
        return ['ok' => false, 'error' => $language === 'bm' ? 'Prompt kosong.' : 'Empty prompt.'];
    }
    if (!ai_enabled()) {
        return ['ok' => false, 'error' => 'AI key is not configured. Set it in Admin → AI Settings.'];
    }

    $wordCount = marker_word_count($submission);

    if ($language === 'bm') {
        $system = "Anda ialah jurutera prompt berpengalaman yang menilai prompt pelajar SPM bagi mata pelajaran Asas Kepintaran Buatan. "
            . "Periksa prompt mengikut amalan terbaik LLM moden (Claude, GPT, Gemini).\n\n"
            . "Rubrik (jumlah 100 markah):\n"
            . "  - Kejelasan (20m): Tugas dinyatakan dengan jelas tanpa kekaburan?\n"
            . "  - Peranan/Persona (15m): AI diberi peranan yang sesuai (cth. 'Anda ialah tutor SPM')?\n"
            . "  - Konteks (15m): Latar belakang mencukupi diberi?\n"
            . "  - Kekangan (15m): Had panjang, gaya, format ditentukan?\n"
            . "  - Format Output (15m): Struktur output dinyatakan (JSON, senarai, perenggan)?\n"
            . "  - Contoh (10m): Contoh few-shot atau template diberi?\n"
            . "  - Ketahanan (10m): Garis panduan menjauhi halusinasi atau jawapan tidak relevan?\n\n"
            . "Tahap: 85-100 cemerlang, 70-84 baik, 50-69 memuaskan, <50 perlu perbaikan.\n"
            . "Kenal pasti anti-corak (vague language, instruksi bercanggah, kurang contoh). "
            . "Tulis SATU versi prompt yang ditambah baik secara menyeluruh.";
    } else {
        $system = "You are an experienced prompt engineer evaluating an SPM student's prompt for the Asas Kepintaran Buatan subject. "
            . "Check the prompt against modern LLM best practices (Claude, GPT, Gemini).\n\n"
            . "Rubric (total 100 marks):\n"
            . "  - Clarity (20m): Is the task clearly stated without ambiguity?\n"
            . "  - Role/Persona (15m): Is the AI given a suitable role (e.g. 'You are an SPM tutor')?\n"
            . "  - Context (15m): Is enough background information provided?\n"
            . "  - Constraints (15m): Are length, style, and format limits specified?\n"
            . "  - Output Format (15m): Is the expected output structure clearly defined (JSON, list, paragraph)?\n"
            . "  - Examples (10m): Are few-shot examples or templates provided?\n"
            . "  - Robustness (10m): Are there guards against hallucination or off-topic answers?\n\n"
            . "Bands: 85-100 Excellent, 70-84 Good, 50-69 Satisfactory, <50 Needs Improvement.\n"
            . "Identify anti-patterns (vague language, conflicting instructions, missing examples). "
            . "Write ONE thoroughly improved version of the prompt.";
    }

    $instr = $language === 'bm'
        ? 'Pulangkan respons sebagai SATU objek JSON sahaja mengikut skema ini dengan tepat.'
        : 'Reply with ONLY a JSON object matching this exact schema.';

    $schema = <<<JSON
{
  "score": <integer total score>,
  "max_score": 100,
  "band": "<Excellent | Good | Satisfactory | Needs Improvement>",
  "rubric": {
    "clarity":       { "score": <0-20>, "max": 20, "comment": "<one line>" },
    "role":          { "score": <0-15>, "max": 15, "comment": "<one line>" },
    "context":       { "score": <0-15>, "max": 15, "comment": "<one line>" },
    "constraints":   { "score": <0-15>, "max": 15, "comment": "<one line>" },
    "output_format": { "score": <0-15>, "max": 15, "comment": "<one line>" },
    "examples":      { "score": <0-10>, "max": 10, "comment": "<one line>" },
    "robustness":    { "score": <0-10>, "max": 10, "comment": "<one line>" }
  },
  "strengths":  ["<2-4 short bullet strings>"],
  "weaknesses": ["<2-4 short bullet strings>"],
  "suggestions":["<2-4 actionable bullet strings>"],
  "anti_patterns": [
    { "original": "<exact phrase from the student's prompt>", "issue": "<short description of the issue>" }
  ],
  "improved_prompt": "<a fully rewritten, production-grade version of the student's prompt>"
}
JSON;

    $ctxLine = $taskContext !== ''
        ? ($language === 'bm' ? "Tujuan prompt: $taskContext\n\n" : "Prompt purpose: $taskContext\n\n")
        : '';

    $user = $ctxLine
        . ($language === 'bm' ? "Bilangan perkataan: $wordCount\n\nPrompt murid:\n\"\"\"\n" : "Word count: $wordCount\n\nStudent's prompt:\n\"\"\"\n")
        . $submission
        . "\n\"\"\"\n\n$instr\n\n$schema";

    try {
        $raw = ai_chat($system, [['role' => 'user', 'content' => $user]], 0.3);
    } catch (Throwable $e) {
        return ['ok' => false, 'error' => 'AI call failed: ' . $e->getMessage()];
    }

    $data = parse_first_json_object($raw);
    if (!$data) {
        return ['ok' => false, 'error' => 'AI returned unparseable JSON. Head: ' . mb_substr($raw, 0, 200), 'raw' => $raw];
    }

    $improved = (string) ($data['improved_prompt'] ?? '');
    $anti     = is_array($data['anti_patterns'] ?? null) ? $data['anti_patterns'] : [];

    // Pack anti-patterns into the standard errors[] shape so the existing
    // renderer + DB column (errors_json) handle them, and put the improved
    // prompt at the front as a special row with type='improved_prompt'.
    $errors = [];
    if ($improved !== '') {
        $errors[] = [
            'original'  => $language === 'bm' ? 'Prompt asal anda' : 'Your original prompt',
            'corrected' => $improved,
            'type'      => 'improved_prompt',
            'rule'      => $language === 'bm' ? 'Versi yang ditambah baik oleh AI' : 'AI-rewritten production version',
        ];
    }
    foreach ($anti as $p) {
        $errors[] = [
            'original'  => (string) ($p['original'] ?? ''),
            'corrected' => '',
            'type'      => 'anti_pattern',
            'rule'      => (string) ($p['issue'] ?? ''),
        ];
    }

    return [
        'ok'              => true,
        'word_count'      => $wordCount,
        'score'           => (int) ($data['score'] ?? 0),
        'max_score'       => (int) ($data['max_score'] ?? 100),
        'band'            => (string) ($data['band'] ?? ''),
        'rubric'          => is_array($data['rubric'] ?? null) ? $data['rubric'] : [],
        'strengths'       => is_array($data['strengths'] ?? null) ? $data['strengths'] : [],
        'weaknesses'      => is_array($data['weaknesses'] ?? null) ? $data['weaknesses'] : [],
        'suggestions'     => is_array($data['suggestions'] ?? null) ? $data['suggestions'] : [],
        'errors'          => $errors,
        'improved_prompt' => $improved,
        'anti_patterns'   => $anti,
        'raw'             => $raw,
    ];
}

/** Persist a submission + AI result into essay_submissions. */
function save_submission(int $userId, array $params, array $result): int
{
    $rubric = isset($result['rubric'])      ? json_encode($result['rubric'],      JSON_UNESCAPED_UNICODE) : null;
    $str    = isset($result['strengths'])   ? json_encode($result['strengths'],   JSON_UNESCAPED_UNICODE) : null;
    $wk     = isset($result['weaknesses'])  ? json_encode($result['weaknesses'],  JSON_UNESCAPED_UNICODE) : null;
    $sug    = isset($result['suggestions']) ? json_encode($result['suggestions'], JSON_UNESCAPED_UNICODE) : null;
    $err    = isset($result['errors'])      ? json_encode($result['errors'],      JSON_UNESCAPED_UNICODE) : null;
    $okMark = !empty($result['ok']);

    return db_exec(
        'INSERT INTO essay_submissions
            (user_id, subject_id, task_type, language, prompt, source_passage, submission,
             word_count, score, max_score, band,
             rubric_json, strengths_json, weaknesses_json, suggestions_json, errors_json,
             ai_raw, status, error_message, marked_at)
         VALUES (?,?,?,?,?,?,?, ?,?,?,?, ?,?,?,?,?, ?,?,?,NOW())',
        [
            $userId,
            $params['subject_id'] ?? null,
            $params['task_type'],
            $params['language'],
            $params['prompt'] ?? null,
            $params['source_passage'] ?? null,
            $params['submission'],
            (int) ($result['word_count'] ?? 0),
            $okMark && isset($result['score'])    ? (int) $result['score']    : null,
            $okMark && isset($result['max_score']) ? (int) $result['max_score'] : null,
            $result['band'] ?? null,
            $rubric, $str, $wk, $sug, $err,
            $result['raw'] ?? null,
            $okMark ? 'marked' : 'failed',
            $okMark ? null : ($result['error'] ?? null),
        ]
    );
}
