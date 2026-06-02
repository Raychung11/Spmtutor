<?php
/**
 * Registry of student-facing reference libraries.
 *
 * Each entry maps a seeded reference table (physics_formulas,
 * chemistry_formulas, bm_peribahasa, etc.) to the display + search
 * + flashcard metadata that student/library.php and student/flashcards.php
 * use to render it. Keeping all the table-shape differences here keeps
 * the page templates simple — they just consume this metadata.
 *
 * Each entry shape:
 *   slug         (string)  URL slug for the library
 *   table        (string)  Source MySQL table
 *   subject_slug (string)  Owning subject (for filter chips)
 *   label        (string)  Human-readable name
 *   description  (string)  One-line description
 *   language     (string)  Display direction hint: 'en' | 'ar' (RTL) | 'zh' | 'ta'
 *   card         (array)   How to render a card:
 *                          title, expression?, subtitle?, body?
 *   flashcard    (array)   front, back, back_extra?
 *   search_cols  (array)   Columns to LIKE-search against
 *   tag_col      (string)  Column used as a chip/filter (e.g. category, type)
 */
declare(strict_types=1);

function reference_libraries(): array
{
    return [
        // -------- Sciences --------
        [
            'slug' => 'physics-formulas', 'table' => 'physics_formulas',
            'subject_slug' => 'physics', 'label' => 'Physics Formulas',
            'description' => 'Equations across kinematics, dynamics, electricity, waves, nuclear.',
            'language' => 'en',
            'card' => ['title' => 'formula_name', 'expression' => 'formula', 'subtitle' => 'topic_label', 'body' => 'description'],
            'flashcard' => ['front' => 'formula_name', 'back' => 'formula', 'back_extra' => 'description'],
            'search_cols' => ['formula_name', 'formula', 'description'],
            'tag_col' => 'topic_label',
        ],
        [
            'slug' => 'chemistry-formulas', 'table' => 'chemistry_formulas',
            'subject_slug' => 'chemistry', 'label' => 'Chemistry Formulas',
            'description' => 'Persamaan dan formula kimia merentas struktur, bahan, larutan, ikatan.',
            'language' => 'en',
            'card' => ['title' => 'formula_name', 'expression' => 'formula', 'subtitle' => 'topic_label', 'body' => 'description'],
            'flashcard' => ['front' => 'formula_name', 'back' => 'formula', 'back_extra' => 'description'],
            'search_cols' => ['formula_name', 'formula', 'description'],
            'tag_col' => 'topic_label',
        ],
        [
            'slug' => 'biology-diagrams', 'table' => 'biology_diagrams',
            'subject_slug' => 'biology', 'label' => 'Biology Diagrams',
            'description' => 'Labelled diagrams — animal/plant cell, heart, digestive system, nephron, monohybrid cross.',
            'language' => 'en',
            'card' => ['title' => 'title', 'subtitle' => 'topic_label', 'body' => 'description', 'expression' => 'labels'],
            'flashcard' => ['front' => 'title', 'back' => 'labels', 'back_extra' => 'description'],
            'search_cols' => ['title', 'description', 'labels'],
            'tag_col' => 'topic_label',
        ],
        [
            'slug' => 'biology-flashcards', 'table' => 'biology_flashcards',
            'subject_slug' => 'biology', 'label' => 'Biology Flashcards',
            'description' => 'Q/A recall cards across cell biology, genetics, ecology, human body.',
            'language' => 'en',
            'card' => ['title' => 'question', 'body' => 'answer', 'subtitle' => 'topic_label'],
            'flashcard' => ['front' => 'question', 'back' => 'answer', 'back_extra' => 'hint'],
            'search_cols' => ['question', 'answer', 'hint'],
            'tag_col' => 'difficulty',
        ],
        [
            'slug' => 'sains-concepts', 'table' => 'sains_concepts',
            'subject_slug' => 'sains', 'label' => 'Sains Konsep & Formula',
            'description' => 'Istilah dan formula merentas biologi/kimia/fizik untuk aliran Sastera.',
            'language' => 'en',
            'card' => ['title' => 'name', 'expression' => 'formula', 'body' => 'definition', 'subtitle' => 'category'],
            'flashcard' => ['front' => 'name', 'back' => 'definition', 'back_extra' => 'example'],
            'search_cols' => ['name', 'definition', 'formula'],
            'tag_col' => 'category',
        ],

        // -------- Languages --------
        [
            'slug' => 'bm-peribahasa', 'table' => 'bm_peribahasa',
            'subject_slug' => 'bahasa-melayu', 'label' => 'BM Peribahasa & Simpulan Bahasa',
            'description' => 'Peribahasa, simpulan bahasa, bidalan, pepatah, cogan kata.',
            'language' => 'en',
            'card' => ['title' => 'expression', 'body' => 'meaning', 'subtitle' => 'type'],
            'flashcard' => ['front' => 'expression', 'back' => 'meaning', 'back_extra' => 'example'],
            'search_cols' => ['expression', 'meaning'],
            'tag_col' => 'type',
        ],
        [
            'slug' => 'english-vocab', 'table' => 'english_vocabulary',
            'subject_slug' => 'english', 'label' => 'English Vocabulary',
            'description' => 'SPM-level words with definitions, examples, synonyms and antonyms.',
            'language' => 'en',
            'card' => ['title' => 'word', 'subtitle' => 'part_of_speech', 'body' => 'meaning'],
            'flashcard' => ['front' => 'word', 'back' => 'meaning', 'back_extra' => 'example'],
            'search_cols' => ['word', 'meaning', 'synonyms'],
            'tag_col' => 'level',
        ],
        [
            'slug' => 'english-idioms', 'table' => 'english_idioms',
            'subject_slug' => 'english', 'label' => 'English Idioms & Phrasal Verbs',
            'description' => 'Common SPM idioms and phrasal verbs with usage examples.',
            'language' => 'en',
            'card' => ['title' => 'expression', 'subtitle' => 'type', 'body' => 'meaning'],
            'flashcard' => ['front' => 'expression', 'back' => 'meaning', 'back_extra' => 'example'],
            'search_cols' => ['expression', 'meaning'],
            'tag_col' => 'type',
        ],
        [
            'slug' => 'english-essays', 'table' => 'english_writing_samples',
            'subject_slug' => 'english', 'label' => 'English Model Essays',
            'description' => 'Band 4–5 model essays with examiner notes — argumentative, narrative, speech, summary.',
            'language' => 'en',
            'card' => ['title' => 'prompt', 'subtitle' => 'band', 'body' => 'notes'],
            'flashcard' => null,  // essays are too long for flashcard mode
            'search_cols' => ['prompt', 'notes', 'model_essay'],
            'tag_col' => 'category',
        ],
        [
            'slug' => 'bc-references', 'table' => 'bc_references',
            'subject_slug' => 'bahasa-cina', 'label' => 'Bahasa Cina 成语 & Wenyan',
            'description' => '成语 chengyu, wenyan function words, tokoh sasterawan dan karya klasik.',
            'language' => 'zh',
            'card' => ['title' => 'expression', 'expression' => 'pinyin', 'body' => 'meaning_bm', 'subtitle' => 'type'],
            'flashcard' => ['front' => 'expression', 'back' => 'meaning_bm', 'back_extra' => 'example'],
            'search_cols' => ['expression', 'pinyin', 'meaning_bm'],
            'tag_col' => 'type',
        ],
        [
            'slug' => 'bt-references', 'table' => 'bt_references',
            'subject_slug' => 'bahasa-tamil', 'label' => 'Bahasa Tamil Thirukkural & Pazhamozhi',
            'description' => 'திருக்குறள் kural, பழமொழி proverbs, tokoh klasik dan moden Tamil.',
            'language' => 'ta',
            'card' => ['title' => 'expression', 'expression' => 'transliteration', 'body' => 'meaning_bm', 'subtitle' => 'type'],
            'flashcard' => ['front' => 'expression', 'back' => 'meaning_bm', 'back_extra' => 'example'],
            'search_cols' => ['expression', 'transliteration', 'meaning_bm'],
            'tag_col' => 'type',
        ],
        [
            'slug' => 'ba-references', 'table' => 'ba_references',
            'subject_slug' => 'bahasa-arab', 'label' => 'Bahasa Arab أمثال & Nahu',
            'description' => 'أمثال (peribahasa Arab), istilah nahu, mufradat tematik, tokoh sasterawan.',
            'language' => 'ar',
            'card' => ['title' => 'expression', 'expression' => 'transliteration', 'body' => 'meaning_bm', 'subtitle' => 'type'],
            'flashcard' => ['front' => 'expression', 'back' => 'meaning_bm', 'back_extra' => 'example'],
            'search_cols' => ['expression', 'transliteration', 'meaning_bm'],
            'tag_col' => 'type',
        ],

        // -------- Humanities --------
        [
            'slug' => 'sejarah-timeline', 'table' => 'sejarah_timeline',
            'subject_slug' => 'sejarah', 'label' => 'Sejarah Timeline',
            'description' => 'Peristiwa penting dari Pengasasan Melaka (1400) hingga Wawasan 2020 (1991).',
            'language' => 'en',
            'card' => ['title' => 'title', 'expression' => 'year_label', 'body' => 'description', 'subtitle' => 'era'],
            'flashcard' => ['front' => 'title', 'back' => 'year_label', 'back_extra' => 'description'],
            'search_cols' => ['title', 'description', 'year_label'],
            'tag_col' => 'era',
        ],
        [
            'slug' => 'geografi-locations', 'table' => 'geografi_locations',
            'subject_slug' => 'geografi', 'label' => 'Geografi Locations',
            'description' => 'Banjaran, sungai, tasik, plat tektonik, zon iklim, petempatan utama.',
            'language' => 'en',
            'card' => ['title' => 'name', 'subtitle' => 'category', 'body' => 'description'],
            'flashcard' => ['front' => 'name', 'back' => 'description', 'back_extra' => 'significance'],
            'search_cols' => ['name', 'description', 'significance'],
            'tag_col' => 'category',
        ],
        [
            'slug' => 'islam-ayat-hadis', 'table' => 'islam_ayat_hadis',
            'subject_slug' => 'pendidikan-islam', 'label' => 'Pendidikan Islam Ayat & Hadis',
            'description' => 'Ayat Al-Quran, hadis dan doa dengan teks Arab, transliterasi dan terjemahan.',
            'language' => 'ar',
            'card' => ['title' => 'reference', 'expression' => 'arabic_text', 'body' => 'translation', 'subtitle' => 'type'],
            'flashcard' => ['front' => 'arabic_text', 'back' => 'translation', 'back_extra' => 'transliteration'],
            'search_cols' => ['reference', 'translation', 'transliteration', 'theme'],
            'tag_col' => 'type',
        ],
        [
            'slug' => 'moral-values', 'table' => 'moral_values',
            'subject_slug' => 'pendidikan-moral', 'label' => 'Pendidikan Moral Nilai Utama',
            'description' => '18 Nilai Utama KSSM dengan takrifan, contoh dan kata kunci.',
            'language' => 'en',
            'card' => ['title' => 'value_name', 'subtitle' => 'category', 'body' => 'definition'],
            'flashcard' => ['front' => 'value_name', 'back' => 'definition', 'back_extra' => 'example'],
            'search_cols' => ['value_name', 'definition', 'keywords'],
            'tag_col' => 'category',
        ],

        // -------- Commerce --------
        [
            'slug' => 'perakaunan-formulas', 'table' => 'perakaunan_formulas',
            'subject_slug' => 'perakaunan', 'label' => 'Perakaunan Formulas & Nisbah',
            'description' => 'Persamaan perakaunan, susut nilai, nisbah kewangan, TPM dan margin caruman.',
            'language' => 'en',
            'card' => ['title' => 'formula_name', 'expression' => 'formula', 'subtitle' => 'category', 'body' => 'description'],
            'flashcard' => ['front' => 'formula_name', 'back' => 'formula', 'back_extra' => 'description'],
            'search_cols' => ['formula_name', 'formula', 'description'],
            'tag_col' => 'category',
        ],
        [
            'slug' => 'perniagaan-terms', 'table' => 'perniagaan_terms',
            'subject_slug' => 'perniagaan', 'label' => 'Perniagaan Istilah',
            'description' => 'Istilah perniagaan: pemilikan, trend, organisasi, kewangan, usahawan.',
            'language' => 'en',
            'card' => ['title' => 'term', 'subtitle' => 'category', 'body' => 'definition'],
            'flashcard' => ['front' => 'term', 'back' => 'definition', 'back_extra' => 'example'],
            'search_cols' => ['term', 'definition'],
            'tag_col' => 'category',
        ],
        [
            'slug' => 'ekonomi-concepts', 'table' => 'ekonomi_concepts',
            'subject_slug' => 'ekonomi', 'label' => 'Ekonomi Konsep & Formula',
            'description' => 'Istilah dan formula ekonomi: permintaan, keanjalan, KDNK, dasar fiskal.',
            'language' => 'en',
            'card' => ['title' => 'name', 'expression' => 'formula', 'body' => 'definition', 'subtitle' => 'category'],
            'flashcard' => ['front' => 'name', 'back' => 'definition', 'back_extra' => 'example'],
            'search_cols' => ['name', 'definition', 'formula'],
            'tag_col' => 'category',
        ],

        // -------- Technology --------
        [
            'slug' => 'cs-concepts', 'table' => 'cs_concepts',
            'subject_slug' => 'sains-komputer', 'label' => 'Sains Komputer Code & Concepts',
            'description' => 'Code snippets dalam Python, SQL, HTML/CSS/JS, PHP + istilah dan get logik.',
            'language' => 'en',
            'card' => ['title' => 'name', 'expression' => 'code_snippet', 'body' => 'definition', 'subtitle' => 'language'],
            'flashcard' => ['front' => 'name', 'back' => 'definition', 'back_extra' => 'code_snippet'],
            'search_cols' => ['name', 'definition', 'code_snippet'],
            'tag_col' => 'language',
        ],
        [
            'slug' => 'rbt-concepts', 'table' => 'rbt_concepts',
            'subject_slug' => 'rbt', 'label' => 'RBT / Reka Cipta Konsep',
            'description' => 'Konsep reka cipta: elemen reka bentuk, harta intelek, prototaip, pemasaran 4P.',
            'language' => 'en',
            'card' => ['title' => 'name', 'subtitle' => 'category', 'body' => 'definition'],
            'flashcard' => ['front' => 'name', 'back' => 'definition', 'back_extra' => 'example'],
            'search_cols' => ['name', 'definition'],
            'tag_col' => 'category',
        ],
        [
            'slug' => 'psv-references', 'table' => 'psv_references',
            'subject_slug' => 'psv', 'label' => 'PSV Rujukan Seni',
            'description' => 'Tokoh seni tempatan & dunia, teknik (impasto/glazing/casting), motif dan istilah.',
            'language' => 'en',
            'card' => ['title' => 'name', 'subtitle' => 'category', 'body' => 'description'],
            'flashcard' => ['front' => 'name', 'back' => 'description', 'back_extra' => 'example'],
            'search_cols' => ['name', 'description', 'origin'],
            'tag_col' => 'category',
        ],
    ];
}

