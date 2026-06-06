<?php
/**
 * KSSM SPM Ekonomi syllabus seeder. Tingkatan 4 (4 bab — pengenalan,
 * pasaran, wang/bank/pendapatan, pengeluaran) + Tingkatan 5 (7 sub-
 * topik dari Bab 1 Ekonomi dan Kerajaan + Bab 2 Malaysia dan Ekonomi
 * Global) — 11 topik total, dengan starter bank konsep ekonomi
 * (istilah + formula gabungan).
 *
 * Idempotent: topik matched by (subject_id, name, form_level); legacy
 * same-named NULL-form topik are adopted.
 *
 *   php public_html/cron/seed_ekonomi_kssm.php
 */
declare(strict_types=1);

require_once __DIR__ . '/../inc/db.php';

function ekonomi_kssm_catalog(): array
{
    return [
        // ============= TINGKATAN 4 =============
        ['form' => 4, 'name' => 'Pengenalan kepada Ekonomi', 'subtopics' => [
            'Konsep Ekonomi dan Kelangkaan',
            'Masalah Asas Ekonomi (Apa, Bagaimana, Untuk Siapa)',
            'Kos Lepas dan Pilihan',
            'Sistem Ekonomi (Pasaran Bebas, Perancangan Pusat, Campuran)',
            'Faktor Pengeluaran',
            'Sektor Ekonomi (Primer, Sekunder, Tertier)',
        ], 'skills' => [
            ['Menjelaskan konsep ekonomi dan kelangkaan', 'easy'],
            ['Membezakan sistem ekonomi', 'medium'],
            ['Menganalisis kos lepas dalam pilihan ekonomi', 'medium'],
        ]],
        ['form' => 4, 'name' => 'Pasaran', 'subtopics' => [
            'Konsep Permintaan dan Hukum Permintaan',
            'Faktor Mempengaruhi Permintaan dan Anjakan Keluk Permintaan',
            'Konsep Penawaran dan Hukum Penawaran',
            'Faktor Mempengaruhi Penawaran dan Anjakan Keluk Penawaran',
            'Keseimbangan Pasaran',
            'Lebihan Pengguna dan Lebihan Pengeluar',
            'Keanjalan Permintaan dan Penawaran',
        ], 'skills' => [
            ['Menjelaskan hukum permintaan dan penawaran', 'medium'],
            ['Melukis keluk permintaan dan penawaran', 'medium'],
            ['Menganalisis anjakan keluk dan keseimbangan pasaran', 'hard'],
            ['Mengira keanjalan permintaan dan penawaran', 'hard'],
        ]],
        ['form' => 4, 'name' => 'Wang, Bank dan Pendapatan Individu', 'subtopics' => [
            'Konsep dan Fungsi Wang',
            'Ciri-ciri Wang yang Baik',
            'Jenis Pendapatan Individu (Gaji, Upah, Untung, Sewa, Faedah)',
            'Bank Pusat dan Bank Perdagangan',
            'Perkhidmatan Perbankan Moden',
            'Belanjawan Peribadi',
            'Tabungan dan Pelaburan',
        ], 'skills' => [
            ['Menjelaskan fungsi wang dan jenis pendapatan', 'easy'],
            ['Membezakan peranan bank pusat dan bank perdagangan', 'medium'],
            ['Menyediakan belanjawan peribadi mudah', 'medium'],
        ]],
        ['form' => 4, 'name' => 'Pengeluaran', 'subtopics' => [
            'Konsep Pengeluaran',
            'Faktor Pengeluaran (Tanah, Buruh, Modal, Usahawan)',
            'Sektor Pengeluaran',
            'Pengkhususan dan Pembahagian Kerja',
            'Kos Pengeluaran (Kos Tetap, Berubah, Marginal)',
            'Hasil Pengeluaran (TR, AR, MR)',
        ], 'skills' => [
            ['Mengenal pasti faktor pengeluaran', 'easy'],
            ['Mengira kos dan hasil pengeluaran', 'hard'],
            ['Menjelaskan kesan pengkhususan', 'medium'],
        ]],

        // ============= TINGKATAN 5 =============
        ['form' => 5, 'name' => 'Peranan Kerajaan dalam Ekonomi', 'subtopics' => [
            'Peranan Kerajaan sebagai Pengeluar',
            'Peranan Kerajaan sebagai Majikan',
            'Objektif Makroekonomi Negara',
            'Pengawal Selia Pengeluar',
            'Eksternaliti Positif dan Negatif',
        ], 'skills' => [
            ['Menghuraikan peranan kerajaan dalam ekonomi', 'medium'],
            ['Menilai objektif makroekonomi negara', 'medium'],
            ['Menganalisis eksternaliti dan kawal selia', 'hard'],
        ]],
        ['form' => 5, 'name' => 'Penunjuk Ekonomi', 'subtopics' => [
            'Indeks Harga Pengguna (IHP)',
            'Inflasi dan Jenis Inflasi',
            'Kesan Inflasi',
            'Pengangguran dan Jenis Pengangguran',
            'Keluaran Dalam Negara Kasar (KDNK)',
            'Pertumbuhan Ekonomi Malaysia',
        ], 'skills' => [
            ['Mengira IHP dan kadar inflasi', 'hard'],
            ['Membezakan jenis inflasi dan pengangguran', 'medium'],
            ['Mentafsir data KDNK dan pertumbuhan ekonomi', 'medium'],
        ]],
        ['form' => 5, 'name' => 'Alat Dasar Ekonomi', 'subtopics' => [
            'Dasar Fiskal',
            'Hasil Kerajaan',
            'Cukai Progresif, Regresif dan Kadar Malar',
            'Perbelanjaan Kerajaan',
            'Belanjawan Negara (Lebihan, Kurangan, Seimbang)',
            'Dasar Kewangan dan Peranan Bank Negara',
        ], 'skills' => [
            ['Membezakan dasar fiskal dan dasar kewangan', 'medium'],
            ['Menjelaskan kesan jenis cukai ke atas agihan pendapatan', 'hard'],
            ['Menganalisis belanjawan negara', 'medium'],
        ]],
        ['form' => 5, 'name' => 'Globalisasi', 'subtopics' => [
            'Konsep dan Ciri Globalisasi',
            'Faktor Pendorong Globalisasi',
            'Kesan Globalisasi terhadap Ekonomi Malaysia',
            'Syarikat Multinasional dan Blok Perdagangan',
            'Pertubuhan Perdagangan Sedunia (WTO, ASEAN, AFTA)',
        ], 'skills' => [
            ['Menjelaskan konsep dan ciri globalisasi', 'medium'],
            ['Menilai kesan globalisasi terhadap Malaysia', 'medium'],
            ['Mengenal pasti peranan pertubuhan perdagangan dunia', 'medium'],
        ]],
        ['form' => 5, 'name' => 'Perdagangan Antarabangsa', 'subtopics' => [
            'Konsep Perdagangan Antarabangsa',
            'Sebab-sebab Perdagangan Antarabangsa',
            'Eksport dan Import Utama Malaysia',
            'Halangan Perdagangan (Tarif, Kuota, Embargo)',
            'Kelebihan Perdagangan Bebas',
        ], 'skills' => [
            ['Menjelaskan sebab perdagangan antarabangsa', 'medium'],
            ['Membezakan jenis halangan perdagangan', 'medium'],
            ['Menilai kelebihan dan kekurangan perdagangan bebas', 'medium'],
        ]],
        ['form' => 5, 'name' => 'Imbangan Pembayaran', 'subtopics' => [
            'Konsep Imbangan Pembayaran',
            'Akaun Semasa (Eksport, Import, Pendapatan Primer dan Sekunder)',
            'Akaun Modal dan Kewangan',
            'Lebihan dan Defisit Imbangan Pembayaran',
            'Langkah Memperbaiki Imbangan Pembayaran',
        ], 'skills' => [
            ['Mengenal pasti komponen imbangan pembayaran', 'medium'],
            ['Mengira imbangan akaun semasa', 'hard'],
            ['Mencadangkan langkah memperbaiki defisit', 'medium'],
        ]],
        ['form' => 5, 'name' => 'Kadar Pertukaran Asing', 'subtopics' => [
            'Konsep Kadar Pertukaran Asing',
            'Sistem Kadar Pertukaran (Tetap, Apungan Bebas, Apungan Terurus)',
            'Faktor Mempengaruhi Kadar Pertukaran',
            'Penurunan Nilai dan Penyusutan Nilai Mata Wang',
            'Kesan Perubahan Kadar Pertukaran',
        ], 'skills' => [
            ['Menjelaskan sistem kadar pertukaran asing', 'medium'],
            ['Menganalisis faktor mempengaruhi kadar pertukaran', 'medium'],
            ['Menilai kesan perubahan nilai mata wang ke atas ekonomi', 'hard'],
        ]],
    ];
}

