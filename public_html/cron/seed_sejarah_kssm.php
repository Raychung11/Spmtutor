<?php
/**
 * KSSM SPM Sejarah syllabus seeder. Form 4 (10 bab) + Form 5 (10 bab) -
 * 20 chapters total, with subtopics + starter skills, plus a timeline
 * bank of ~40 significant Malaysian historical dates and events used
 * by the AI Sejarah Trainer and date-recall flashcards.
 *
 * Idempotent: topics matched by (subject_id, name, form_level); legacy
 * same-named NULL-form topics are adopted.
 *
 *   php public_html/cron/seed_sejarah_kssm.php
 */
declare(strict_types=1);

require_once __DIR__ . '/../inc/db.php';

function sejarah_kssm_catalog(): array
{
    return [
        // ============= TINGKATAN 4 (Tema: Pembinaan Negara) =============
        ['form' => 4, 'name' => 'Warisan Negara Bangsa', 'subtopics' => [
            'Konsep Negara Bangsa', 'Ciri-ciri Negara Bangsa', 'Kesultanan Melayu Melaka',
            'Sistem Pemerintahan Tradisional', 'Adat dan Undang-undang',
        ], 'skills' => [
            ['Mentakrifkan konsep negara bangsa', 'easy'],
            ['Menghuraikan ciri negara bangsa', 'medium'],
            ['Membandingkan sistem pentadbiran tradisional', 'medium'],
        ]],
        ['form' => 4, 'name' => 'Kebangkitan Nasionalisme', 'subtopics' => [
            'Faktor Kebangkitan Nasionalisme', 'Pengaruh Luar', 'Gerakan Islah',
            'Akhbar dan Majalah', 'Pertubuhan Politik Awal',
        ], 'skills' => [
            ['Menganalisis faktor kebangkitan nasionalisme', 'medium'],
            ['Menilai peranan akhbar dan majalah', 'medium'],
            ['Membincangkan tokoh nasionalis', 'medium'],
        ]],
        ['form' => 4, 'name' => 'Konflik Dunia dan Pendudukan Jepun di Negara Kita', 'subtopics' => [
            'Perang Dunia Pertama', 'Perang Dunia Kedua', 'Pendudukan Jepun di Tanah Melayu',
            'Kesan Pendudukan Jepun', 'Gerakan Anti-Jepun',
        ], 'skills' => [
            ['Menjelaskan sebab perang dunia', 'medium'],
            ['Menghuraikan kesan pendudukan Jepun', 'medium'],
            ['Menilai gerakan tentangan rakyat', 'medium'],
        ]],
        ['form' => 4, 'name' => 'Era Peralihan Kuasa British di Negara Kita', 'subtopics' => [
            'Pentadbiran Tentera British (BMA)', 'Malayan Union',
            'Penentangan Malayan Union', 'Tokoh Penentangan',
        ], 'skills' => [
            ['Menganalisis Malayan Union', 'medium'],
            ['Menilai penentangan rakyat', 'medium'],
            ['Menjelaskan peranan tokoh penentangan', 'medium'],
        ]],
        ['form' => 4, 'name' => 'Persekutuan Tanah Melayu 1948', 'subtopics' => [
            'Pembentukan Persekutuan', 'Ciri-ciri Persekutuan',
            'Suruhanjaya Hubungan Antara Kaum', 'Sistem Pemerintahan',
        ], 'skills' => [
            ['Menjelaskan pembentukan Persekutuan 1948', 'medium'],
            ['Membandingkan Malayan Union dan Persekutuan', 'medium'],
        ]],
        ['form' => 4, 'name' => 'Ancaman Komunis dan Perisytiharan Darurat', 'subtopics' => [
            'Parti Komunis Malaya (PKM)', 'Pengisytiharan Darurat 1948',
            'Rancangan Briggs', 'Pemimpin Komunis', 'Penyelesaian Darurat',
        ], 'skills' => [
            ['Menjelaskan ancaman komunis', 'medium'],
            ['Menilai strategi British menghadapi komunis', 'medium'],
        ]],
        ['form' => 4, 'name' => 'Usaha ke Arah Kemerdekaan', 'subtopics' => [
            'Sistem Ahli', 'Konsep Kerjasama Kaum', 'Persidangan Kebangsaan',
            'Rundingan Merdeka', 'Tokoh Kemerdekaan',
        ], 'skills' => [
            ['Menghuraikan rundingan kemerdekaan', 'medium'],
            ['Menilai peranan tokoh kemerdekaan', 'medium'],
        ]],
        ['form' => 4, 'name' => 'Pilihan Raya', 'subtopics' => [
            'Pilihan Raya Majlis Bandaran 1952', 'Pilihan Raya Umum 1955',
            'Parti Perikatan', 'Pakatan Politik',
        ], 'skills' => [
            ['Menjelaskan kepentingan pilihan raya', 'medium'],
            ['Menganalisis pakatan politik', 'medium'],
        ]],
        ['form' => 4, 'name' => 'Perlembagaan Persekutuan Tanah Melayu 1957', 'subtopics' => [
            'Suruhanjaya Reid', 'Cadangan Suruhanjaya',
            'Kontrak Sosial', 'Penggubalan Perlembagaan',
        ], 'skills' => [
            ['Menjelaskan kandungan perlembagaan', 'medium'],
            ['Menilai kontrak sosial', 'hard'],
        ]],
        ['form' => 4, 'name' => 'Pemasyhuran Kemerdekaan', 'subtopics' => [
            'Persiapan Kemerdekaan', 'Upacara Pemasyhuran',
            'Tunku Abdul Rahman', 'Implikasi Kemerdekaan',
        ], 'skills' => [
            ['Menjelaskan upacara pemasyhuran kemerdekaan', 'easy'],
            ['Menilai sumbangan Tunku Abdul Rahman', 'medium'],
        ]],

        // ============= TINGKATAN 5 (Tema: Malaysia dan Masa Hadapan) =============
        ['form' => 5, 'name' => 'Kedaulatan Negara', 'subtopics' => [
            'Konsep Kedaulatan', 'Lambang Kedaulatan', 'Yang di-Pertuan Agong',
            'Bendera dan Lagu Kebangsaan', 'Bahasa Kebangsaan',
        ], 'skills' => [
            ['Menjelaskan konsep kedaulatan', 'medium'],
            ['Menghuraikan lambang negara', 'easy'],
            ['Menilai peranan Yang di-Pertuan Agong', 'medium'],
        ]],
        ['form' => 5, 'name' => 'Perlembagaan Persekutuan', 'subtopics' => [
            'Konsep Perlembagaan', 'Ciri Perlembagaan', 'Perkara Asas',
            'Pindaan Perlembagaan', 'Kepentingan Perlembagaan',
        ], 'skills' => [
            ['Menganalisis perlembagaan persekutuan', 'medium'],
            ['Menjelaskan Perkara 152, 153 dan 181', 'hard'],
            ['Menilai pindaan perlembagaan', 'medium'],
        ]],
        ['form' => 5, 'name' => 'Raja Berperlembagaan dan Demokrasi Berparlimen', 'subtopics' => [
            'Sistem Raja Berperlembagaan', 'Pembahagian Kuasa',
            'Parlimen', 'Badan Eksekutif', 'Badan Kehakiman',
        ], 'skills' => [
            ['Menjelaskan demokrasi berparlimen', 'medium'],
            ['Membandingkan badan eksekutif dan kehakiman', 'medium'],
        ]],
        ['form' => 5, 'name' => 'Sistem Persekutuan', 'subtopics' => [
            'Konsep Persekutuan', 'Kerajaan Persekutuan vs Negeri',
            'Pembahagian Kuasa', 'Mahkamah Persekutuan',
        ], 'skills' => [
            ['Menjelaskan sistem persekutuan', 'medium'],
            ['Menilai pembahagian kuasa antara persekutuan dan negeri', 'medium'],
        ]],
        ['form' => 5, 'name' => 'Pembentukan Malaysia', 'subtopics' => [
            'Cadangan Pembentukan Malaysia', 'Suruhanjaya Cobbold',
            'Jawatankuasa Antara Kerajaan', 'Perjanjian Malaysia 1963',
            'Sabah, Sarawak dan Singapura',
        ], 'skills' => [
            ['Menjelaskan pembentukan Malaysia', 'medium'],
            ['Menilai peranan Suruhanjaya Cobbold', 'medium'],
        ]],
        ['form' => 5, 'name' => 'Cabaran Selepas Pembentukan Malaysia', 'subtopics' => [
            'Konfrontasi Indonesia', 'Tuntutan Filipina ke atas Sabah',
            'Pengunduran Singapura', 'Peristiwa 13 Mei 1969',
        ], 'skills' => [
            ['Menjelaskan cabaran selepas pembentukan Malaysia', 'medium'],
            ['Menilai penyelesaian konflik', 'medium'],
        ]],
        ['form' => 5, 'name' => 'Membina Kesejahteraan Negara', 'subtopics' => [
            'MAGERAN', 'Rukun Negara', 'Dasar Pendidikan Kebangsaan',
            'Dasar Kebudayaan Kebangsaan', 'Wawasan 2020',
        ], 'skills' => [
            ['Menjelaskan Rukun Negara', 'easy'],
            ['Menilai dasar pendidikan dan kebudayaan', 'medium'],
        ]],
        ['form' => 5, 'name' => 'Membina Kemakmuran Negara', 'subtopics' => [
            'Dasar Ekonomi Baru (DEB)', 'Rancangan Malaysia',
            'Dasar Pembangunan Nasional', 'Dasar Industri Berat',
            'Wawasan Kemakmuran',
        ], 'skills' => [
            ['Menganalisis Dasar Ekonomi Baru', 'medium'],
            ['Menilai dasar ekonomi negara', 'medium'],
        ]],
        ['form' => 5, 'name' => 'Dasar Luar Malaysia', 'subtopics' => [
            'Prinsip Dasar Luar', 'Hubungan Negara Komanwel',
            'ASEAN', 'NAM', 'OIC', 'PBB',
        ], 'skills' => [
            ['Menjelaskan dasar luar Malaysia', 'medium'],
            ['Menilai keanggotaan dalam pertubuhan dunia', 'medium'],
        ]],
        ['form' => 5, 'name' => 'Kecemerlangan Malaysia di Persada Dunia', 'subtopics' => [
            'Sumbangan Antarabangsa', 'Sukan', 'Sains dan Teknologi',
            'Ekonomi Global', 'Diplomasi',
        ], 'skills' => [
            ['Menilai pencapaian Malaysia di pentas dunia', 'medium'],
            ['Menghuraikan kecemerlangan negara', 'medium'],
        ]],
    ];
}