/** Pick one library entry by slug, or null. */
function reference_library_by_slug(string $slug): ?array
{
    foreach (reference_libraries() as $lib) {
        if ($lib['slug'] === $slug) {
            return $lib;
        }
    }
    return null;
}

/** Returns true if the reference table for this library actually exists in the DB. */
function reference_library_available(array $lib): bool
{
    $row = db_one(
        "SELECT 1 AS x FROM information_schema.TABLES
         WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?",
        [$lib['table']]
    );
    return (bool) $row;
}

/** Count rows in this library (uses status filter and respects table existence). */
function reference_library_count(array $lib): int
{
    if (!reference_library_available($lib)) {
        return 0;
    }
    try {
        $row = db_one("SELECT COUNT(*) AS c FROM `{$lib['table']}` WHERE status = 'active'");
    } catch (Throwable $e) {
        // Some tables may not have a status column — fall back to plain count.
        $row = db_one("SELECT COUNT(*) AS c FROM `{$lib['table']}`");
    }
    return (int) ($row['c'] ?? 0);
}

/** Group libraries by subject_slug for sidebar/nav rendering. */
function reference_libraries_by_subject(): array
{
    $out = [];
    foreach (reference_libraries() as $lib) {
        $out[$lib['subject_slug']][] = $lib;
    }
    return $out;
}
