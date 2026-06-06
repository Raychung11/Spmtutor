<?php
/**
 * Klang Valley schools seeder. Inserts ~100 SMK schools into the schools
 * table, plus marks the top-20 priority targets (their names also exist
 * in the main list so duplicates are skipped). All schools are inserted
 * as type=school, status=active, with no owner yet.
 *
 *   php public_html/cron/seed_schools.php
 *
 * Idempotent — already-present school names are skipped.
 */
declare(strict_types=1);

require_once __DIR__ . '/../inc/db.php';

function seed_schools_catalog(): array
{
    return [
        // 1-30 Kuala Lumpur
        'SMK Victoria', 'SMK St. John', 'SMK Cochrane', 'SMK Convent Bukit Nanas',
        'SMK Seri Bintang Selatan', 'SMK Seri Bintang Utara', 'SMK Taman Connaught',
        'SMK Cheras', 'SMK Bandar Tun Razak', 'SMK Desa Petaling',
        'SMK Bukit Jalil', 'SMK Wangsa Maju Seksyen 2', 'SMK Wangsa Maju Seksyen 4',
        'SMK Setapak Indah', 'SMK Danau Kota', 'SMK Taman Melati',
        'SMK Aminuddin Baki', 'SMK Seri Sentosa', 'SMK Bandar Baru Sentul',
        'SMK Kepong Baru', 'SMK Menjalara', 'SMK Kepong',
        'SMK Jinjang', 'SMK Batu Muda', 'SMK Segambut', 'SMK Taman Maluri',
        'SMK Maxwell', 'SMK Teknik Kuala Lumpur', 'SMK Methodist (ACS) Kuala Lumpur',
        'SMK La Salle Brickfields',

        // 31-50 Petaling Jaya / Subang
        'SMK Seri Hartamas', 'SMK Bukit Bandaraya', 'SMK Sri Permata', 'SMK Sri Aman',
        'SMK Assunta', 'SMK Taman SEA', 'SMK Damansara Jaya', 'SMK Damansara Utama',
        'SMK Damansara Perdana', 'SMK Kelana Jaya', 'SMK Bandar Utama Damansara 4',
        'SMK Bandar Utama Damansara', 'SMK Tropicana', 'SMK Lembah Subang',
        'SMK Sultan Abdul Samad', 'SMK Taman Medan', 'SMK Seksyen 10 Kota Damansara',
        'SMK Kota Damansara', 'SMK Subang Utama', 'SMK USJ 4',

        // 51-65 Subang / Shah Alam
        'SMK USJ 13', 'SMK Seafield', 'SMK Subang Bestari',
        'SMK Shah Alam', 'SMK Seksyen 7 Shah Alam', 'SMK Seksyen 9 Shah Alam',
        'SMK Seksyen 18 Shah Alam', 'SMK Bukit Jelutong', 'SMK Kota Kemuning',
        'SMK Alam Megah', 'SMK TTDI Jaya',
        'SMK Raja Mahadi', 'SMK Sultan Sulaiman', 'SMK Tengku Ampuan Rahimah',
        'SMK Tinggi Klang',

        // 66-74 Klang area
        'SMK Meru', 'SMK Bukit Tinggi', 'SMK Kapar', 'SMK Rantau Panjang',
        'SMK Bandar Baru Klang', 'SMK Pandamaran Jaya', 'SMK Jalan Kebun',
        'SMK Sungai Kapar Indah', 'SMK Saujana Utama',

        // 75-84 Puchong / Cyberjaya / Putrajaya
        'SMK Puchong Perdana', 'SMK Puchong Utama (1)', 'SMK Puchong Utama (2)',
        'SMK Pusat Bandar Puchong 1', 'SMK Pusat Bandar Puchong',
        'SMK Cyberjaya', 'SMK Putrajaya Presint 8(1)', 'SMK Putrajaya Presint 9(1)',
        'SMK Putrajaya Presint 11(1)', 'SMK Putrajaya Presint 14(1)',

        // 85-100 Kajang / Bangi / Cheras south / Sungai Besi etc
        'SMK Jalan Reko', 'SMK Kajang', 'SMK Tinggi Kajang', 'SMK Convent Kajang',
        'SMK Bandar Baru Bangi', 'SMK Jalan Tiga', 'SMK Seksyen 4 Bandar Kinrara',
        'SMK Bandar Kinrara', 'SMK Taman Yarl', 'SMK Seri Kembangan', 'SMK Seri Indah',
        'SMK Bandar Sunway', 'SMK Sungai Besi', 'SMK Taman Seraya', 'SMK Hillcrest',
        'SMK Taman Kosas',
    ];
}

/** Top-20 SPM / population priority targets. */
function seed_schools_priority(): array
{
    return [
        'SMK Bandar Tun Razak', 'SMK Aminuddin Baki', 'SMK Cochrane',
        'SMK Taman Connaught', 'SMK Cheras', 'SMK Bukit Jalil',
        'SMK Seri Bintang Selatan', 'SMK Bandar Baru Bangi', 'SMK Jalan Reko',
        'SMK Kajang', 'SMK Cyberjaya', 'SMK Putrajaya Presint 8(1)',
        'SMK Puchong Utama (1)', 'SMK Puchong Perdana', 'SMK Kota Damansara',
        'SMK Damansara Utama', 'SMK Kelana Jaya', 'SMK Shah Alam',
        'SMK Tinggi Klang', 'SMK Sultan Abdul Samad',
    ];
}

function seed_schools_run(): array
{
    $catalog  = seed_schools_catalog();
    $priority = array_fill_keys(seed_schools_priority(), true);

    $created = 0;
    $kept    = 0;
    $createdPriority = 0;

    foreach ($catalog as $name) {
        $existing = db_one('SELECT id FROM schools WHERE name = ?', [$name]);
        if ($existing) {
            $kept++;
            continue;
        }
        db_exec(
            'INSERT INTO schools (name, type, status) VALUES (?, ?, ?)',
            [$name, 'school', 'active']
        );
        $created++;
        if (isset($priority[$name])) {
            $createdPriority++;
        }
    }

    return [
        'created'  => $created,
        'kept'     => $kept,
        'total'    => count($catalog),
        'priority' => count($priority),
        'priority_created' => $createdPriority,
    ];
}

if (PHP_SAPI === 'cli') {
    $r = seed_schools_run();
    echo "Schools seeded — created: {$r['created']}, already present: {$r['kept']}, total in catalog: {$r['total']}\n";
    echo "Priority targets in catalog: {$r['priority']} (of which {$r['priority_created']} were just created).\n";
}