/** Starter Ekonomi concepts bank (istilah + formula). */
function ekonomi_kssm_concepts(): array
{
    return [
        // topic, type, name, definition, formula, example, category
        ['Pengenalan kepada Ekonomi',     'istilah', 'Kelangkaan', 'Keadaan sumber yang terhad berbanding dengan kehendak manusia yang tidak terhad.', null, 'Minyak mentah dunia terhad walaupun permintaan global meningkat.', 'konsep_asas'],
        ['Pengenalan kepada Ekonomi',     'istilah', 'Kos Lepas', 'Nilai pilihan terbaik kedua yang terpaksa diketepikan apabila membuat sesuatu pilihan.', null, 'Memilih belajar di universiti, kos lepasnya ialah gaji yang boleh diperoleh jika bekerja.', 'konsep_asas'],
        ['Pengenalan kepada Ekonomi',     'istilah', 'Faktor Pengeluaran', 'Sumber yang digunakan dalam proses pengeluaran iaitu tanah, buruh, modal dan usahawan.', null, 'Pertanian memerlukan tanah, pekerja, mesin (modal) dan pengusaha (usahawan).', 'pengeluaran'],
        ['Pengenalan kepada Ekonomi',     'istilah', 'Sistem Ekonomi Campuran', 'Sistem ekonomi yang menggabungkan ciri pasaran bebas dan perancangan pusat — sektor swasta dan kerajaan sama-sama memainkan peranan.', null, 'Malaysia, Singapura, kebanyakan negara membangun.', 'sistem'],

        ['Pasaran',                       'istilah', 'Hukum Permintaan', 'Apabila harga sesuatu barang naik, kuantiti yang diminta turun; apabila harga turun, kuantiti yang diminta naik (ceteris paribus).', null, 'Harga durian naik dari RM10 ke RM20 sekilo - jualan turun.', 'pasaran'],
        ['Pasaran',                       'istilah', 'Hukum Penawaran', 'Apabila harga sesuatu barang naik, kuantiti yang ditawarkan naik; apabila harga turun, kuantiti yang ditawarkan turun (ceteris paribus).', null, 'Harga getah naik - pengeluar menoreh lebih banyak.', 'pasaran'],
        ['Pasaran',                       'istilah', 'Keseimbangan Pasaran', 'Keadaan di mana kuantiti yang diminta sama dengan kuantiti yang ditawarkan pada satu harga tertentu.', null, 'Pertemuan keluk D dan S pada graf permintaan-penawaran.', 'pasaran'],
        ['Pasaran',                       'formula', 'Keanjalan Harga Permintaan (Ed)', 'Mengukur peratus perubahan kuantiti diminta apabila harga berubah 1%.', 'Ed = (%ΔQd) ÷ (%ΔP)', 'Ed = -50%/25% = -2 (anjal); nilai mutlak |Ed| dibanding 1.', 'pasaran'],
        ['Pasaran',                       'formula', 'Keanjalan Harga Penawaran (Es)', 'Mengukur peratus perubahan kuantiti ditawarkan apabila harga berubah 1%.', 'Es = (%ΔQs) ÷ (%ΔP)', 'Es > 1: anjal; Es < 1: tak anjal; Es = 1: anjal seunit.', 'pasaran'],
        ['Pasaran',                       'istilah', 'Lebihan Pengguna', 'Perbezaan antara harga maksimum yang sanggup dibayar pengguna dengan harga sebenar.', null, 'Sanggup bayar RM30 untuk filem, tapi bayar RM15 - lebihan pengguna RM15.', 'pasaran'],

        ['Wang, Bank dan Pendapatan Individu', 'istilah', 'Wang', 'Sebarang benda yang diterima umum sebagai bayaran barang dan perkhidmatan atau pelunasan hutang.', null, 'Ringgit Malaysia, USD, e-wallet.', 'wang_bank'],
        ['Wang, Bank dan Pendapatan Individu', 'istilah', 'Bank Pusat', 'Bank tertinggi negara yang mengawal sistem kewangan dan menggubal dasar kewangan.', null, 'Bank Negara Malaysia (BNM).', 'wang_bank'],
        ['Wang, Bank dan Pendapatan Individu', 'istilah', 'Bank Perdagangan', 'Bank yang menyediakan perkhidmatan deposit, pinjaman dan pemindahan dana kepada orang awam dan perniagaan.', null, 'Maybank, CIMB, Public Bank, Hong Leong.', 'wang_bank'],

        ['Pengeluaran',                   'istilah', 'Pengkhususan', 'Memberi tumpuan kepada pengeluaran satu jenis barang atau perkhidmatan tertentu untuk mencapai kecekapan.', null, 'Malaysia mengkhusus dalam getah dan kelapa sawit.', 'pengeluaran'],
        ['Pengeluaran',                   'formula', 'Hasil Purata (AR)', 'Hasil yang diterima bagi setiap unit yang dijual.', 'AR = Hasil Jumlah ÷ Bilangan Unit', 'Jika TR = RM500, Q = 50, maka AR = RM10.', 'pengeluaran'],
        ['Pengeluaran',                   'formula', 'Hasil Marginal (MR)', 'Tambahan hasil yang diperoleh apabila unit tambahan dijual.', 'MR = ΔTR ÷ ΔQ', 'TR naik RM50 apabila jual 5 unit lagi: MR = RM10.', 'pengeluaran'],
        ['Pengeluaran',                   'formula', 'Kos Marginal (MC)', 'Tambahan kos apabila satu unit lagi dikeluarkan.', 'MC = ΔTC ÷ ΔQ', 'TC naik RM30 untuk 3 unit tambahan: MC = RM10.', 'pengeluaran'],

        ['Penunjuk Ekonomi',              'formula', 'Indeks Harga Pengguna (IHP)', 'Indeks yang mengukur perubahan harga purata barang dan perkhidmatan yang dibeli oleh pengguna.', 'IHP = (Harga Bakul Semasa ÷ Harga Bakul Tahun Asas) × 100', 'IHP 2024 = 115 (berbanding 100 pada tahun asas).', 'penunjuk'],
        ['Penunjuk Ekonomi',              'formula', 'Kadar Inflasi', 'Peratus perubahan IHP dari tahun ke tahun.', 'Kadar Inflasi = ((IHP Tahun Ini − IHP Tahun Lepas) ÷ IHP Tahun Lepas) × 100%', 'IHP 110 → 115: kadar inflasi = (5/110) × 100 ≈ 4.5%.', 'penunjuk'],
        ['Penunjuk Ekonomi',              'formula', 'Kadar Pengangguran', 'Peratus tenaga buruh yang menganggur tetapi sedang mencari kerja.', 'Kadar = (Bilangan Penganggur ÷ Jumlah Tenaga Buruh) × 100%', 'Tenaga buruh 15 juta, penganggur 500,000: kadar = 3.3%.', 'penunjuk'],
        ['Penunjuk Ekonomi',              'istilah', 'Inflasi Tarikan Permintaan', 'Inflasi yang berlaku apabila permintaan agregat melebihi penawaran agregat.', null, 'Ekonomi pulih selepas pandemik, permintaan melonjak melepasi kapasiti pengeluaran.', 'penunjuk'],
        ['Penunjuk Ekonomi',              'istilah', 'Inflasi Tolakan Kos', 'Inflasi yang berlaku akibat kenaikan kos pengeluaran (gaji, bahan mentah, tenaga).', null, 'Harga minyak naik mendorong harga semua barang naik.', 'penunjuk'],
        ['Penunjuk Ekonomi',              'istilah', 'Pengangguran Berkitar', 'Pengangguran yang berlaku semasa kemelesetan ekonomi.', null, 'Pengangguran besar-besaran semasa krisis pandemik 2020-2021.', 'penunjuk'],
        ['Penunjuk Ekonomi',              'istilah', 'Pengangguran Geseran', 'Pengangguran sementara antara dua pekerjaan.', null, 'Graduan baharu mencari kerja, atau bertukar kerjaya.', 'penunjuk'],
        ['Penunjuk Ekonomi',              'formula', 'KDNK', 'Nilai pasaran semua barang dan perkhidmatan akhir yang dikeluarkan dalam ekonomi sesebuah negara dalam tempoh setahun.', 'KDNK = C + I + G + (X − M)', 'C: penggunaan; I: pelaburan; G: perbelanjaan kerajaan; X−M: eksport bersih.', 'penunjuk'],
        ['Penunjuk Ekonomi',              'formula', 'Kadar Pertumbuhan Ekonomi', 'Peratus perubahan KDNK benar dari tahun ke tahun.', 'Pertumbuhan = ((KDNK Tahun Ini − KDNK Tahun Lepas) ÷ KDNK Tahun Lepas) × 100%', 'Pertumbuhan Malaysia 2023: ~3.7%.', 'penunjuk'],

        ['Alat Dasar Ekonomi',            'istilah', 'Dasar Fiskal', 'Dasar kerajaan menggunakan cukai dan perbelanjaan untuk mempengaruhi ekonomi.', null, 'Kerajaan mengurangkan cukai untuk merangsang ekonomi.', 'dasar'],
        ['Alat Dasar Ekonomi',            'istilah', 'Dasar Kewangan', 'Dasar Bank Negara menggunakan kadar faedah dan bekalan wang untuk mempengaruhi ekonomi.', null, 'BNM menaikkan OPR untuk mengawal inflasi.', 'dasar'],
        ['Alat Dasar Ekonomi',            'istilah', 'Cukai Progresif', 'Cukai yang kadarnya meningkat dengan kenaikan pendapatan.', null, 'Cukai pendapatan individu Malaysia: 0% hingga 30% mengikut band pendapatan.', 'cukai'],
        ['Alat Dasar Ekonomi',            'istilah', 'Cukai Regresif', 'Cukai yang membebankan golongan berpendapatan rendah lebih banyak secara peratusan.', null, 'Cukai jualan (SST) sama pada semua pengguna walaupun pendapatan berbeza.', 'cukai'],
        ['Alat Dasar Ekonomi',            'istilah', 'Belanjawan Defisit', 'Perbelanjaan kerajaan melebihi hasil kerajaan.', null, 'Kerajaan meminjam untuk menampung kurangan dalam belanjawan.', 'dasar'],

        ['Globalisasi',                   'istilah', 'Globalisasi', 'Proses peningkatan saling kebergantungan ekonomi, politik dan budaya antara negara di dunia.', null, 'Syarikat seperti Apple, Samsung beroperasi di banyak negara.', 'global'],
        ['Globalisasi',                   'istilah', 'Syarikat Multinasional', 'Syarikat yang beroperasi di lebih daripada satu negara dengan kemudahan pengeluaran dan operasi merentas sempadan.', null, 'Petronas, Toyota, Nestlé.', 'global'],
        ['Globalisasi',                   'istilah', 'Blok Perdagangan', 'Kumpulan negara yang membentuk perjanjian perdagangan untuk mengurangkan halangan perdagangan antara mereka.', null, 'ASEAN, Kesatuan Eropah, USMCA.', 'global'],

        ['Perdagangan Antarabangsa',      'istilah', 'Tarif', 'Cukai yang dikenakan ke atas barang import.', null, 'Tarif 10% ke atas import kereta dari negara X.', 'halangan'],
        ['Perdagangan Antarabangsa',      'istilah', 'Kuota', 'Had kuantiti barang yang boleh diimport dalam tempoh tertentu.', null, 'Kuota import beras 500,000 tan setahun.', 'halangan'],
        ['Perdagangan Antarabangsa',      'istilah', 'Embargo', 'Larangan total perdagangan ke atas barang tertentu atau dengan negara tertentu.', null, 'Embargo US ke atas Iran.', 'halangan'],

        ['Imbangan Pembayaran',           'istilah', 'Akaun Semasa', 'Komponen imbangan pembayaran yang merekod eksport/import barang dan perkhidmatan serta pendapatan primer/sekunder.', null, 'Lebihan akaun semasa Malaysia: eksport > import.', 'imbangan'],
        ['Imbangan Pembayaran',           'istilah', 'Imbangan Pembayaran Defisit', 'Apabila aliran keluar (import + pembayaran luar) melebihi aliran masuk (eksport + penerimaan).', null, 'Negara terpaksa meminjam atau menggunakan rizab antarabangsa.', 'imbangan'],

        ['Kadar Pertukaran Asing',        'istilah', 'Kadar Pertukaran Apungan Bebas', 'Sistem di mana kadar pertukaran ditentukan sepenuhnya oleh kuasa pasaran (permintaan dan penawaran).', null, 'USD, JPY, EUR — semua diapungkan bebas.', 'pertukaran'],
        ['Kadar Pertukaran Asing',        'istilah', 'Penurunan Nilai (Devaluation)', 'Pengurangan nilai mata wang sesebuah negara berbanding mata wang lain (sistem kadar tetap).', null, 'Bank pusat memutuskan menurunkan kadar tukaran rasmi.', 'pertukaran'],
        ['Kadar Pertukaran Asing',        'istilah', 'Penyusutan Nilai (Depreciation)', 'Pengurangan nilai mata wang secara semula jadi melalui pasaran (sistem kadar apungan).', null, 'Ringgit jatuh dari RM4.10 ke RM4.50 per USD akibat permintaan pasaran.', 'pertukaran'],
    ];
}