/**
 * Timeline of major Malaysian historical events. Each entry maps to a
 * chapter via topic name. era: pre-colonial | colonial | japanese |
 * post-war | independence | malaysia | modern.
 */
function sejarah_kssm_timeline(): array
{
    return [
        // -------- Pra-kolonial / awal --------
        ['Warisan Negara Bangsa',                     'pre-colonial', '1400',          NULL,            'Pengasasan Kesultanan Melayu Melaka', 'Parameswara mengasaskan Kesultanan Melaka selepas berhijrah dari Palembang.', 'high'],
        ['Warisan Negara Bangsa',                     'colonial',     '1511',          NULL,            'Portugis menakluki Melaka', 'Angkatan Alfonso de Albuquerque menawan Melaka, menamatkan zaman keemasan Kesultanan Melaka.', 'high'],
        ['Warisan Negara Bangsa',                     'colonial',     '1641',          NULL,            'Belanda menakluki Melaka', 'VOC Belanda merampas Melaka daripada Portugis dengan bantuan Johor.', 'medium'],
        ['Warisan Negara Bangsa',                     'colonial',     '1786',          NULL,            'Francis Light menduduki Pulau Pinang', 'Francis Light mengambil alih Pulau Pinang bagi pihak Syarikat Hindia Timur Inggeris (EIC).', 'high'],
        ['Warisan Negara Bangsa',                     'colonial',     '1819',          NULL,            'Singapura diasaskan Stamford Raffles', 'Singapura mula dibuka sebagai pelabuhan British oleh Sir Stamford Raffles.', 'medium'],
        ['Warisan Negara Bangsa',                     'colonial',     '1826',          NULL,            'Penubuhan Negeri-Negeri Selat', 'Pulau Pinang, Melaka dan Singapura disatukan sebagai Negeri-Negeri Selat di bawah EIC.', 'medium'],
        ['Warisan Negara Bangsa',                     'colonial',     '1874',          '20 Januari',    'Perjanjian Pangkor', 'British memulakan campur tangan rasmi di Tanah Melayu melalui Sistem Residen.', 'high'],
        ['Warisan Negara Bangsa',                     'colonial',     '1896',          NULL,            'Penubuhan Negeri-Negeri Melayu Bersekutu (NNMB)', 'Perak, Selangor, Pahang dan Negeri Sembilan disatukan di bawah satu pentadbiran British.', 'high'],
        ['Warisan Negara Bangsa',                     'colonial',     '1909',          NULL,            'Penubuhan Negeri-Negeri Melayu Tidak Bersekutu (NNMTB)', 'Kedah, Perlis, Kelantan dan Terengganu diserahkan oleh Siam kepada British (Perjanjian Bangkok); Johor menyusul 1914.', 'medium'],

        // -------- Awal nasionalisme --------
        ['Kebangkitan Nasionalisme',                  'colonial',     '1914-1918',     NULL,            'Perang Dunia Pertama', 'Konflik antarabangsa yang memberi kesan kepada ekonomi dan pentadbiran Tanah Melayu.', 'medium'],
        ['Kebangkitan Nasionalisme',                  'pre-war',      '1937',          NULL,            'Penubuhan Kesatuan Melayu Muda (KMM)', 'Pertubuhan politik radikal Melayu pertama yang menentang penjajahan, diasaskan oleh Ibrahim Yaacob.', 'high'],

        // -------- Pendudukan Jepun --------
        ['Konflik Dunia dan Pendudukan Jepun di Negara Kita', 'wwii', '1939-1945',     NULL,            'Perang Dunia Kedua', 'Perang global antara kuasa Berikat dan Paksi yang membawa kepada pendudukan Jepun di Tanah Melayu.', 'medium'],
        ['Konflik Dunia dan Pendudukan Jepun di Negara Kita', 'wwii', '1941',          '8 Disember',    'Pendaratan Jepun di Kota Bharu', 'Tentera Jepun mendarat di Pantai Sabak, Kota Bharu, memulakan serangan ke atas Tanah Melayu.', 'high'],
        ['Konflik Dunia dan Pendudukan Jepun di Negara Kita', 'wwii', '1942',          '15 Februari',   'Singapura jatuh ke tangan Jepun', 'British menyerah kalah di Singapura — kejatuhan terbesar tentera British dalam sejarah.', 'high'],
        ['Konflik Dunia dan Pendudukan Jepun di Negara Kita', 'wwii', '1945',          '15 Ogos',       'Jepun menyerah kalah', 'Tamatnya Perang Dunia Kedua dan pendudukan Jepun di Tanah Melayu.', 'high'],

        // -------- Era peralihan British --------
        ['Era Peralihan Kuasa British di Negara Kita', 'post-war',    '1945',          'September',     'Pentadbiran Tentera British (BMA)', 'British Military Administration mengambil alih pentadbiran selepas Jepun menyerah.', 'medium'],
        ['Era Peralihan Kuasa British di Negara Kita', 'post-war',    '1946',          '1 April',       'Malayan Union dilancarkan', 'British melancarkan Malayan Union dengan kewarganegaraan terbuka kepada semua kaum — dibantah hebat oleh orang Melayu.', 'high'],
        ['Era Peralihan Kuasa British di Negara Kita', 'post-war',    '1946',          '11 Mei',        'Penubuhan UMNO', 'Dato\' Onn Jaafar menubuhkan United Malays National Organisation untuk menentang Malayan Union.', 'high'],

        // -------- Persekutuan 1948 + Darurat --------
        ['Persekutuan Tanah Melayu 1948',             'post-war',    '1948',          '1 Februari',    'Persekutuan Tanah Melayu ditubuhkan', 'Menggantikan Malayan Union; mengembalikan kuasa Raja-Raja Melayu dan mengetatkan syarat kewarganegaraan.', 'high'],
        ['Ancaman Komunis dan Perisytiharan Darurat', 'emergency',   '1948',          '16 Jun',        'Pembunuhan pengurus ladang Sungai Siput', 'Tiga pengurus ladang Eropah dibunuh oleh komunis — pencetus Darurat.', 'medium'],
        ['Ancaman Komunis dan Perisytiharan Darurat', 'emergency',   '1948',          '18 Jun',        'Pengisytiharan Darurat', 'British mengisytiharkan Darurat selepas pembunuhan Sungai Siput; berlanjutan sehingga 1960.', 'high'],
        ['Ancaman Komunis dan Perisytiharan Darurat', 'emergency',   '1950',          NULL,            'Rancangan Briggs', 'Jeneral Sir Harold Briggs memperkenalkan strategi memutuskan bekalan komunis melalui Kampung Baru.', 'medium'],
        ['Ancaman Komunis dan Perisytiharan Darurat', 'emergency',   '1955',          '28 Disember',   'Rundingan Baling', 'Tunku Abdul Rahman bertemu Chin Peng untuk merundingkan amnesti — gagal mencapai persetujuan.', 'high'],

        // -------- Usaha ke arah kemerdekaan --------
        ['Usaha ke Arah Kemerdekaan',                 'independence', '1949',         NULL,            'Penubuhan MCA', 'Persatuan Cina Malaya ditubuhkan oleh Tan Cheng Lock.', 'medium'],
        ['Usaha ke Arah Kemerdekaan',                 'independence', '1951',         NULL,            'Sistem Ahli diperkenalkan', 'Latihan kepimpinan rakyat tempatan oleh British, pendahulu kabinet selepas merdeka.', 'medium'],
        ['Usaha ke Arah Kemerdekaan',                 'independence', '1954',         NULL,            'Parti Perikatan UMNO-MCA-MIC ditubuhkan', 'Gabungan tiga parti kaum utama yang menjadi tunggak politik selepas merdeka.', 'high'],

        // -------- Pilihan Raya --------
        ['Pilihan Raya',                              'independence', '1952',         NULL,            'Pilihan Raya Bandaran Kuala Lumpur', 'Perikatan UMNO-MCA memenangi 9 daripada 12 kerusi — bukti kerjasama kaum berkesan.', 'medium'],
        ['Pilihan Raya',                              'independence', '1955',         '27 Julai',      'Pilihan Raya Umum Pertama', 'Parti Perikatan memenangi 51 daripada 52 kerusi; Tunku Abdul Rahman menjadi Ketua Menteri.', 'high'],

        // -------- Perlembagaan & Kemerdekaan --------
        ['Perlembagaan Persekutuan Tanah Melayu 1957','independence', '1956',         'Januari',       'Rundingan Merdeka di London', 'Tunku Abdul Rahman dan delegasi merundingkan kemerdekaan dengan kerajaan British.', 'high'],
        ['Perlembagaan Persekutuan Tanah Melayu 1957','independence', '1956-1957',    NULL,            'Suruhanjaya Reid', 'Suruhanjaya bebas yang merangka Perlembagaan Persekutuan, diketuai Lord Reid.', 'high'],
        ['Pemasyhuran Kemerdekaan',                   'independence', '1957',         '31 Ogos',       'Hari Merdeka', 'Tunku Abdul Rahman memasyhurkan kemerdekaan Persekutuan Tanah Melayu di Stadium Merdeka.', 'high'],
        ['Pemasyhuran Kemerdekaan',                   'independence', '1960',         '31 Julai',      'Tamat Darurat', 'Pengisytiharan rasmi tamat tempoh Darurat selepas 12 tahun.', 'medium'],

        // -------- Pembentukan Malaysia --------
        ['Pembentukan Malaysia',                      'malaysia',     '1961',         '27 Mei',        'Cadangan Malaysia oleh Tunku', 'Tunku Abdul Rahman mencadangkan pembentukan Malaysia di Hotel Adelphi, Singapura.', 'high'],
        ['Pembentukan Malaysia',                      'malaysia',     '1962',         NULL,            'Suruhanjaya Cobbold', 'Suruhanjaya untuk meninjau pandangan rakyat Sabah dan Sarawak — majoriti menyokong Malaysia.', 'high'],
        ['Pembentukan Malaysia',                      'malaysia',     '1963',         '16 September',  'Hari Malaysia (Pembentukan Malaysia)', 'Persekutuan Tanah Melayu, Sabah, Sarawak dan Singapura bergabung membentuk Malaysia.', 'high'],

        // -------- Cabaran selepas Malaysia --------
        ['Cabaran Selepas Pembentukan Malaysia',      'malaysia',     '1963-1966',    NULL,            'Konfrontasi Indonesia', 'Pertelingkahan dengan Indonesia di bawah Sukarno yang menentang pembentukan Malaysia.', 'high'],
        ['Cabaran Selepas Pembentukan Malaysia',      'malaysia',     '1965',         '9 Ogos',        'Singapura keluar dari Malaysia', 'Singapura berpisah dari Malaysia dan menjadi negara berdaulat.', 'high'],
        ['Cabaran Selepas Pembentukan Malaysia',      'malaysia',     '1969',         '13 Mei',        'Peristiwa 13 Mei', 'Rusuhan kaum di Kuala Lumpur selepas pilihan raya umum — titik perubahan dalam dasar negara.', 'high'],

        // -------- Era pembinaan negara --------
        ['Membina Kesejahteraan Negara',              'modern',       '1970',         '31 Ogos',       'Pengisytiharan Rukun Negara', 'Lima prinsip Rukun Negara diisytiharkan oleh Yang di-Pertuan Agong sempena Hari Kemerdekaan.', 'high'],
        ['Membina Kemakmuran Negara',                 'modern',       '1970-1990',    NULL,            'Dasar Ekonomi Baru (DEB)', 'Strategi 20 tahun untuk membasmi kemiskinan dan menyusun semula masyarakat.', 'high'],
        ['Membina Kesejahteraan Negara',              'modern',       '1981',         '16 Julai',      'Tun Dr Mahathir menjadi Perdana Menteri', 'Mahathir Mohamad memulakan kerajaan 22 tahun yang mengubah landskap Malaysia.', 'medium'],
        ['Membina Kesejahteraan Negara',              'modern',       '1991',         '28 Februari',   'Wawasan 2020 dilancarkan', 'Tun Dr Mahathir melancarkan Wawasan 2020 ke arah Malaysia sebagai negara maju.', 'high'],
    ];
}

