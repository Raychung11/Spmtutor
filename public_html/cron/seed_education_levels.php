<?php
/**
 * Education levels catalog seeder.
 *
 * Idempotent — inserts any missing education levels and re-syncs the
 * name/sort_order for existing ones. Existing user profiles keep their
 * education_level_id — we only add to the catalog.
 *
 * Adds IGCSE (Cambridge International General Certificate of Secondary
 * Education) alongside the Malaysian ladder so LulusAI can serve
 * international students on the same platform. Curriculum content for
 * IGCSE is deliberately not seeded on this pass — scaffold only.
 *
 *   php public_html/cron/seed_education_levels.php
 */
declare(strict_types=1);

require_once __DIR__ . '/../inc/db.php';

function education_levels_catalog(): array
{
    return [
        // Malaysian ladder
        ['slug' => 'upsr',  'name' => 'UPSR',  'sort_order' => 10],
        ['slug' => 'pt3',   'name' => 'PT3',   'sort_order' => 20],
        ['slug' => 'spm',   'name' => 'SPM',   'sort_order' => 30],
        ['slug' => 'stpm',  'name' => 'STPM',  'sort_order' => 40],
        // International — added Phase 36. Curriculum content still TBD.
        ['slug' => 'igcse', 'name' => 'IGCSE', 'sort_order' => 50],
    ];
}

function seed_education_levels_run(): array
{
    $created = 0;
    $kept = 0;
    foreach (education_levels_catalog() as $l) {
        $existing = db_one('SELECT id FROM education_levels WHERE slug = ? LIMIT 1', [$l['slug']]);
        if ($existing) {
            db_exec(
                'UPDATE education_levels SET name = ?, sort_order = ?, status = "active" WHERE id = ?',
                [$l['name'], (int) $l['sort_order'], (int) $existing['id']]
            );
            $kept++;
        } else {
            db_exec(
                'INSERT INTO education_levels (name, slug, sort_order, status) VALUES (?,?,?, "active")',
                [$l['name'], $l['slug'], (int) $l['sort_order']]
            );
            $created++;
        }
    }
    return ['status' => 'ok', 'created' => $created, 'kept' => $kept, 'total' => count(education_levels_catalog())];
}

if (PHP_SAPI === 'cli') {
    $r = seed_education_levels_run();
    echo "Education levels — created: {$r['created']}, kept: {$r['kept']}, catalog: {$r['total']}\n";
}
