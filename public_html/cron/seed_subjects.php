<?php
/**
 * SPM subjects seeder. Inserts the missing core + elective SPM subjects
 * alongside the seven already added by seed.sql (Mathematics, Add Maths,
 * Physics, Chemistry, Biology, English, Bahasa Melayu).
 *
 *   php public_html/cron/seed_subjects.php
 *
 * Idempotent — subjects already present for the SPM level (matched by
 * slug or name) are skipped.
 */
declare(strict_types=1);

require_once __DIR__ . '/../inc/db.php';

/** All SPM-level subjects we want available. */
function seed_subjects_catalog(): array
{
    return [
        // ---- Core (mandatory or near-mandatory) ----
        ['slug' => 'sejarah',          'name' => 'Sejarah',                            'icon' => 'history',   'description' => 'Compulsory pass subject for the SPM certificate.',     'sort_order' => 10],
        ['slug' => 'pendidikan-islam', 'name' => 'Pendidikan Islam',                   'icon' => 'mosque',    'description' => 'Core SPM subject for Muslim students.',                  'sort_order' => 11],
        ['slug' => 'pendidikan-moral', 'name' => 'Pendidikan Moral',                   'icon' => 'heart',     'description' => 'Core SPM subject for non-Muslim students.',              'sort_order' => 12],

        // ---- Commerce ----
        ['slug' => 'perakaunan',       'name' => 'Prinsip Perakaunan',                 'icon' => 'ledger',    'description' => 'Commerce elective — accounting principles.',             'sort_order' => 20],
        ['slug' => 'perniagaan',       'name' => 'Perniagaan',                         'icon' => 'briefcase', 'description' => 'Commerce elective — business studies.',                  'sort_order' => 21],
        ['slug' => 'ekonomi',          'name' => 'Ekonomi',                            'icon' => 'chart',     'description' => 'Commerce elective — economics.',                         'sort_order' => 22],

        // ---- Technology ----
        ['slug' => 'sains-komputer',   'name' => 'Sains Komputer',                     'icon' => 'laptop',    'description' => 'Technology elective — computer science.',                'sort_order' => 30],
        ['slug' => 'rbt',              'name' => 'Reka Bentuk dan Teknologi',          'icon' => 'tools',     'description' => 'Technology elective — design & technology (RBT).',       'sort_order' => 31],

        // ---- General / Arts ----
        ['slug' => 'sains',            'name' => 'Sains',                              'icon' => 'flask',     'description' => 'General science (Arts stream).',                         'sort_order' => 40],
        ['slug' => 'geografi',         'name' => 'Geografi',                           'icon' => 'globe',     'description' => 'Geography.',                                             'sort_order' => 41],
        ['slug' => 'psv',              'name' => 'Pendidikan Seni Visual',             'icon' => 'palette',   'description' => 'Visual arts education (PSV).',                           'sort_order' => 42],

        // ---- Languages ----
        ['slug' => 'bahasa-cina',      'name' => 'Bahasa Cina',                        'icon' => 'language',  'description' => 'Chinese language.',                                      'sort_order' => 50],
        ['slug' => 'bahasa-tamil',     'name' => 'Bahasa Tamil',                       'icon' => 'language',  'description' => 'Tamil language.',                                        'sort_order' => 51],
        ['slug' => 'bahasa-arab',      'name' => 'Bahasa Arab',                        'icon' => 'language',  'description' => 'Arabic language.',                                       'sort_order' => 52],

        // ---- Religious electives ----
        ['slug' => 'tasawwur-islam',   'name' => 'Tasawwur Islam',                     'icon' => 'mosque',    'description' => 'Islamic worldview elective.',                            'sort_order' => 60],
        ['slug' => 'pqs',              'name' => 'Pendidikan Al-Quran dan Al-Sunnah',  'icon' => 'mosque',    'description' => 'Al-Quran and Al-Sunnah studies (PQS).',                  'sort_order' => 61],
        ['slug' => 'psi',              'name' => 'Pendidikan Syariah Islamiah',        'icon' => 'mosque',    'description' => 'Islamic Shariah studies (PSI).',                         'sort_order' => 62],

        // ---- Pioneer Track: AI literacy elective (not yet in KSSM DSKP) ----
        ['slug' => 'kepintaran-buatan','name' => 'Asas Kepintaran Buatan',             'icon' => 'cpu',       'description' => 'Pioneer AI literacy elective — AI fundamentals, prompt engineering, ethics, generative AI.', 'sort_order' => 70],
    ];
}

/** The 12 LulusAI MVP subjects (used to surface which were just added). */
function seed_subjects_mvp(): array
{
    return [
        // already seeded by seed.sql
        'mathematics', 'add-maths', 'physics', 'chemistry', 'biology',
        'english', 'bahasa-melayu',
        // added here
        'sejarah', 'pendidikan-islam', 'pendidikan-moral',
        'perniagaan', 'perakaunan',
    ];
}

function seed_subjects_run(): array
{
    $level = db_one("SELECT id FROM education_levels WHERE slug = 'spm' LIMIT 1");
    if (!$level) {
        return ['status' => 'no_level', 'message' => 'SPM education level not found — run the main installer first.', 'created' => 0, 'kept' => 0];
    }
    $spm = (int) $level['id'];
    $created = 0;
    $kept    = 0;

    foreach (seed_subjects_catalog() as $s) {
        $existing = db_one(
            'SELECT id FROM subjects WHERE education_level_id = ? AND (slug = ? OR name = ?) LIMIT 1',
            [$spm, $s['slug'], $s['name']]
        );
        if ($existing) {
            $kept++;
            continue;
        }
        db_exec(
            'INSERT INTO subjects (education_level_id, name, slug, icon, description, sort_order, status)
             VALUES (?,?,?,?,?,?,?)',
            [$spm, $s['name'], $s['slug'], $s['icon'] ?? null, $s['description'] ?? null, (int) ($s['sort_order'] ?? 0), 'active']
        );
        $created++;
    }

    return [
        'status'  => 'ok',
        'created' => $created,
        'kept'    => $kept,
        'total'   => count(seed_subjects_catalog()),
    ];
}

if (PHP_SAPI === 'cli') {
    $r = seed_subjects_run();
    if (($r['status'] ?? '') === 'no_level') {
        fwrite(STDERR, $r['message'] . "\n");
        exit(1);
    }
    echo "SPM subjects — created: {$r['created']}, kept (already present): {$r['kept']}, catalog: {$r['total']}\n";
}