function sejarah_kssm_run(): array
{
    $subject = db_one("SELECT id FROM subjects WHERE slug = 'sejarah' LIMIT 1");
    if (!$subject) {
        return ['status' => 'no_subject', 'message' => 'Sejarah subject not found. Run install / seed_subjects first.'];
    }
    $sid = (int) $subject['id'];

    $timelineTable = (bool) db_one("SELECT 1 AS x FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'sejarah_timeline'");

    $catalog = sejarah_kssm_catalog();
    $topicsCreated = $topicsKept = $topicsAdopted = 0;
    $subCreated = $subKept = 0;
    $skillsCreated = $skillsKept = 0;
    $timelineCreated = $timelineKept = 0;

    $topicIds = []; // [name => id]

    foreach ($catalog as $i => $t) {
        $name = $t['name'];
        $form = (int) $t['form'];
        $sortOrder = ($form === 4 ? 0 : 100) + $i;

        $existing = db_one(
            'SELECT id FROM topics WHERE subject_id = ? AND name = ? AND form_level <=> ? LIMIT 1',
            [$sid, $name, $form]
        );
        if ($existing) {
            $tid = (int) $existing['id'];
            $topicsKept++;
        } else {
            $legacy = db_one(
                'SELECT id FROM topics WHERE subject_id = ? AND name = ? AND form_level IS NULL LIMIT 1',
                [$sid, $name]
            );
            if ($legacy) {
                $tid = (int) $legacy['id'];
                db_exec('UPDATE topics SET form_level = ? WHERE id = ?', [$form, $tid]);
                $topicsAdopted++;
            } else {
                $slug = preg_replace('/[^a-z0-9]+/', '-', strtolower($name . ' f' . $form));
                $tid  = db_exec(
                    'INSERT INTO topics (subject_id, form_level, name, slug, sort_order) VALUES (?, ?, ?, ?, ?)',
                    [$sid, $form, $name, $slug, $sortOrder]
                );
                $topicsCreated++;
            }
        }
        if (!isset($topicIds[$name])) {
            $topicIds[$name] = $tid;
        }

        foreach ($t['subtopics'] as $idx => $subName) {
            $sub = db_one('SELECT id FROM subtopics WHERE topic_id = ? AND name = ? LIMIT 1', [$tid, $subName]);
            if ($sub) {
                $subKept++;
            } else {
                db_exec('INSERT INTO subtopics (topic_id, name, sort_order) VALUES (?, ?, ?)', [$tid, $subName, $idx + 1]);
                $subCreated++;
            }
        }

        foreach ($t['skills'] as [$skillName, $diff]) {
            $existing = db_one('SELECT id FROM skills WHERE topic_id = ? AND name = ? LIMIT 1', [$tid, $skillName]);
            if ($existing) {
                $skillsKept++;
            } else {
                db_exec('INSERT INTO skills (topic_id, name, difficulty) VALUES (?, ?, ?)', [$tid, $skillName, $diff]);
                $skillsCreated++;
            }
        }
    }

    if ($timelineTable) {
        foreach (sejarah_kssm_timeline() as $idx => [$topicName, $era, $year, $date, $title, $desc, $importance]) {
            $tid = $topicIds[$topicName] ?? null;
            $existing = db_one('SELECT id FROM sejarah_timeline WHERE title = ? LIMIT 1', [$title]);
            if ($existing) {
                $timelineKept++;
            } else {
                db_exec(
                    'INSERT INTO sejarah_timeline (topic_id, topic_label, era, year_label, event_date, title, description, importance, sort_order)
                     VALUES (?,?,?,?,?,?,?,?,?)',
                    [$tid, $topicName, $era, $year, $date, $title, $desc, $importance, $idx + 1]
                );
                $timelineCreated++;
            }
        }
    }

    return [
        'status'             => 'ok',
        'topics_created'     => $topicsCreated,
        'topics_kept'        => $topicsKept,
        'topics_adopted'     => $topicsAdopted,
        'subtopics_created'  => $subCreated,
        'subtopics_kept'     => $subKept,
        'skills_created'     => $skillsCreated,
        'skills_kept'        => $skillsKept,
        'timeline_created'   => $timelineCreated,
        'timeline_kept'      => $timelineKept,
        'catalog_topics'     => count($catalog),
        'timeline_table'     => $timelineTable,
    ];
}

if (PHP_SAPI === 'cli') {
    $r = sejarah_kssm_run();
    if (($r['status'] ?? '') !== 'ok') {
        fwrite(STDERR, ($r['message'] ?? 'unknown error') . "\n");
        exit(1);
    }
    echo "KSSM Sejarah seeded —\n";
    echo "  Bab:        created {$r['topics_created']}, adopted {$r['topics_adopted']}, kept {$r['topics_kept']} (catalog {$r['catalog_topics']})\n";
    echo "  Subtopik:   created {$r['subtopics_created']}, kept {$r['subtopics_kept']}\n";
    echo "  Kemahiran:  created {$r['skills_created']}, kept {$r['skills_kept']}\n";
    if ($r['timeline_table']) {
        echo "  Timeline:   created {$r['timeline_created']}, kept {$r['timeline_kept']}\n";
    } else {
        echo "  Timeline:   table missing — run database migrations to enable.\n";
    }
}