function ekonomi_kssm_run(): array
{
    $subject = db_one("SELECT id FROM subjects WHERE slug = 'ekonomi' LIMIT 1");
    if (!$subject) {
        return ['status' => 'no_subject', 'message' => 'Ekonomi subject not found. Run install / seed_subjects first.'];
    }
    $sid = (int) $subject['id'];

    $conceptsTable = (bool) db_one("SELECT 1 AS x FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ekonomi_concepts'");

    $catalog = ekonomi_kssm_catalog();
    $topicsCreated = $topicsKept = $topicsAdopted = 0;
    $subCreated = $subKept = 0;
    $skillsCreated = $skillsKept = 0;
    $conceptsCreated = $conceptsKept = 0;

    $topicIds = [];

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

    if ($conceptsTable) {
        foreach (ekonomi_kssm_concepts() as $idx => [$topicName, $type, $name, $def, $formula, $example, $category]) {
            $tid = $topicIds[$topicName] ?? null;
            $existing = db_one('SELECT id FROM ekonomi_concepts WHERE name = ? LIMIT 1', [$name]);
            if ($existing) {
                $conceptsKept++;
            } else {
                db_exec(
                    'INSERT INTO ekonomi_concepts (topic_id, topic_label, type, name, definition, formula, example, category, sort_order)
                     VALUES (?,?,?,?,?,?,?,?,?)',
                    [$tid, $topicName, $type, $name, $def, $formula, $example, $category, $idx + 1]
                );
                $conceptsCreated++;
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
        'concepts_created'   => $conceptsCreated,
        'concepts_kept'      => $conceptsKept,
        'catalog_topics'     => count($catalog),
        'concepts_table'     => $conceptsTable,
    ];
}

if (PHP_SAPI === 'cli') {
    $r = ekonomi_kssm_run();
    if (($r['status'] ?? '') !== 'ok') {
        fwrite(STDERR, ($r['message'] ?? 'unknown error') . "\n");
        exit(1);
    }
    echo "KSSM Ekonomi seeded —\n";
    echo "  Topik:     created {$r['topics_created']}, adopted {$r['topics_adopted']}, kept {$r['topics_kept']} (catalog {$r['catalog_topics']})\n";
    echo "  Subtopik:  created {$r['subtopics_created']}, kept {$r['subtopics_kept']}\n";
    echo "  Kemahiran: created {$r['skills_created']}, kept {$r['skills_kept']}\n";
    if ($r['concepts_table']) {
        echo "  Konsep:    created {$r['concepts_created']}, kept {$r['concepts_kept']}\n";
    } else {
        echo "  Konsep:    table missing — run database migrations to enable.\n";
    }
}
