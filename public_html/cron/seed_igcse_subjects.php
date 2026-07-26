<?php
/**
 * IGCSE (Cambridge International) subjects seeder.
 *
 * Seeds the 18 most-offered IGCSE subjects across Mathematics, English,
 * Languages, Sciences, Humanities, Technology, Arts, and Religious
 * studies. Cambridge syllabus codes are included in each description
 * so teachers/parents can cross-reference the official Cambridge
 * syllabus PDFs.
 *
 * Idempotent — subjects already present for the IGCSE level (matched
 * by slug or name) are skipped. Auto-bootstraps the IGCSE
 * education_level row if it doesn't exist yet.
 *
 *   php public_html/cron/seed_igcse_subjects.php
 */
declare(strict_types=1);

require_once __DIR__ . '/../inc/db.php';

function igcse_subjects_catalog(): array
{
    return [
        // ---- Mathematics ----
        ['slug' => 'igcse-mathematics',       'name' => 'Mathematics',              'icon' => 'sigma',    'description' => 'Cambridge IGCSE Mathematics (0580) — core & extended tiers.',                            'sort_order' => 10],
        ['slug' => 'igcse-add-maths',         'name' => 'Additional Mathematics',   'icon' => 'sigma',    'description' => 'Cambridge IGCSE Additional Mathematics (0606) — bridges to A-Level Maths.',              'sort_order' => 11],

        // ---- English ----
        ['slug' => 'igcse-english-first',     'name' => 'English — First Language', 'icon' => 'book',     'description' => 'Cambridge IGCSE First Language English (0500) — reading, writing, directed response.', 'sort_order' => 20],
        ['slug' => 'igcse-english-second',    'name' => 'English — Second Language','icon' => 'book',     'description' => 'Cambridge IGCSE English as a Second Language (0510 / 0511 speaking endorsement).',      'sort_order' => 21],
        ['slug' => 'igcse-english-lit',       'name' => 'English Literature',       'icon' => 'book',     'description' => 'Cambridge IGCSE Literature in English (0475) — poetry, prose, drama.',                  'sort_order' => 22],

        // ---- Sciences ----
        ['slug' => 'igcse-biology',           'name' => 'Biology',                  'icon' => 'leaf',     'description' => 'Cambridge IGCSE Biology (0610) — cells, physiology, ecology, genetics.',                'sort_order' => 30],
        ['slug' => 'igcse-chemistry',         'name' => 'Chemistry',                'icon' => 'flask',    'description' => 'Cambridge IGCSE Chemistry (0620) — bonding, moles, organic, redox, industry.',          'sort_order' => 31],
        ['slug' => 'igcse-physics',           'name' => 'Physics',                  'icon' => 'atom',     'description' => 'Cambridge IGCSE Physics (0625) — motion, waves, electricity, atomic, thermal.',         'sort_order' => 32],
        ['slug' => 'igcse-combined-science',  'name' => 'Combined Science',         'icon' => 'flask',    'description' => 'Cambridge IGCSE Combined Science (0653) — single-award science for broad electives.',   'sort_order' => 33],

        // ---- Humanities ----
        ['slug' => 'igcse-history',           'name' => 'History',                  'icon' => 'history',  'description' => 'Cambridge IGCSE History (0470) — 20th-century international relations + depth study.',   'sort_order' => 40],
        ['slug' => 'igcse-geography',         'name' => 'Geography',                'icon' => 'globe',    'description' => 'Cambridge IGCSE Geography (0460) — population, environment, economic development.',      'sort_order' => 41],
        ['slug' => 'igcse-economics',         'name' => 'Economics',                'icon' => 'chart',    'description' => 'Cambridge IGCSE Economics (0455) — micro, macro, international, developing economies.',  'sort_order' => 42],
        ['slug' => 'igcse-business-studies',  'name' => 'Business Studies',         'icon' => 'briefcase','description' => 'Cambridge IGCSE Business Studies (0450) — enterprise, HR, marketing, finance, ops.',    'sort_order' => 43],

        // ---- Technology ----
        ['slug' => 'igcse-computer-science',  'name' => 'Computer Science',         'icon' => 'laptop',   'description' => 'Cambridge IGCSE Computer Science (0478) — algorithms, programming, hardware, networks.','sort_order' => 50],
        ['slug' => 'igcse-ict',               'name' => 'Information & Communication Technology', 'icon' => 'laptop', 'description' => 'Cambridge IGCSE ICT (0417) — practical + theory across office suites, web, DTP.',  'sort_order' => 51],

        // ---- Arts & Global ----
        ['slug' => 'igcse-art-design',        'name' => 'Art & Design',             'icon' => 'palette',  'description' => 'Cambridge IGCSE Art & Design (0400) — observational drawing, mixed-media portfolio.',    'sort_order' => 60],
        ['slug' => 'igcse-global-perspectives','name' => 'Global Perspectives',     'icon' => 'globe',    'description' => 'Cambridge IGCSE Global Perspectives (0457) — research, teamwork, critical thinking.',    'sort_order' => 61],

        // ---- Religious ----
        ['slug' => 'igcse-islamiyat',         'name' => 'Islamiyat',                'icon' => 'mosque',   'description' => 'Cambridge IGCSE Islamiyat (0493) — Quran, Hadith, life of the Prophet, Islamic history.','sort_order' => 70],
    ];
}

function seed_igcse_subjects_run(): array
{
    // Bootstrap the IGCSE education level if it doesn't exist yet, so
    // this seeder is fully standalone.
    $level = db_one("SELECT id FROM education_levels WHERE slug = 'igcse' LIMIT 1");
    if (!$level) {
        db_exec(
            'INSERT INTO education_levels (name, slug, sort_order, status) VALUES (?,?,?, "active")',
            ['IGCSE', 'igcse', 50]
        );
        $level = db_one("SELECT id FROM education_levels WHERE slug = 'igcse' LIMIT 1");
    }
    $lid = (int) $level['id'];
    $levelAutoCreated = false;
    // A level with 0 subjects and just created = auto_created signal for admin UI.
    if (!db_one('SELECT id FROM subjects WHERE education_level_id = ? LIMIT 1', [$lid])) {
        $levelAutoCreated = true;
    }

    $created = 0;
    $kept    = 0;
    foreach (igcse_subjects_catalog() as $s) {
        $existing = db_one(
            'SELECT id FROM subjects WHERE education_level_id = ? AND (slug = ? OR name = ?) LIMIT 1',
            [$lid, $s['slug'], $s['name']]
        );
        if ($existing) {
            $kept++;
            continue;
        }
        db_exec(
            'INSERT INTO subjects (education_level_id, name, slug, icon, description, sort_order, status)
             VALUES (?,?,?,?,?,?,?)',
            [$lid, $s['name'], $s['slug'], $s['icon'] ?? null, $s['description'] ?? null, (int) $s['sort_order'], 'active']
        );
        $created++;
    }

    return [
        'status'             => 'ok',
        'level_auto_created' => $levelAutoCreated,
        'created'            => $created,
        'kept'               => $kept,
        'total'              => count(igcse_subjects_catalog()),
    ];
}

if (PHP_SAPI === 'cli') {
    $r = seed_igcse_subjects_run();
    if ($r['level_auto_created']) {
        echo "IGCSE education level auto-created.\n";
    }
    echo "IGCSE subjects — created: {$r['created']}, kept: {$r['kept']}, catalog: {$r['total']}\n";
}
