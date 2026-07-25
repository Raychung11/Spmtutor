<?php
declare(strict_types=1);
require_once __DIR__ . '/../inc/auth.php';
require_once __DIR__ . '/../inc/admin_layout.php';

$admin  = require_role('admin');
$output = null;

/** List migration files in numeric order (phase4.sql, phase5.sql, …). */
function list_migration_files(): array
{
    $dir = __DIR__ . '/../sql/migrations';
    $files = glob($dir . '/*.sql') ?: [];
    sort($files, SORT_NATURAL);
    return $files;
}

/** Track applied migrations in a tiny table; create it on demand. */
function ensure_migrations_table(): void
{
    db_exec('CREATE TABLE IF NOT EXISTS applied_migrations (
        id INT AUTO_INCREMENT PRIMARY KEY,
        filename VARCHAR(190) NOT NULL UNIQUE,
        applied_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB');
}

function applied_migration_set(): array
{
    ensure_migrations_table();
    $rows = db_all('SELECT filename FROM applied_migrations');
    return array_column($rows, 'filename');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $which = input('which');
    $force = input('force') === '1';

    @set_time_limit(180);

    try {
        switch ($which) {
            case 'courses':
                require_once __DIR__ . '/../cron/seed_courses.php';
                $r = seed_courses();
                $output = [
                    'title'   => 'Courses seeded',
                    'lines'   => [
                        'Topics added: ' . $r['topics'],
                        'Skills added: ' . $r['skills'],
                        'Questions added: ' . $r['questions'],
                        'Questions skipped (already present): ' . $r['skipped'],
                    ],
                    'kind'    => 'success',
                ];
                break;

            case 'demo_logins':
                require_once __DIR__ . '/../cron/seed_demo_logins.php';
                $r = seed_demo_logins_run();
                $lines = ['Password for all demo accounts: ' . $r['password']];
                foreach ($r['accounts'] as $a) {
                    $lines[] = '[' . $a['action'] . '] ' . str_pad($a['role'], 13) . ' ' . $a['email'];
                }
                $output = ['title' => 'Demo logins ready', 'lines' => $lines, 'kind' => 'success'];
                break;

            case 'demo_data':
                require_once __DIR__ . '/../cron/seed_demo.php';
                $r = seed_demo_data_run($force);
                $output = [
                    'title' => 'Demo data',
                    'lines' => [$r['message']],
                    'kind'  => $r['status'] === 'seeded' ? 'success' : 'info',
                ];
                break;

            case 'weekly_reports':
                require_once __DIR__ . '/../cron/weekly_reports.php';
                $r = run_weekly_reports();
                $output = [
                    'title' => 'Weekly AI reports',
                    'lines' => ['Generated: ' . $r['generated'] . ' / ' . $r['total'], 'Failed: ' . $r['failed']],
                    'kind'  => 'success',
                ];
                break;

            case 'reminders':
                require_once __DIR__ . '/../cron/reminders.php';
                $r = run_reminders();
                $output = [
                    'title' => 'Daily reminders',
                    'lines' => ['Notified: ' . $r['sent'] . ' / ' . $r['total'], 'WhatsApp delivered: ' . $r['whatsapp']],
                    'kind'  => 'success',
                ];
                break;

            case 'schools':
                require_once __DIR__ . '/../cron/seed_schools.php';
                $r = seed_schools_run();
                $output = [
                    'title' => 'Klang Valley schools seeded',
                    'lines' => [
                        'Created: ' . $r['created'],
                        'Already present (kept): ' . $r['kept'],
                        'Total in catalog: ' . $r['total'],
                        'Priority targets in catalog: ' . $r['priority'] . ' (of which ' . $r['priority_created'] . ' were just created)',
                    ],
                    'kind'  => 'success',
                ];
                break;

            case 'subjects':
                require_once __DIR__ . '/../cron/seed_subjects.php';
                $r = seed_subjects_run();
                if (($r['status'] ?? '') === 'no_level') {
                    $output = ['title' => 'SPM subjects', 'lines' => [$r['message']], 'kind' => 'error'];
                } else {
                    $output = [
                        'title' => 'SPM subjects seeded',
                        'lines' => [
                            'Created: ' . $r['created'],
                            'Already present (kept): ' . $r['kept'],
                            'Total in catalog: ' . $r['total'],
                        ],
                        'kind'  => 'success',
                    ];
                }
                break;

            case 'math_kssm':
                require_once __DIR__ . '/../cron/seed_math_kssm.php';
                $r = math_kssm_run();
                if (($r['status'] ?? '') !== 'ok') {
                    $output = ['title' => 'KSSM Mathematics', 'lines' => [$r['message'] ?? 'Failed.'], 'kind' => 'error'];
                } else {
                    $output = [
                        'title' => 'KSSM Mathematics seeded',
                        'lines' => [
                            'Topics created: ' . $r['topics_created'] . ' (kept ' . $r['topics_kept'] . ', catalog ' . $r['catalog_topics'] . ')',
                            'Subtopics created: ' . $r['subtopics_created'] . ' (kept ' . $r['subtopics_kept'] . ')',
                            'Starter skills created: ' . $r['skills_created'] . ' (kept ' . $r['skills_kept'] . ')',
                            'Next: open Admin → Topics, filter by Mathematics, and use 🤖 Skills / 🤖 Qs on each row.',
                        ],
                        'kind'  => 'success',
                    ];
                }
                break;

            case 'chemistry_kssm':
                require_once __DIR__ . '/../cron/seed_chemistry_kssm.php';
                $r = chemistry_kssm_run();
                if (($r['status'] ?? '') !== 'ok') {
                    $output = ['title' => 'KSSM Chemistry', 'lines' => [$r['message'] ?? 'Failed.'], 'kind' => 'error'];
                } else {
                    $output = [
                        'title' => 'KSSM Chemistry seeded',
                        'lines' => [
                            'Topics: created ' . $r['topics_created'] . ', adopted legacy ' . $r['topics_adopted'] . ', kept ' . $r['topics_kept'] . ' (catalog ' . $r['catalog_topics'] . ')',
                            'Subtopics: created ' . $r['subtopics_created'] . ', kept ' . $r['subtopics_kept'],
                            'Skills: created ' . $r['skills_created'] . ', kept ' . $r['skills_kept'],
                            'Formulas: created ' . $r['formulas_created'] . ', kept ' . $r['formulas_kept'],
                            'Next: Admin → Topics, filter by Chemistry, 🤖 Qs to generate question banks.',
                        ],
                        'kind'  => 'success',
                    ];
                }
                break;

            case 'biology_kssm':
                require_once __DIR__ . '/../cron/seed_biology_kssm.php';
                $r = biology_kssm_run();
                if (($r['status'] ?? '') !== 'ok') {
                    $output = ['title' => 'KSSM Biology', 'lines' => [$r['message'] ?? 'Failed.'], 'kind' => 'error'];
                } else {
                    $lines = [
                        'Topics: created ' . $r['topics_created'] . ', adopted legacy ' . $r['topics_adopted'] . ', kept ' . $r['topics_kept'] . ' (catalog ' . $r['catalog_topics'] . ')',
                        'Subtopics: created ' . $r['subtopics_created'] . ', kept ' . $r['subtopics_kept'],
                        'Skills: created ' . $r['skills_created'] . ', kept ' . $r['skills_kept'],
                    ];
                    $lines[] = $r['diagrams_table']
                        ? 'Diagrams: created ' . $r['diagrams_created'] . ', kept ' . $r['diagrams_kept']
                        : 'Diagrams: table missing — run database migrations to enable.';
                    $lines[] = $r['flashcards_table']
                        ? 'Flashcards: created ' . $r['flashcards_created'] . ', kept ' . $r['flashcards_kept']
                        : 'Flashcards: table missing — run database migrations to enable.';
                    $lines[] = 'Next: Admin → Topics, filter by Biology, 🤖 Qs to generate question banks.';
                    $output = ['title' => 'KSSM Biology seeded', 'lines' => $lines, 'kind' => 'success'];
                }
                break;

            case 'bm_kssm':
                require_once __DIR__ . '/../cron/seed_bm_kssm.php';
                $r = bm_kssm_run();
                if (($r['status'] ?? '') !== 'ok') {
                    $output = ['title' => 'KSSM Bahasa Melayu', 'lines' => [$r['message'] ?? 'Failed.'], 'kind' => 'error'];
                } else {
                    $lines = [
                        'Topics: created ' . $r['topics_created'] . ', adopted legacy ' . $r['topics_adopted'] . ', kept ' . $r['topics_kept'] . ' (catalog ' . $r['catalog_topics'] . ')',
                        'Subtopics: created ' . $r['subtopics_created'] . ', kept ' . $r['subtopics_kept'],
                        'Skills: created ' . $r['skills_created'] . ', kept ' . $r['skills_kept'],
                    ];
                    $lines[] = $r['peribahasa_table']
                        ? 'Peribahasa: created ' . $r['peribahasa_created'] . ', kept ' . $r['peribahasa_kept']
                        : 'Peribahasa: table missing — run database migrations to enable.';
                    $lines[] = 'Next: Admin → Topics, filter by Bahasa Melayu, 🤖 Qs to generate karangan / tatabahasa / KOMSAS banks.';
                    $output = ['title' => 'KSSM Bahasa Melayu seeded', 'lines' => $lines, 'kind' => 'success'];
                }
                break;

            case 'english_kssm':
                require_once __DIR__ . '/../cron/seed_english_kssm.php';
                $r = english_kssm_run();
                if (($r['status'] ?? '') !== 'ok') {
                    $output = ['title' => 'KSSM English', 'lines' => [$r['message'] ?? 'Failed.'], 'kind' => 'error'];
                } else {
                    $lines = [
                        'Topics: created ' . $r['topics_created'] . ', adopted legacy ' . $r['topics_adopted'] . ', kept ' . $r['topics_kept'] . ' (catalog ' . $r['catalog_topics'] . ')',
                        'Subtopics: created ' . $r['subtopics_created'] . ', kept ' . $r['subtopics_kept'],
                        'Skills: created ' . $r['skills_created'] . ', kept ' . $r['skills_kept'],
                    ];
                    $lines[] = $r['vocab_table']
                        ? 'Vocabulary: created ' . $r['vocab_created'] . ', kept ' . $r['vocab_kept']
                        : 'Vocabulary: table missing — run database migrations to enable.';
                    $lines[] = $r['idioms_table']
                        ? 'Idioms / phrasal verbs: created ' . $r['idioms_created'] . ', kept ' . $r['idioms_kept']
                        : 'Idioms: table missing — run database migrations to enable.';
                    $lines[] = $r['samples_table']
                        ? 'Writing samples: created ' . $r['samples_created'] . ', kept ' . $r['samples_kept']
                        : 'Writing samples: table missing — run database migrations to enable.';
                    $lines[] = 'Next: Admin → Topics, filter by English, 🤖 Qs to generate essay / reading / grammar / literature banks.';
                    $output = ['title' => 'KSSM English seeded', 'lines' => $lines, 'kind' => 'success'];
                }
                break;

            case 'sejarah_kssm':
                require_once __DIR__ . '/../cron/seed_sejarah_kssm.php';
                $r = sejarah_kssm_run();
                if (($r['status'] ?? '') !== 'ok') {
                    $output = ['title' => 'KSSM Sejarah', 'lines' => [$r['message'] ?? 'Failed.'], 'kind' => 'error'];
                } else {
                    $lines = [
                        'Bab: created ' . $r['topics_created'] . ', adopted legacy ' . $r['topics_adopted'] . ', kept ' . $r['topics_kept'] . ' (catalog ' . $r['catalog_topics'] . ')',
                        'Subtopik: created ' . $r['subtopics_created'] . ', kept ' . $r['subtopics_kept'],
                        'Kemahiran: created ' . $r['skills_created'] . ', kept ' . $r['skills_kept'],
                    ];
                    $lines[] = $r['timeline_table']
                        ? 'Timeline: created ' . $r['timeline_created'] . ', kept ' . $r['timeline_kept']
                        : 'Timeline: table missing — run database migrations to enable.';
                    $lines[] = 'Next: Admin → Topics, filter by Sejarah, 🤖 Qs to generate KBAT / esei / objektif banks.';
                    $output = ['title' => 'KSSM Sejarah seeded', 'lines' => $lines, 'kind' => 'success'];
                }
                break;

            case 'geografi_kssm':
                require_once __DIR__ . '/../cron/seed_geografi_kssm.php';
                $r = geografi_kssm_run();
                if (($r['status'] ?? '') !== 'ok') {
                    $output = ['title' => 'KSSM Geografi', 'lines' => [$r['message'] ?? 'Failed.'], 'kind' => 'error'];
                } else {
                    $lines = [
                        'Bab: created ' . $r['topics_created'] . ', adopted legacy ' . $r['topics_adopted'] . ', kept ' . $r['topics_kept'] . ' (catalog ' . $r['catalog_topics'] . ')',
                        'Subtopik: created ' . $r['subtopics_created'] . ', kept ' . $r['subtopics_kept'],
                        'Kemahiran: created ' . $r['skills_created'] . ', kept ' . $r['skills_kept'],
                    ];
                    $lines[] = $r['locations_table']
                        ? 'Lokasi: created ' . $r['locations_created'] . ', kept ' . $r['locations_kept']
                        : 'Lokasi: table missing — run database migrations to enable.';
                    $lines[] = 'Next: Admin → Topics, filter by Geografi, 🤖 Qs to generate peta / iklim / sumber question banks.';
                    $output = ['title' => 'KSSM Geografi seeded', 'lines' => $lines, 'kind' => 'success'];
                }
                break;

            case 'pendidikan_islam_kssm':
                require_once __DIR__ . '/../cron/seed_pendidikan_islam_kssm.php';
                $r = pendidikan_islam_kssm_run();
                if (($r['status'] ?? '') !== 'ok') {
                    $output = ['title' => 'KSSM Pendidikan Islam', 'lines' => [$r['message'] ?? 'Failed.'], 'kind' => 'error'];
                } else {
                    $lines = [
                        'Bidang: created ' . $r['topics_created'] . ', adopted legacy ' . $r['topics_adopted'] . ', kept ' . $r['topics_kept'] . ' (catalog ' . $r['catalog_topics'] . ')',
                        'Pelajaran (subtopik): created ' . $r['subtopics_created'] . ', kept ' . $r['subtopics_kept'],
                        'Kemahiran: created ' . $r['skills_created'] . ', kept ' . $r['skills_kept'],
                    ];
                    $lines[] = $r['ayat_table']
                        ? 'Ayat / Hadis / Doa: created ' . $r['ayat_created'] . ', kept ' . $r['ayat_kept']
                        : 'Ayat / Hadis / Doa: table missing — run database migrations to enable.';
                    $lines[] = 'Next: Admin → Topics, filter by Pendidikan Islam, 🤖 Qs to generate KBAT / esei / objektif banks.';
                    $output = ['title' => 'KSSM Pendidikan Islam seeded', 'lines' => $lines, 'kind' => 'success'];
                }
                break;

            case 'pendidikan_moral_kssm':
                require_once __DIR__ . '/../cron/seed_pendidikan_moral_kssm.php';
                $r = pendidikan_moral_kssm_run();
                if (($r['status'] ?? '') !== 'ok') {
                    $output = ['title' => 'KSSM Pendidikan Moral', 'lines' => [$r['message'] ?? 'Failed.'], 'kind' => 'error'];
                } else {
                    $lines = [
                        'Unit: created ' . $r['topics_created'] . ', adopted legacy ' . $r['topics_adopted'] . ', kept ' . $r['topics_kept'] . ' (catalog ' . $r['catalog_topics'] . ')',
                        'Subtopik: created ' . $r['subtopics_created'] . ', kept ' . $r['subtopics_kept'],
                        'Kemahiran: created ' . $r['skills_created'] . ', kept ' . $r['skills_kept'],
                    ];
                    $lines[] = $r['values_table']
                        ? 'Nilai utama: created ' . $r['values_created'] . ', kept ' . $r['values_kept']
                        : 'Nilai utama: table missing — run database migrations to enable.';
                    $lines[] = 'Next: Admin → Topics, filter by Pendidikan Moral, 🤖 Qs to generate KBAT / esei / objektif banks.';
                    $output = ['title' => 'KSSM Pendidikan Moral seeded', 'lines' => $lines, 'kind' => 'success'];
                }
                break;

            case 'perakaunan_kssm':
                require_once __DIR__ . '/../cron/seed_perakaunan_kssm.php';
                $r = perakaunan_kssm_run();
                if (($r['status'] ?? '') !== 'ok') {
                    $output = ['title' => 'KSSM Prinsip Perakaunan', 'lines' => [$r['message'] ?? 'Failed.'], 'kind' => 'error'];
                } else {
                    $lines = [
                        'Bab: created ' . $r['topics_created'] . ', adopted legacy ' . $r['topics_adopted'] . ', kept ' . $r['topics_kept'] . ' (catalog ' . $r['catalog_topics'] . ')',
                        'Subtopik: created ' . $r['subtopics_created'] . ', kept ' . $r['subtopics_kept'],
                        'Kemahiran: created ' . $r['skills_created'] . ', kept ' . $r['skills_kept'],
                    ];
                    $lines[] = $r['formulas_table']
                        ? 'Formula / nisbah: created ' . $r['formulas_created'] . ', kept ' . $r['formulas_kept']
                        : 'Formula / nisbah: table missing — run database migrations to enable.';
                    $lines[] = 'Next: Admin → Topics, filter by Prinsip Perakaunan, 🤖 Qs to generate jurnal / lejar / penyata banks.';
                    $output = ['title' => 'KSSM Prinsip Perakaunan seeded', 'lines' => $lines, 'kind' => 'success'];
                }
                break;

            case 'perniagaan_kssm':
                require_once __DIR__ . '/../cron/seed_perniagaan_kssm.php';
                $r = perniagaan_kssm_run();
                if (($r['status'] ?? '') !== 'ok') {
                    $output = ['title' => 'KSSM Perniagaan', 'lines' => [$r['message'] ?? 'Failed.'], 'kind' => 'error'];
                } else {
                    $lines = [
                        'Bab: created ' . $r['topics_created'] . ', adopted legacy ' . $r['topics_adopted'] . ', kept ' . $r['topics_kept'] . ' (catalog ' . $r['catalog_topics'] . ')',
                        'Subtopik: created ' . $r['subtopics_created'] . ', kept ' . $r['subtopics_kept'],
                        'Kemahiran: created ' . $r['skills_created'] . ', kept ' . $r['skills_kept'],
                    ];
                    $lines[] = $r['terms_table']
                        ? 'Istilah: created ' . $r['terms_created'] . ', kept ' . $r['terms_kept']
                        : 'Istilah: table missing — run database migrations to enable.';
                    $lines[] = 'Next: Admin → Topics, filter by Perniagaan, 🤖 Qs to generate case study / esei / objektif banks.';
                    $output = ['title' => 'KSSM Perniagaan seeded', 'lines' => $lines, 'kind' => 'success'];
                }
                break;

            case 'ekonomi_kssm':
                require_once __DIR__ . '/../cron/seed_ekonomi_kssm.php';
                $r = ekonomi_kssm_run();
                if (($r['status'] ?? '') !== 'ok') {
                    $output = ['title' => 'KSSM Ekonomi', 'lines' => [$r['message'] ?? 'Failed.'], 'kind' => 'error'];
                } else {
                    $lines = [
                        'Topik: created ' . $r['topics_created'] . ', adopted legacy ' . $r['topics_adopted'] . ', kept ' . $r['topics_kept'] . ' (catalog ' . $r['catalog_topics'] . ')',
                        'Subtopik: created ' . $r['subtopics_created'] . ', kept ' . $r['subtopics_kept'],
                        'Kemahiran: created ' . $r['skills_created'] . ', kept ' . $r['skills_kept'],
                    ];
                    $lines[] = $r['concepts_table']
                        ? 'Konsep (istilah + formula): created ' . $r['concepts_created'] . ', kept ' . $r['concepts_kept']
                        : 'Konsep: table missing — run database migrations to enable.';
                    $lines[] = 'Next: Admin → Topics, filter by Ekonomi, 🤖 Qs to generate graf / pengiraan / esei banks.';
                    $output = ['title' => 'KSSM Ekonomi seeded', 'lines' => $lines, 'kind' => 'success'];
                }
                break;

            case 'sains_komputer_kssm':
                require_once __DIR__ . '/../cron/seed_sains_komputer_kssm.php';
                $r = sains_komputer_kssm_run();
                if (($r['status'] ?? '') !== 'ok') {
                    $output = ['title' => 'KSSM Sains Komputer', 'lines' => [$r['message'] ?? 'Failed.'], 'kind' => 'error'];
                } else {
                    $lines = [
                        'Topik: created ' . $r['topics_created'] . ', adopted legacy ' . $r['topics_adopted'] . ', kept ' . $r['topics_kept'] . ' (catalog ' . $r['catalog_topics'] . ')',
                        'Subtopik: created ' . $r['subtopics_created'] . ', kept ' . $r['subtopics_kept'],
                        'Kemahiran: created ' . $r['skills_created'] . ', kept ' . $r['skills_kept'],
                    ];
                    $lines[] = $r['concepts_table']
                        ? 'Konsep + code snippets: created ' . $r['concepts_created'] . ', kept ' . $r['concepts_kept']
                        : 'Konsep + code: table missing — run database migrations to enable.';
                    $lines[] = 'Next: Admin → Topics, filter by Sains Komputer, 🤖 Qs to generate code / SQL / algoritma banks.';
                    $output = ['title' => 'KSSM Sains Komputer seeded', 'lines' => $lines, 'kind' => 'success'];
                }
                break;

            case 'rbt_kssm':
                require_once __DIR__ . '/../cron/seed_rbt_kssm.php';
                $r = rbt_kssm_run();
                if (($r['status'] ?? '') !== 'ok') {
                    $output = ['title' => 'KSSM RBT / Reka Cipta', 'lines' => [$r['message'] ?? 'Failed.'], 'kind' => 'error'];
                } else {
                    $lines = [
                        'Bab: created ' . $r['topics_created'] . ', adopted legacy ' . $r['topics_adopted'] . ', kept ' . $r['topics_kept'] . ' (catalog ' . $r['catalog_topics'] . ')',
                        'Subtopik: created ' . $r['subtopics_created'] . ', kept ' . $r['subtopics_kept'],
                        'Kemahiran: created ' . $r['skills_created'] . ', kept ' . $r['skills_kept'],
                    ];
                    $lines[] = $r['concepts_table']
                        ? 'Konsep reka cipta: created ' . $r['concepts_created'] . ', kept ' . $r['concepts_kept']
                        : 'Konsep: table missing — run database migrations to enable.';
                    $lines[] = 'Next: Admin → Topics, filter by RBT / Reka Cipta, 🤖 Qs to generate reka bentuk / projek / KBAT banks.';
                    $output = ['title' => 'KSSM RBT / Reka Cipta seeded', 'lines' => $lines, 'kind' => 'success'];
                }
                break;

            case 'psv_kssm':
                require_once __DIR__ . '/../cron/seed_psv_kssm.php';
                $r = psv_kssm_run();
                if (($r['status'] ?? '') !== 'ok') {
                    $output = ['title' => 'KSSM Pendidikan Seni Visual', 'lines' => [$r['message'] ?? 'Failed.'], 'kind' => 'error'];
                } else {
                    $lines = [
                        'Tajuk: created ' . $r['topics_created'] . ', adopted legacy ' . $r['topics_adopted'] . ', kept ' . $r['topics_kept'] . ' (catalog ' . $r['catalog_topics'] . ')',
                        'Subtopik: created ' . $r['subtopics_created'] . ', kept ' . $r['subtopics_kept'],
                        'Kemahiran: created ' . $r['skills_created'] . ', kept ' . $r['skills_kept'],
                    ];
                    $lines[] = $r['refs_table']
                        ? 'Rujukan (tokoh + teknik + istilah): created ' . $r['refs_created'] . ', kept ' . $r['refs_kept']
                        : 'Rujukan: table missing — run database migrations to enable.';
                    $lines[] = 'Next: Admin → Topics, filter by PSV, 🤖 Qs to generate apresiasi / teknik / projek banks.';
                    $output = ['title' => 'KSSM Pendidikan Seni Visual seeded', 'lines' => $lines, 'kind' => 'success'];
                }
                break;

            case 'sains_kssm':
                require_once __DIR__ . '/../cron/seed_sains_kssm.php';
                $r = sains_kssm_run();
                if (($r['status'] ?? '') !== 'ok') {
                    $output = ['title' => 'KSSM Sains', 'lines' => [$r['message'] ?? 'Failed.'], 'kind' => 'error'];
                } else {
                    $lines = [
                        'Bab: created ' . $r['topics_created'] . ', adopted legacy ' . $r['topics_adopted'] . ', kept ' . $r['topics_kept'] . ' (catalog ' . $r['catalog_topics'] . ')',
                        'Subtopik: created ' . $r['subtopics_created'] . ', kept ' . $r['subtopics_kept'],
                        'Kemahiran: created ' . $r['skills_created'] . ', kept ' . $r['skills_kept'],
                    ];
                    $lines[] = $r['concepts_table']
                        ? 'Konsep (istilah + formula): created ' . $r['concepts_created'] . ', kept ' . $r['concepts_kept']
                        : 'Konsep: table missing — run database migrations to enable.';
                    $lines[] = 'Next: Admin → Topics, filter by Sains, 🤖 Qs to generate KBAT / pengiraan / esei banks.';
                    $output = ['title' => 'KSSM Sains seeded', 'lines' => $lines, 'kind' => 'success'];
                }
                break;

            case 'bahasa_cina_kssm':
                require_once __DIR__ . '/../cron/seed_bahasa_cina_kssm.php';
                $r = bahasa_cina_kssm_run();
                if (($r['status'] ?? '') !== 'ok') {
                    $output = ['title' => 'KSSM Bahasa Cina', 'lines' => [$r['message'] ?? 'Failed.'], 'kind' => 'error'];
                } else {
                    $lines = [
                        'Topik: created ' . $r['topics_created'] . ', adopted legacy ' . $r['topics_adopted'] . ', kept ' . $r['topics_kept'] . ' (catalog ' . $r['catalog_topics'] . ')',
                        'Subtopik: created ' . $r['subtopics_created'] . ', kept ' . $r['subtopics_kept'],
                        'Kemahiran: created ' . $r['skills_created'] . ', kept ' . $r['skills_kept'],
                    ];
                    $lines[] = $r['refs_table']
                        ? 'Rujukan (成语 + wenyan + pengarang): created ' . $r['refs_created'] . ', kept ' . $r['refs_kept']
                        : 'Rujukan: table missing — run database migrations to enable.';
                    $lines[] = 'Next: Admin → Topics, filter by Bahasa Cina, 🤖 Qs to generate 作文 / 阅读 / 文言 banks.';
                    $output = ['title' => 'KSSM Bahasa Cina seeded', 'lines' => $lines, 'kind' => 'success'];
                }
                break;

            case 'bahasa_tamil_kssm':
                require_once __DIR__ . '/../cron/seed_bahasa_tamil_kssm.php';
                $r = bahasa_tamil_kssm_run();
                if (($r['status'] ?? '') !== 'ok') {
                    $output = ['title' => 'KSSM Bahasa Tamil', 'lines' => [$r['message'] ?? 'Failed.'], 'kind' => 'error'];
                } else {
                    $lines = [
                        'Topik: created ' . $r['topics_created'] . ', adopted legacy ' . $r['topics_adopted'] . ', kept ' . $r['topics_kept'] . ' (catalog ' . $r['catalog_topics'] . ')',
                        'Subtopik: created ' . $r['subtopics_created'] . ', kept ' . $r['subtopics_kept'],
                        'Kemahiran: created ' . $r['skills_created'] . ', kept ' . $r['skills_kept'],
                    ];
                    $lines[] = $r['refs_table']
                        ? 'Rujukan (Thirukkural + Pazhamozhi + Tokoh): created ' . $r['refs_created'] . ', kept ' . $r['refs_kept']
                        : 'Rujukan: table missing — run database migrations to enable.';
                    $lines[] = 'Next: Admin → Topics, filter by Bahasa Tamil, 🤖 Qs to generate கட்டுரை / புரிதல் / இலக்கணம் banks.';
                    $output = ['title' => 'KSSM Bahasa Tamil seeded', 'lines' => $lines, 'kind' => 'success'];
                }
                break;

            case 'bahasa_arab_kssm':
                require_once __DIR__ . '/../cron/seed_bahasa_arab_kssm.php';
                $r = bahasa_arab_kssm_run();
                if (($r['status'] ?? '') !== 'ok') {
                    $output = ['title' => 'KSSM Bahasa Arab', 'lines' => [$r['message'] ?? 'Failed.'], 'kind' => 'error'];
                } else {
                    $lines = [
                        'Topik: created ' . $r['topics_created'] . ', adopted legacy ' . $r['topics_adopted'] . ', kept ' . $r['topics_kept'] . ' (catalog ' . $r['catalog_topics'] . ')',
                        'Subtopik: created ' . $r['subtopics_created'] . ', kept ' . $r['subtopics_kept'],
                        'Kemahiran: created ' . $r['skills_created'] . ', kept ' . $r['skills_kept'],
                    ];
                    $lines[] = $r['refs_table']
                        ? 'Rujukan (أمثال + nahu + mufradat + tokoh): created ' . $r['refs_created'] . ', kept ' . $r['refs_kept']
                        : 'Rujukan: table missing — run database migrations to enable.';
                    $lines[] = 'Next: Admin → Topics, filter by Bahasa Arab, 🤖 Qs to generate إنشاء / قراءة / نحو banks.';
                    $output = ['title' => 'KSSM Bahasa Arab seeded', 'lines' => $lines, 'kind' => 'success'];
                }
                break;

            case 'kepintaran_buatan_kssm':
                require_once __DIR__ . '/../cron/seed_kepintaran_buatan_kssm.php';
                $r = kepintaran_buatan_kssm_run();
                if (($r['status'] ?? '') !== 'ok') {
                    $output = ['title' => 'Pioneer Asas Kepintaran Buatan', 'lines' => [$r['message'] ?? 'Failed.'], 'kind' => 'error'];
                } else {
                    $lines = [];
                    if (!empty($r['subject_auto_created'])) {
                        $lines[] = 'Subject auto-created: Asas Kepintaran Buatan (slug: kepintaran-buatan)';
                    }
                    $lines[] = 'Bab: created ' . $r['topics_created'] . ', adopted legacy ' . $r['topics_adopted'] . ', kept ' . $r['topics_kept'] . ' (catalog ' . $r['catalog_topics'] . ')';
                    $lines[] = 'Subtopik: created ' . $r['subtopics_created'] . ', kept ' . $r['subtopics_kept'];
                    $lines[] = 'Kemahiran: created ' . $r['skills_created'] . ', kept ' . $r['skills_kept'];
                    $lines[] = $r['concepts_table']
                        ? 'Konsep AI (istilah + code + prompt patterns): created ' . $r['concepts_created'] . ', kept ' . $r['concepts_kept']
                        : 'Konsep AI: table missing — run database migrations to enable.';
                    $lines[] = $r['timeline_table']
                        ? 'Timeline AI (1950 Turing → 2025 reasoning): created ' . $r['timeline_created'] . ', kept ' . $r['timeline_kept']
                        : 'Timeline AI: table missing — run database migrations to enable.';
                    $lines[] = 'Next: Admin → Topics, filter by Asas Kepintaran Buatan, 🤖 Qs to generate prompt / ML / etika banks.';
                    $output = ['title' => 'Pioneer Asas Kepintaran Buatan seeded', 'lines' => $lines, 'kind' => 'success'];
                }
                break;

            case 'physics_kssm':
                require_once __DIR__ . '/../cron/seed_physics_kssm.php';
                $r = physics_kssm_run();
                if (($r['status'] ?? '') !== 'ok') {
                    $output = ['title' => 'KSSM Physics', 'lines' => [$r['message'] ?? 'Failed.'], 'kind' => 'error'];
                } else {
                    $output = [
                        'title' => 'KSSM Physics seeded',
                        'lines' => [
                            'Topics: created ' . $r['topics_created'] . ', adopted legacy ' . $r['topics_adopted'] . ', kept ' . $r['topics_kept'] . ' (catalog ' . $r['catalog_topics'] . ')',
                            'Subtopics: created ' . $r['subtopics_created'] . ', kept ' . $r['subtopics_kept'],
                            'Skills: created ' . $r['skills_created'] . ', kept ' . $r['skills_kept'],
                            'Formulas: created ' . $r['formulas_created'] . ', kept ' . $r['formulas_kept'],
                            'Next: Admin → Topics, filter by Physics, 🤖 Qs to generate question banks.',
                        ],
                        'kind'  => 'success',
                    ];
                }
                break;

            case 'addmath_kssm':
                require_once __DIR__ . '/../cron/seed_addmath_kssm.php';
                $r = addmath_kssm_run();
                if (($r['status'] ?? '') !== 'ok') {
                    $output = ['title' => 'KSSM Additional Mathematics', 'lines' => [$r['message'] ?? 'Failed.'], 'kind' => 'error'];
                } else {
                    $output = [
                        'title' => 'KSSM Additional Mathematics seeded',
                        'lines' => [
                            'Topics created: ' . $r['topics_created'] . ' (kept ' . $r['topics_kept'] . ', catalog ' . $r['catalog_topics'] . ')',
                            'Subtopics created: ' . $r['subtopics_created'] . ' (kept ' . $r['subtopics_kept'] . ')',
                            'Skills created: ' . $r['skills_created'] . ' (kept ' . $r['skills_kept'] . ')',
                            'Next: open Admin → Topics, filter by Additional Mathematics, and use 🤖 Qs to generate the question bank.',
                        ],
                        'kind'  => 'success',
                    ];
                }
                break;

            case 'education_levels':
                require_once __DIR__ . '/../cron/seed_education_levels.php';
                $r = seed_education_levels_run();
                $output = [
                    'title' => 'Education levels seeded',
                    'lines' => [
                        'Created: ' . $r['created'],
                        'Kept (already present, refreshed): ' . $r['kept'],
                        'Catalog total: ' . $r['total'],
                        'Includes: UPSR, PT3, SPM, STPM (Malaysia) + IGCSE (Cambridge International).',
                    ],
                    'kind' => 'success',
                ];
                break;

            case 'dedupe_subjects':
                require_once __DIR__ . '/../cron/dedupe_subjects.php';
                $r = dedupe_subjects_run($force);
                $kind = $r['groups'] > 0 ? 'success' : 'info';
                $title = $force ? 'Subject dedupe — applied' : 'Subject dedupe — dry-run';
                $lines = [
                    ($force ? 'APPLIED — duplicates have been merged and deleted.' : 'DRY-RUN — tick "Force re-run" below to actually merge + delete.'),
                    'Duplicate groups:   ' . $r['groups'],
                    'Duplicate subjects: ' . $r['duplicates'],
                    'Rows re-pointed:    ' . $r['rows_repointed'],
                    'Stats conflicts:    ' . $r['stats_conflicts'] . ' (winning row kept, loser dropped)',
                    'Subjects deleted:   ' . $r['subjects_deleted'],
                ];
                foreach ($r['details'] as $d) {
                    $lines[] = '· ' . $d;
                }
                $output = ['title' => $title, 'lines' => $lines, 'kind' => $kind];
                break;

            case 'migrations':
                $applied = applied_migration_set();
                $cfg = require __DIR__ . '/../config/db_config.php';
                $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=%s', $cfg['host'], $cfg['port'], $cfg['name'], $cfg['charset']);
                $pdo = new PDO($dsn, $cfg['user'], $cfg['pass'], [
                    PDO::ATTR_ERRMODE          => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_EMULATE_PREPARES => true,
                ]);
                $lines = [];
                foreach (list_migration_files() as $path) {
                    $name = basename($path);
                    if (!$force && in_array($name, $applied, true)) {
                        $lines[] = '[skip] ' . $name . ' — already applied';
                        continue;
                    }
                    try {
                        $pdo->exec(file_get_contents($path));
                        db_exec(
                            'INSERT INTO applied_migrations (filename) VALUES (?)
                             ON DUPLICATE KEY UPDATE applied_at = NOW()',
                            [$name]
                        );
                        $lines[] = '[ok]   ' . $name;
                    } catch (Throwable $e) {
                        $lines[] = '[FAIL] ' . $name . ' — ' . $e->getMessage();
                    }
                }
                $output = ['title' => 'Migrations', 'lines' => $lines ?: ['No migration files found.'], 'kind' => 'success'];
                break;

            default:
                flash('error', 'Unknown seeder.');
                redirect('admin/seeders.php');
        }
    } catch (Throwable $e) {
        $output = ['title' => 'Failed', 'lines' => [$e->getMessage()], 'kind' => 'error'];
    }
}

$seeders = [
    ['key' => 'migrations',       'name' => 'Run database migrations', 'desc' => 'Apply any pending sql/migrations/*.sql files (e.g. phase4, phase5, phase6 school portal, phase7 invitations, phase8 leads). Each file runs at most once. Tracks state in an applied_migrations table.', 'has_force' => true],
    ['key' => 'education_levels', 'name' => 'Education levels (regions)', 'desc' => 'Seed the education levels catalog — Malaysian ladder (UPSR / PT3 / SPM / STPM) plus IGCSE (Cambridge International). Idempotent: existing rows are refreshed with the latest name / sort order; user profiles keep their chosen level. Run this once so IGCSE appears in the registration dropdown.'],
    ['key' => 'dedupe_subjects','name' => 'Deduplicate subjects (maintenance)', 'desc' => 'Find subjects that share an (education_level, slug) or (education_level, name) — usually from running an import twice — and merge them. Re-points every dependent row (topics, questions, lessons, diagnostic tests, learning paths, teacher classes, chat sessions, essay submissions, sandbox runs, student stats) to the lowest-id keeper, then deletes the duplicates. Runs as DRY-RUN by default — tick "Force re-run" to actually commit the merge. Note: this dedupes the SUBJECTS rows; if you imported topics/skills twice too, run the matching subject seeder again afterwards (it is idempotent) or ask for a topics-level dedupe.', 'has_force' => true],
    ['key' => 'courses',        'name' => 'Seed courses',         'desc' => 'Add the topics, skills and MCQs from the course catalog (Math, Add Maths, Physics, Chemistry, Biology, English, BM). Idempotent — already-present items are skipped.'],
    ['key' => 'demo_logins',    'name' => 'Demo logins (5 roles)','desc' => 'Create one demo account per role (student, parent, teacher, school admin, platform admin) with predictable credentials and sensible relationships.'],
    ['key' => 'schools',        'name' => 'Klang Valley schools',  'desc' => 'Seed ~100 SMK secondary schools across KL, PJ, Shah Alam, Subang, Klang, Kajang, Cheras, Puchong, Bangi, Cyberjaya and Putrajaya. Idempotent — schools already present (by name) are skipped.'],
    ['key' => 'subjects',       'name' => 'SPM subjects',          'desc' => 'Add the missing SPM subjects beyond the original 7 (Sejarah, Pendidikan Islam, Pendidikan Moral, Perakaunan, Perniagaan, Ekonomi, Sains Komputer, RBT, Sains, Geografi, PSV, three languages, three Islamic electives). Idempotent — existing slugs/names are kept.'],
    ['key' => 'math_kssm',      'name' => 'KSSM Mathematics (Form 4 + 5)', 'desc' => 'Seed the full KSSM SPM Mathematics syllabus: 20 chapters across Form 4 & Form 5 with all their subtopics, plus a few starter skills. Idempotent — existing topics/subtopics matched by name are kept and just refreshed with form_level.'],
    ['key' => 'addmath_kssm',   'name' => 'KSSM Additional Mathematics (Form 4 + 5)', 'desc' => 'Seed the full KSSM SPM Additional Mathematics syllabus: 20 chapters across Form 4 & Form 5 with all their subtopics and the syllabus-defined starter skills. Idempotent.'],
    ['key' => 'physics_kssm',   'name' => 'KSSM Physics (Form 4 + 5)',                'desc' => 'Seed the full KSSM SPM Physics syllabus: 15 chapters (F4: 8, F5: 7) with subtopics + starter skills, plus a Physics formula bank (kinematics, momentum, Ohm/power, transformer, pressure, half-life, photon energy …). Adopts legacy same-named topics like the original Electricity row.'],
    ['key' => 'chemistry_kssm', 'name' => 'KSSM Chemistry (Form 4 + 5)',              'desc' => 'Seed the full KSSM SPM Chemistry syllabus: 16 chapters (F4: 8, F5: 8) with subtopics + starter skills, plus a Chemistry formula bank (n=m/Mr, C=n/V, dilution, pH, percentage yield/purity, ΔH/Q=mcΔT, Q=It, n_e=Q/F …).'],
    ['key' => 'biology_kssm',   'name' => 'KSSM Biology (Form 4 + 5)',                'desc' => 'Seed the full KSSM SPM Biology syllabus: 21 chapters (F4: 11, F5: 10) with subtopics + starter skills, plus a diagram bank (animal/plant cell, heart, digestive system, nephron, xylem/phloem, monohybrid cross …) and ~60 starter flashcards. Run phase14 migration first to enable diagrams/flashcards.'],
    ['key' => 'bm_kssm',        'name' => 'KSSM Bahasa Melayu (Form 4 + 5)',          'desc' => 'Seed the full KSSM SPM Bahasa Melayu syllabus: 14 topik (F4: 7, F5: 7) — Kemahiran Mendengar, Sistem Bahasa, Tatabahasa, Pemahaman, Rumusan, Karangan, KOMSAS, Novel — with subtopics + starter skills, plus a peribahasa / simpulan bahasa / bidalan / pepatah / cogan kata bank used by the AI Tatabahasa Trainer. Run phase15 migration first to enable the peribahasa table.'],
    ['key' => 'english_kssm',   'name' => 'KSSM English (Form 4 + 5)',                'desc' => 'Seed the full KSSM SPM English syllabus: 20 chapters (F4: 10, F5: 10) covering Listening / Speaking / Reading / Writing / Grammar / Vocabulary / Literature (Poems, Short Stories, Novel, Drama), with subtopics + starter skills. Plus a starter vocabulary bank (~55 SPM-level words), idiom + phrasal verb bank (~30 entries) and 5 model writing samples used by the AI Essay Marker. Run phase16 migration first to enable the reference tables.'],
    ['key' => 'sejarah_kssm',   'name' => 'KSSM Sejarah (Tingkatan 4 + 5)',           'desc' => 'Seed the full KSSM SPM Sejarah syllabus: 20 bab (T4: 10 — Warisan Negara Bangsa → Pemasyhuran Kemerdekaan; T5: 10 — Kedaulatan Negara → Kecemerlangan Malaysia di Persada Dunia) with subtopik + starter skills, plus a timeline bank of ~40 peristiwa penting (1400 Melaka → 1991 Wawasan 2020) used by the AI Sejarah Trainer and date-recall flashcards. Run phase17 migration first to enable the timeline table.'],
    ['key' => 'geografi_kssm',  'name' => 'KSSM Geografi (Tingkatan 4 + 5)',          'desc' => 'Seed the full KSSM SPM Geografi syllabus: 25 bab (T4: 15 — peta topografi, plat tektonik, batuan, luluhawa, sungai, ombak, taburan/pertumbuhan/migrasi penduduk, petempatan, urbanisasi; T5: 10 — graf, foto, cuaca/iklim, tumbuhan, sumber tenaga, kegiatan ekonomi, kesan terhadap alam sekitar) with subtopik + starter skills, plus a locations bank of ~40 ciri geografi (banjaran, sungai, tasik, plat, zon iklim, sumber tenaga, petempatan) used by the AI Geografi Trainer. Run phase18 migration first to enable the locations table.'],
    ['key' => 'pendidikan_islam_kssm', 'name' => 'KSSM Pendidikan Islam (Tingkatan 4 + 5)', 'desc' => 'Seed the full KSSM SPM Pendidikan Islam syllabus organised by enam bidang DSKP (Al-Quran, Hadis, Akidah, Fiqah, Sirah dan Tamadun Islam, Akhlak Islamiah) for both Tingkatan 4 dan 5 — 12 bidang total with pelajaran sebagai subtopik + starter kemahiran, plus a bank of ~20 ayat / hadis / doa lengkap dengan teks Arab, transliterasi, terjemahan dan tema (rasuah, tauhid, dakwah, kepimpinan, akhlak, istiqamah) yang digunakan oleh AI Pendidikan Islam Trainer dan flashcard hafazan. Run phase19 migration first to enable the ayat/hadis table.'],
    ['key' => 'pendidikan_moral_kssm', 'name' => 'KSSM Pendidikan Moral (Tingkatan 4 + 5)', 'desc' => 'Seed the full KSSM SPM Pendidikan Moral syllabus: 24 unit (T4: 12, T5: 12) merentas tiga bidang DSKP — Bidang 5 (Insan Bermoral), Bidang 6 (Jati Diri Moral), Bidang 7 (Moral dan Kenegaraan) — dengan subtopik + starter kemahiran, plus a 18-Nilai Utama bank (Kepercayaan kepada Tuhan, Baik hati, Bertanggungjawab, Hormat, Kasih sayang, Keadilan, Kebebasan, Keberanian, Kejujuran, Kerajinan, Kerjasama, Kesederhanaan, Toleransi, Patriotisme, Rasional, dll.) dengan takrifan, contoh, kata kunci dan kategori. Run phase20 migration first to enable the nilai table.'],
    ['key' => 'perakaunan_kssm', 'name' => 'KSSM Prinsip Perakaunan (Tingkatan 4 + 5)', 'desc' => 'Seed the full KSSM SPM Prinsip Perakaunan syllabus: 16 bab (T4: 9 — Pengenalan, Klasifikasi & Persamaan, Dokumen Perniagaan, Buku Catatan Pertama, Lejar, Imbangan Duga, Penyata Kewangan Milikan Tunggal, Pelarasan, Pembetulan Kesilapan; T5: 7 — Analisis Penyata Kewangan, Rekod Tak Lengkap, Kawalan Dalaman, Perkongsian, Syarikat Berhad, Kelab dan Persatuan, Perakaunan Kos) with subtopik + starter kemahiran, plus a formula/nisbah bank (~25 entries — persamaan perakaunan, kos jualan, untung kasar/bersih, susut nilai garis lurus & baki berkurangan, margin untung, ROCE, nisbah semasa & cepat, pusing ganti inventori, faedah perkongsian, dividen syer, titik pulang modal, margin caruman). Run phase21 migration first to enable the formula table.'],
    ['key' => 'perniagaan_kssm', 'name' => 'KSSM Perniagaan (Tingkatan 4 + 5)', 'desc' => 'Seed the full KSSM SPM Perniagaan syllabus: 11 bab (T4: 4 — Tujuan & Pemilikan, Trend Semasa, Visi/Misi/Objektif, Bahagian Fungsian; T5: 7 — Pengurusan Sumber Manusia, Sumber Fizikal/Teknologi, Sumber Pembiayaan, Penyata Kewangan, Persediaan Usahawan, Memulakan Perniagaan, Merancang Pengendalian) with subtopik + starter kemahiran, plus a starter istilah perniagaan bank (~30 entries — pemilikan tunggal/perkongsian/Sdn Bhd/Bhd/koperasi, e-dagang, gig economy, SMART, 4P, SSM, modal teroka, crowdfunding, usahawan, rancangan perniagaan, dll.). Run phase22 migration first to enable the istilah table.'],
    ['key' => 'ekonomi_kssm', 'name' => 'KSSM Ekonomi (Tingkatan 4 + 5)', 'desc' => 'Seed the full KSSM SPM Ekonomi syllabus: 11 topik (T4: 4 — Pengenalan kepada Ekonomi, Pasaran, Wang/Bank/Pendapatan, Pengeluaran; T5: 7 sub-topik — Peranan Kerajaan, Penunjuk Ekonomi, Alat Dasar Ekonomi, Globalisasi, Perdagangan Antarabangsa, Imbangan Pembayaran, Kadar Pertukaran Asing) with subtopik + starter kemahiran, plus a Ekonomi concepts bank (~40 entries gabungan istilah + formula — kelangkaan, kos lepas, hukum permintaan/penawaran, keanjalan harga Ed/Es, faktor pengeluaran, AR/MR/MC, IHP, kadar inflasi, kadar pengangguran, KDNK, dasar fiskal/kewangan, cukai progresif/regresif, tarif/kuota/embargo, akaun semasa, devaluation/depreciation, dll.). Run phase23 migration first to enable the concepts table.'],
    ['key' => 'sains_komputer_kssm', 'name' => 'KSSM Sains Komputer (Tingkatan 4 + 5)', 'desc' => 'Seed the full KSSM SPM Sains Komputer syllabus: 21 topik (T4: 13 — Strategi Penyelesaian Masalah, Algoritma, Pemboleh Ubah & Jenis Data, Struktur Kawalan, Amalan Terbaik, Modular & Struktur Data, Pembangunan Aplikasi, Pangkalan Data Hubungan, Reka Bentuk PD, Pembangunan PD, Sistem PD, Reka Bentuk Interaksi, Paparan & Reka Bentuk Skrin; T5: 8 — Komputer & Impak, Seni Bina Komputer, Get Logik, Masa Hadapan Pengkomputeran, Pangkalan Data Lanjutan SQL, Bahasa Penskripan Klien, Bahasa Penskripan Pelayan, Laman Web Interaktif) with subtopik + starter kemahiran, plus a code-snippets + concepts bank (~40 entries — Python (if/else, loops, functions, lists), SQL (CREATE/INSERT/SELECT/UPDATE/DELETE/JOIN/GROUP BY), HTML/CSS/JavaScript, PHP (PDO MySQL, $_POST), penukaran binari/desimal, jadual kebenaran get logik AND/OR/NOT/XOR, keselamatan web (SQL injection, XSS, encryption), normalisasi 1NF/3NF). Run phase24 migration first to enable the concepts table.'],
    ['key' => 'rbt_kssm', 'name' => 'KSSM RBT / Reka Cipta (Tingkatan 4 + 5)', 'desc' => 'Seed the SPM-level Reka Cipta syllabus under the rbt subject slug (full RBT is T1-T3 only — Reka Cipta is the MPEI elective at SPM level): 16 bab (T4: 8 Asas Reka Cipta — Pengenalan, Asas Reka Bentuk, Faktor Pemilihan, Pengenalpastian Masalah, Penyelidikan, Penjanaan Idea, Model Olokan, Lukisan Kerja; T5: 8 dalam 2 kluster — Teknologi Pembuatan (LTK/CAD, Bahan & Mesin, Sistem, Prototaip) + Strategi Pemasaran (Penjenamaan, Pemasaran, Harta Intelek, Pendokumentasian)) with subtopik + starter kemahiran, plus a starter konsep bank (~45 entries — invention/innovation/creativity, elemen reka bentuk, prinsip reka bentuk, ergonomik, SWOT, brainstorming, lukisan ortografik/isometrik/CAD, sistem pneumatik/hidraulik, prototaip, 4P, paten/cap dagangan/hak cipta, MyIPO, dll.). Run phase25 migration first to enable the concepts table.'],
    ['key' => 'psv_kssm', 'name' => 'KSSM Pendidikan Seni Visual (Tingkatan 4 + 5)', 'desc' => 'Seed the full KSSM SPM Pendidikan Seni Visual syllabus: 17 tajuk (T4: 9 — Alat Kebesaran & Perhiasan Diraja, Seni Lukisan, Seni Catan, Seni Cetakan, Reka Bentuk Landskap, Reka Bentuk Hiasan Dalaman, Seni Ukiran, Seni Reka Grafik, Seni Foto; T5: 8 — Seni Bina, Seni Lukisan T5, Seni Catan T5, Seni Arca, Reka Bentuk Industri, Seni Batik, Reka Grafik/Infografik, Seni Foto Manipulasi & e-Portfolio) merentas 5 bidang DSKP (Sejarah/Apresiasi, Seni Halus, Reka Bentuk, Seni Kraf, Komunikasi Visual) with subtopik + starter kemahiran. Plus a rujukan seni bank (~50 entries — tokoh tempatan (Syed Ahmad Jamal, Latiff Mohidin, Ibrahim Hussein) dan dunia (Van Gogh, Picasso, Monet, Dieter Rams, Jony Ive), teknik (impasto, glazing, stippling, casting, assemblage), istilah (warna primer/komplementari, perspektif, Rule of Thirds, leading lines), motif Melayu (awan larat, pucuk rebung), jenis batik canting/tjap/lukis, bangunan ikonik Malaysia). Run phase26 migration first to enable the references table.'],
    ['key' => 'sains_kssm', 'name' => 'KSSM Sains (Tingkatan 4 + 5)', 'desc' => 'Seed the full KSSM SPM Sains (integrated science, Arts stream) syllabus: 21 bab (T4: 12 — Langkah Keselamatan Makmal, Bantuan Kecemasan, Teknik Parameter Kesihatan, Teknologi Hijau, Genetik, Sokongan/Pergerakan/Pertumbuhan, Koordinasi Badan, Unsur & Bahan, Kimia Industri, Kimia dalam Perubatan, Daya & Gerakan, Tenaga Nuklear; T5: 9 — Mikroorganisma, Nutrisi & Teknologi Makanan, Kelestarian Alam, Kadar Tindak Balas, Sebatian Karbon, Elektrokimia, Cahaya & Optik, Daya & Tekanan, Teknologi Angkasa Lepas) with subtopik + starter kemahiran. Plus a campuran istilah + formula bank (~50 entries merentas biologi/kimia/fizik — CPR, BMI, DNA, Hukum Mendel, ikatan ionik/kovalen, Proses Sentuhan, Proses Haber, F=ma, Prinsip Archimedes/Bernoulli, isotop, vaksin, hujan asid, kadar tindak balas, alkana/alkena, polimer, elektrolisis, hukum Snell, persamaan kanta 1/f=1/u+1/v, P=ρgh, halaju lepas, Sheikh Muszaphar). Run phase27 migration first to enable the concepts table.'],
    ['key' => 'bahasa_cina_kssm', 'name' => 'KSSM Bahasa Cina (Tingkatan 4 + 5)', 'desc' => 'Seed the full KSSM SPM Bahasa Cina (华文 6351) syllabus organised by macro skills mirroring the BM seeder: 14 topik (T4: 7 — Pemahaman Bahasa Cina Moden, Pembacaan Wenyan/Klasik, Tatabahasa Asas, Karangan Pendek, Karangan Berpandu, Karangan Berformat, KOMSAS Moden; T5: 7 — Pemahaman Lanjutan, Terjemahan Wenyan, Ringkasan, Karangan Hujahan, Karangan Naratif/Emosi, Puisi Klasik, Karya Klasik Cina) with subtopik dwi-bahasa (Cina + BM) + starter kemahiran. Plus a 成语 chengyu and wenyan reference bank (~40 entries — 一举两得, 画蛇添足, 守株待兔, 亡羊补牢, 愚公移山, 滴水穿石, 锦上添花, 雪中送炭, 三思而行 etc.; kata tugas wenyan 之/而/以/其/也; tokoh sasterawan 李白/杜甫/王维/苏轼/孔子/孟子/鲁迅/冰心/老舍; karya klasik 论语/孟子/大学/中庸; puisi 静夜思/春望/登鹳雀楼) lengkap dengan pinyin, makna BM, makna Cina, contoh dan sumber. Run phase28 migration first to enable the references table.'],
    ['key' => 'bahasa_tamil_kssm', 'name' => 'KSSM Bahasa Tamil (Tingkatan 4 + 5)', 'desc' => 'Seed the full KSSM SPM Bahasa Tamil (தமிழ்மொழி) syllabus organised by macro skills mirip seeder BM/Bahasa Cina: 14 topik (T4: 7 — கேட்டல் பேசுதல், புரிதல், அடிப்படை இலக்கணம், சிறு கட்டுரை, வழிகாட்டப்பட்ட கட்டுரை, இலக்கியம் தொடக்கம், பழமொழி & மரபுத்தொடர்; T5: 7 — KBAT புரிதல், மேம்பட்ட இலக்கணம், வாதிடும் கட்டுரை, நிகழ்ச்சி/உணர்வுக் கட்டுரை, சுருக்க எழுத்து, திருக்குறள் & பாரம்பரிய இலக்கியம், நவீன இலக்கியம்) dengan subtopik dwi-bahasa Tamil + BM dan starter kemahiran. Plus a Tamil references bank (~25 entries — 8 திருக்குறள் kural daripada திருவள்ளுவர் lengkap dengan transliterasi rumi/makna BM/makna Tamil/sumber; 9 பழமொழி (peribahasa Tamil) termasuk yang dipopularkan ஒளவையார்; tokoh klasik (Thiruvalluvar, Avvaiyar) dan karya klasik (Silappathikaram, Manimekalai); tokoh moden (Bharathiyar, Bharathidasan, Pudhumaipithan, Kalki Krishnamurthy). Run phase29 migration first to enable the references table.'],
    ['key' => 'kepintaran_buatan_kssm', 'name' => 'Pioneer Asas Kepintaran Buatan (Tingkatan 4 + 5)', 'desc' => 'Seed the Pioneer Track "Asas Kepintaran Buatan" (AI Foundations) elective — LulusAI\'s proposed next-generation subject, not yet in the official KSSM DSKP. 20 bab (T4: 10 — Pengenalan AI, Data, Pemikiran Komputasi, ML Asas, Algoritma ML, Neural Networks 101, NLP, Computer Vision, Generative AI, Prompt Engineering; T5: 10 — Etika AI, Privasi & PDPA, Misinformation & Deepfakes, AI & Pekerjaan, AI dalam Ekonomi Malaysia, AI dalam Sektor Industri, No-Code AI, Coding dengan AI Assistant, Sustainability AI, Projek Capstone) dengan subtopik + starter kemahiran. Plus an AI concepts bank (~45 entries — istilah Narrow/General AI, Supervised/Unsupervised/RLHF, Decision Tree, k-NN, Neural Network, Backpropagation, Transformer, RAG, Hallucination, CNN, Diffusion Model, PDPA Malaysia, NAIRR, Edge AI, plus Python code snippets untuk train_test_split / DecisionTreeClassifier / Anthropic API call dan prompt patterns untuk chain-of-thought / anatomi prompt) dan AI timeline bank (~30 peristiwa — 1950 Turing Test → 1956 Dartmouth → 1957 Perceptron → 1986 Backpropagation → 1997 Deep Blue → 2012 AlexNet → 2014 GANs → 2017 Transformer → 2022 ChatGPT → 2023 Claude → 2024 EU AI Act + Malaysia NAIRR → 2025 reasoning models o1/Claude 3.7/DeepSeek R1). Run phase34 migration first to enable the ai_concepts and ai_timeline tables.'],
    ['key' => 'bahasa_arab_kssm', 'name' => 'KSSM Bahasa Arab (Tingkatan 4 + 5)', 'desc' => 'Seed the full KSSM SPM Bahasa Arab (اللغة العربية) syllabus organised by macro skills mirip seeder BM/Bahasa Cina/Tamil: 14 topik (T4: 7 — الاستماع والكلام, القراءة والفهم, النحو الأساسي, المفردات, الإنشاء القصير, الإنشاء الموجه, الثقافة العربية والإسلامية; T5: 7 — القراءة المتقدمة, النحو المتقدم (Marfu\'at/Mansubat/Majrurat, Kana wa Inna), الإنشاء الحجاجي, الإنشاء السردي, التلخيص, الأدب العربي, الحضارة الإسلامية) dengan subtopik dwi-bahasa Arab + BM dan starter kemahiran. Plus an Arabic references bank (~35 entries — 10 أمثال amthal/peribahasa Arab termasuk من جدّ وجد, الصبر مفتاح الفرج, إنّ مع العسر يسرا; istilah nahu (isim, fi\'l, harf, jumlah ismiyyah/fi\'liyyah, mubtada/khabar, Kana wa Akhwatuha, Inna wa Akhwatuha, Marfu\'at/Mansubat/Majrurat); mufradat tematik; tokoh sasterawan klasik (المتنبي) dan moden (أحمد شوقي, محمود درويش, نجيب محفوظ pemenang Nobel); cendekiawan Tamadun Islam (الخوارزمي bapa algebra, ابن سينا Avicenna, ابن خلدون bapa sosiologi, بيت الحكمة Baghdad). Run phase30 migration first to enable the references table.'],
    ['key' => 'demo_data',      'name' => 'Demo data (rich)',     'desc' => 'Populate sample students with attempts, subscriptions, a class, a Snap & Check marking and parent reports so dashboards look alive.', 'has_force' => true],
    ['key' => 'weekly_reports', 'name' => 'Generate weekly reports','desc' => 'Generate this week\'s AI parent report for every active student.'],
    ['key' => 'reminders',      'name' => 'Send study reminders', 'desc' => 'Notify students who have not practised yet today (in-app + WhatsApp if configured).'],
];

admin_layout_start('Seeders', $admin, 'seeders.php');
?>
<p class="muted">Run the maintenance / seed scripts from here. These are the same scripts in <code>cron/</code> — they still work from SSH or a scheduled Hostinger cron job.</p>

<?php if ($output): ?>
  <div class="flash flash--<?= $output['kind'] === 'error' ? 'error' : ($output['kind'] === 'info' ? 'info' : 'success') ?>">
    <strong><?= e($output['title']) ?></strong>
    <div style="margin-top:6px;font-family:monospace;font-size:13px;white-space:pre-wrap"><?= e(implode("\n", $output['lines'])) ?></div>
  </div>
<?php endif; ?>

<?php foreach ($seeders as $s): ?>
  <div class="card" style="margin-bottom:14px">
    <h3 style="margin-top:0"><?= e($s['name']) ?></h3>
    <p class="muted"><?= e($s['desc']) ?></p>
    <form method="post" style="display:flex;gap:8px;flex-wrap:wrap;align-items:center">
      <?= csrf_field() ?>
      <input type="hidden" name="which" value="<?= e($s['key']) ?>">
      <?php if (!empty($s['has_force'])): ?>
        <label class="muted" style="display:inline-flex;gap:6px;align-items:center;font-size:13px">
          <input type="checkbox" name="force" value="1"> Force re-run (ignore "already seeded" marker)
        </label>
      <?php endif; ?>
      <button class="btn" type="submit">Run</button>
    </form>
  </div>
<?php endforeach; ?>

<p class="muted" style="font-size:13px;margin-top:18px">
  CLI usage (e.g. Hostinger SSH or scheduled cron):
  <br><code>php public_html/cron/seed_courses.php</code>
  <br><code>php public_html/cron/seed_demo_logins.php</code>
  <br><code>php public_html/cron/seed_demo.php [--force]</code>
  <br><code>php public_html/cron/weekly_reports.php</code>
  <br><code>php public_html/cron/reminders.php</code>
</p>
<?php
admin_layout_end();
