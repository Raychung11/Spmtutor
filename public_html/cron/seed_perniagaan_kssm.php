<?php
/**
 * KSSM SPM Perniagaan syllabus seeder. Tingkatan 4 (4 bab — tujuan,
 * pemilikan, trend, visi/misi, organisasi) + Tingkatan 5 (7 bab —
 * pengurusan sumber manusia/fizikal/teknologi, pembiayaan, penyata
 * kewangan, usahawan, rancangan perniagaan) — 11 bab total, dengan
 * starter bank istilah perniagaan.
 *
 * Idempotent: bab matched by (subject_id, name, form_level); legacy
 * same-named NULL-form bab are adopted.
 *
 *   php public_html/cron/seed_perniagaan_kssm.php
 */
declare(strict_types=1);

require_once __DIR__ . '/../inc/db.php';

function perniagaan_kssm_catalog(): array
{
    return [
        // ============= TINGKATAN 4 =============
        ['form' => 4, 'name' => 'Tujuan Perniagaan dan Pemilikan Perniagaan', 'subtopics' => [
            'Tujuan Perniagaan',
            'Pemilikan Tunggal',
            'Perkongsian',
            'Syarikat Sendirian Berhad (Sdn Bhd)',
            'Syarikat Awam Berhad (Bhd)',
            'Koperasi',
            'Klasifikasi Perniagaan Mengikut Saiz',
            'Persekitaran Perniagaan',
            'Peranan Kerajaan dalam Perniagaan',
        ], 'skills' => [
            ['Menjelaskan tujuan perniagaan', 'easy'],
            ['Membandingkan bentuk-bentuk pemilikan perniagaan', 'medium'],
            ['Mengklasifikasikan perniagaan mengikut saiz', 'medium'],
            ['Menilai peranan kerajaan dalam perniagaan', 'medium'],
        ]],
        ['form' => 4, 'name' => 'Trend Semasa dalam Perniagaan', 'subtopics' => [
            'E-dagang dan M-dagang',
            'Perniagaan Hijau dan Lestari',
            'Globalisasi Perniagaan',
            'Gig Economy dan Perniagaan Atas Talian',
            'Inovasi dan Teknologi dalam Perniagaan',
        ], 'skills' => [
            ['Menghuraikan trend semasa perniagaan', 'medium'],
            ['Menganalisis kesan globalisasi', 'medium'],
            ['Menilai peluang perniagaan atas talian', 'medium'],
        ]],
        ['form' => 4, 'name' => 'Penetapan Visi, Misi dan Objektif Perniagaan', 'subtopics' => [
            'Maksud Visi, Misi dan Objektif',
            'Perbandingan Visi, Misi dan Objektif Beberapa Perniagaan',
            'Ciri Objektif yang Baik (SMART)',
            'Tujuan Penetapan Visi, Misi dan Objektif',
            'Faktor-faktor yang Mempengaruhi Perubahan Visi, Misi dan Objektif',
        ], 'skills' => [
            ['Membezakan visi, misi dan objektif', 'medium'],
            ['Membina objektif perniagaan menggunakan SMART', 'medium'],
            ['Menganalisis faktor perubahan visi/misi/objektif', 'medium'],
        ]],
        ['form' => 4, 'name' => 'Bahagian-bahagian Fungsian Utama dalam Organisasi Perniagaan', 'subtopics' => [
            'Bahagian Pengeluaran',
            'Bahagian Pemasaran',
            'Bahagian Kewangan dan Perakaunan',
            'Bahagian Sumber Manusia',
            'Bahagian Pentadbiran',
            'Bahagian Penyelidikan dan Pembangunan (R&D)',
            'Pembangunan Produk dan Pasaran Baharu',
        ], 'skills' => [
            ['Mengenal pasti bahagian fungsian dalam organisasi', 'easy'],
            ['Menerangkan tujuan setiap bahagian fungsian', 'medium'],
            ['Membincangkan pembangunan produk dan pasaran baharu', 'medium'],
        ]],

        // ============= TINGKATAN 5 =============
        ['form' => 5, 'name' => 'Pengurusan Sumber Manusia', 'subtopics' => [
            'Definisi Pengurusan Sumber Manusia',
            'Pengambilan dan Pemilihan Pekerja',
            'Latihan dan Pembangunan Pekerja',
            'Penilaian Prestasi',
            'Sistem Gaji dan Pampasan',
            'Hubungan Industri dan Kesatuan Sekerja',
        ], 'skills' => [
            ['Menjelaskan fungsi pengurusan sumber manusia', 'medium'],
            ['Menghuraikan proses pengambilan pekerja', 'medium'],
            ['Menilai sistem penilaian prestasi', 'medium'],
        ]],
        ['form' => 5, 'name' => 'Pengurusan Sumber Fizikal dan Teknologi', 'subtopics' => [
            'Sumber Fizikal Perniagaan',
            'Pemilihan Lokasi dan Premis',
            'Susun Atur dan Reka Bentuk Premis',
            'Teknologi dalam Perniagaan',
            'Sistem Maklumat Perniagaan',
            'Penyenggaraan Aset',
        ], 'skills' => [
            ['Menghuraikan pengurusan sumber fizikal', 'medium'],
            ['Menilai pemilihan lokasi perniagaan', 'medium'],
            ['Membincangkan kepentingan teknologi dalam perniagaan', 'medium'],
        ]],
        ['form' => 5, 'name' => 'Sumber Pembiayaan Perniagaan', 'subtopics' => [
            'Sumber Pembiayaan Dalaman (Modal Sendiri, Untung Tertahan)',
            'Sumber Pembiayaan Luaran',
            'Pinjaman Bank dan Institusi Kewangan',
            'Agensi Kerajaan dan Geran',
            'Pajakan dan Sewa Beli',
            'Crowdfunding dan Modal Teroka',
        ], 'skills' => [
            ['Membezakan sumber pembiayaan dalaman dan luaran', 'medium'],
            ['Menilai pilihan pembiayaan terbaik', 'hard'],
            ['Menghuraikan peranan agensi kerajaan', 'medium'],
        ]],
        ['form' => 5, 'name' => 'Penyata Kewangan Perniagaan', 'subtopics' => [
            'Penyata Pendapatan',
            'Penyata Kedudukan Kewangan',
            'Penyata Aliran Tunai',
            'Tafsiran Penyata Kewangan',
            'Belanjawan Perniagaan',
        ], 'skills' => [
            ['Menyediakan penyata pendapatan dan kedudukan kewangan', 'hard'],
            ['Mentafsir penyata kewangan untuk membuat keputusan', 'hard'],
            ['Menyediakan belanjawan perniagaan', 'medium'],
        ]],
        ['form' => 5, 'name' => 'Persediaan Menjadi Usahawan', 'subtopics' => [
            'Ciri-ciri Keperibadian Usahawan',
            'Kekuatan Diri dan Kelebihan Menjadi Usahawan',
            'Faktor Penyumbang untuk Memulakan Perniagaan',
            'Kelebihan Berniaga Sebagai Peluang Kerjaya',
            'Tokoh Usahawan Berjaya',
        ], 'skills' => [
            ['Mengenal pasti ciri usahawan berjaya', 'medium'],
            ['Menilai kekuatan diri sebagai usahawan', 'medium'],
            ['Menganalisis faktor penyumbang kejayaan perniagaan', 'medium'],
        ]],
        ['form' => 5, 'name' => 'Persediaan Memulakan Perniagaan', 'subtopics' => [
            'Akta Penubuhan Entiti Perniagaan',
            'Prosedur Pendaftaran Perniagaan (SSM)',
            'Tanggungjawab Selepas Mendaftar Perniagaan',
            'Dokumen Perniagaan yang Perlu Disimpan',
            'Penyediaan Penyata Aliran Tunai',
            'Pemasaran dan Jualan',
            'Persediaan Aktiviti Pemasaran',
        ], 'skills' => [
            ['Mengenal pasti akta penubuhan entiti', 'medium'],
            ['Menghuraikan prosedur pendaftaran SSM', 'medium'],
            ['Menyediakan penyata aliran tunai usahawan', 'hard'],
            ['Merancang aktiviti pemasaran', 'medium'],
        ]],
        ['form' => 5, 'name' => 'Merancang Pengendalian Perniagaan', 'subtopics' => [
            'Definisi Rancangan Perniagaan',
            'Kepentingan Menyediakan Rancangan Perniagaan',
            'Format Rancangan Perniagaan',
            'Bahagian Pengenalan Syarikat',
            'Bahagian Pemasaran dan Operasi',
            'Bahagian Sumber Manusia dan Kewangan',
            'Penyediaan Rancangan Perniagaan',
        ], 'skills' => [
            ['Menerangkan kepentingan rancangan perniagaan', 'medium'],
            ['Menghuraikan format rancangan perniagaan', 'medium'],
            ['Menyediakan rancangan perniagaan ringkas', 'hard'],
        ]],
    ];
}

/** Starter istilah perniagaan bank. */
function perniagaan_kssm_terms(): array
{
    return [
        // topic name, term, definition, example, category
        ['Tujuan Perniagaan dan Pemilikan Perniagaan', 'Pemilikan Tunggal', 'Perniagaan yang dimiliki dan dikendalikan oleh seorang individu sahaja, di mana pemilik bertanggungjawab penuh terhadap semua hutang.', 'Kedai runcit Pak Ali, kedai dobi.', 'pemilikan'],
        ['Tujuan Perniagaan dan Pemilikan Perniagaan', 'Perkongsian', 'Perniagaan yang dimiliki oleh 2 hingga 20 pekongsi yang berkongsi modal, untung dan tanggungjawab.', 'Firma guaman, firma audit.', 'pemilikan'],
        ['Tujuan Perniagaan dan Pemilikan Perniagaan', 'Syarikat Sendirian Berhad', 'Entiti perniagaan berasingan dengan minimum 1 dan maksimum 50 pemegang syer; tanggungan terhad kepada syer yang dipegang.', 'Tech startups, perniagaan keluarga berdaftar SSM.', 'pemilikan'],
        ['Tujuan Perniagaan dan Pemilikan Perniagaan', 'Syarikat Awam Berhad', 'Syarikat yang menjual syer kepada orang awam dan disenaraikan di Bursa Malaysia.', 'Maybank, Maxis, Petronas Dagangan.', 'pemilikan'],
        ['Tujuan Perniagaan dan Pemilikan Perniagaan', 'Koperasi', 'Pertubuhan sukarela yang dimiliki dan dikawal secara demokratik oleh ahli-ahlinya untuk faedah bersama.', 'Bank Rakyat, KOJADI.', 'pemilikan'],
        ['Tujuan Perniagaan dan Pemilikan Perniagaan', 'Perniagaan Kecil dan Sederhana (PKS)', 'Perniagaan dengan jualan tahunan tidak melebihi RM50 juta atau pekerja tidak melebihi 200 orang (sektor pembuatan).', 'Kedai kek, bengkel kereta tempatan.', 'klasifikasi'],

        ['Trend Semasa dalam Perniagaan', 'E-dagang', 'Aktiviti jual beli barang dan perkhidmatan melalui Internet.', 'Shopee, Lazada, Mudah.my.', 'trend'],
        ['Trend Semasa dalam Perniagaan', 'Gig Economy', 'Sistem ekonomi di mana pekerjaan bersifat sementara, fleksibel dan bebas tanpa kontrak jangka panjang.', 'Grab driver, Foodpanda rider, freelancer.', 'trend'],
        ['Trend Semasa dalam Perniagaan', 'Perniagaan Hijau', 'Perniagaan yang mengamalkan operasi mesra alam dan lestari.', 'Kafe sifar sisa, kedai produk biodegradable.', 'trend'],

        ['Penetapan Visi, Misi dan Objektif Perniagaan', 'Visi', 'Gambaran jangka panjang tentang apa yang ingin dicapai oleh sesebuah perniagaan pada masa hadapan.', 'Visi Petronas: "A leading oil and gas multinational of choice."', 'organisasi'],
        ['Penetapan Visi, Misi dan Objektif Perniagaan', 'Misi', 'Pernyataan tujuan dan cara perniagaan mencapai visi.', 'Misi: "Menyediakan tenaga mampan untuk semua."', 'organisasi'],
        ['Penetapan Visi, Misi dan Objektif Perniagaan', 'Objektif SMART', 'Objektif yang Spesifik, boleh diukur (Measurable), Achievable, Realistik dan Terikat masa (Time-bound).', 'Meningkatkan jualan 15% dalam tempoh 12 bulan.', 'organisasi'],

        ['Bahagian-bahagian Fungsian Utama dalam Organisasi Perniagaan', 'Bahagian Pemasaran', 'Bahagian yang bertanggungjawab mempromosi dan menjual produk atau perkhidmatan kepada pelanggan.', 'Iklan TV, kempen media sosial, kajian pasaran.', 'fungsian'],
        ['Bahagian-bahagian Fungsian Utama dalam Organisasi Perniagaan', 'Bahagian Sumber Manusia', 'Bahagian yang menguruskan pengambilan, latihan dan kebajikan pekerja.', 'Rekrutmen graduan baharu, latihan kepimpinan.', 'fungsian'],
        ['Bahagian-bahagian Fungsian Utama dalam Organisasi Perniagaan', 'Bahagian Penyelidikan dan Pembangunan (R&D)', 'Bahagian yang membangunkan produk baharu dan menambah baik produk sedia ada.', 'Pembangunan vaksin baharu, formula baharu makanan.', 'fungsian'],

        ['Pengurusan Sumber Manusia', 'Pengambilan Pekerja', 'Proses menarik dan memilih calon yang sesuai untuk mengisi jawatan kosong.', 'Pengiklanan kerja, temu duga, ujian psikometrik.', 'sumber_manusia'],
        ['Pengurusan Sumber Manusia', 'Penilaian Prestasi', 'Proses sistematik untuk menilai kerja dan sumbangan pekerja.', 'KPI tahunan, sesi maklum balas 360 darjah.', 'sumber_manusia'],
        ['Pengurusan Sumber Manusia', 'Kesatuan Sekerja', 'Pertubuhan pekerja yang ditubuhkan untuk melindungi kepentingan dan kebajikan ahli.', 'NUBE, MTUC.', 'sumber_manusia'],

        ['Sumber Pembiayaan Perniagaan', 'Modal Teroka (Venture Capital)', 'Pelaburan oleh syarikat khusus dalam perniagaan baharu berpotensi tinggi sebagai pertukaran syer ekuiti.', 'Sequoia, Gobi Partners.', 'kewangan'],
        ['Sumber Pembiayaan Perniagaan', 'Crowdfunding', 'Pengumpulan dana dalam jumlah kecil daripada ramai orang melalui platform dalam talian.', 'pitchIN, Kickstarter.', 'kewangan'],
        ['Sumber Pembiayaan Perniagaan', 'Pajakan (Leasing)', 'Penyewaan aset jangka panjang dengan pilihan untuk membeli pada akhir tempoh.', 'Pajakan kenderaan komersial, mesin pencetakan.', 'kewangan'],
        ['Sumber Pembiayaan Perniagaan', 'Untung Tertahan', 'Untung perniagaan yang tidak diagihkan kepada pemilik dan dilabur semula ke dalam perniagaan.', 'Dana pengembangan kilang baharu.', 'kewangan'],

        ['Penyata Kewangan Perniagaan', 'Penyata Aliran Tunai', 'Penyata yang menunjukkan aliran masuk dan keluar tunai sesebuah perniagaan dalam tempoh tertentu.', 'Aliran tunai bulanan PKS.', 'kewangan'],
        ['Penyata Kewangan Perniagaan', 'Belanjawan', 'Rancangan kewangan terperinci untuk tempoh tertentu yang menunjukkan jangkaan pendapatan dan perbelanjaan.', 'Belanjawan operasi suku tahunan.', 'kewangan'],

        ['Persediaan Menjadi Usahawan', 'Usahawan', 'Individu yang mengenal pasti peluang perniagaan dan mengambil risiko untuk menubuhkan dan menjalankan perniagaan.', 'Tony Fernandes (AirAsia), Vincent Tan (Berjaya).', 'usahawan'],
        ['Persediaan Menjadi Usahawan', 'Ciri Usahawan Berjaya', 'Kreatif, inovatif, berani mengambil risiko, gigih, berdaya tahan, mempunyai visi.', 'Tabah menghadapi kegagalan pertama dan terus mencuba.', 'usahawan'],

        ['Persediaan Memulakan Perniagaan', 'Suruhanjaya Syarikat Malaysia (SSM)', 'Badan kerajaan yang bertanggungjawab mendaftarkan perniagaan dan syarikat di Malaysia.', 'Pendaftaran ROB untuk pemilikan tunggal/perkongsian, ROC untuk Sdn Bhd.', 'pemasaran'],
        ['Persediaan Memulakan Perniagaan', 'Pemasaran 4P', 'Strategi pemasaran berdasarkan empat unsur: Product, Price, Place, Promotion.', 'Produk baharu (Product), harga promosi (Price), kedai pelbagai cawangan (Place), iklan Tiktok (Promotion).', 'pemasaran'],
        ['Persediaan Memulakan Perniagaan', 'Segmentasi Pasaran', 'Pembahagian pasaran kepada kumpulan pengguna dengan ciri yang sama untuk pemasaran lebih berkesan.', 'Pasaran remaja, dewasa muda, warga emas.', 'pemasaran'],

        ['Merancang Pengendalian Perniagaan', 'Rancangan Perniagaan', 'Dokumen bertulis yang menerangkan idea perniagaan, matlamat, strategi dan unjuran kewangan.', 'Business Plan untuk memohon pinjaman SME Bank.', 'rancangan_perniagaan'],
        ['Merancang Pengendalian Perniagaan', 'Pengenalan Syarikat', 'Bahagian rancangan perniagaan yang menerangkan latar belakang, struktur dan visi/misi perniagaan.', 'Profil syarikat, struktur pemilikan.', 'rancangan_perniagaan'],
        ['Merancang Pengendalian Perniagaan', 'Unjuran Kewangan', 'Anggaran pendapatan, kos dan untung perniagaan untuk 3-5 tahun akan datang.', 'Unjuran penyata pendapatan, aliran tunai 3 tahun.', 'rancangan_perniagaan'],
    ];
}

function perniagaan_kssm_run(): array
{
    $subject = db_one("SELECT id FROM subjects WHERE slug = 'perniagaan' LIMIT 1");
    if (!$subject) {
        return ['status' => 'no_subject', 'message' => 'Perniagaan subject not found. Run install / seed_subjects first.'];
    }
    $sid = (int) $subject['id'];

    $termsTable = (bool) db_one("SELECT 1 AS x FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'perniagaan_terms'");

    $catalog = perniagaan_kssm_catalog();
    $topicsCreated = $topicsKept = $topicsAdopted = 0;
    $subCreated = $subKept = 0;
    $skillsCreated = $skillsKept = 0;
    $termsCreated = $termsKept = 0;

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

    if ($termsTable) {
        foreach (perniagaan_kssm_terms() as $idx => [$topicName, $term, $def, $example, $category]) {
            $tid = $topicIds[$topicName] ?? null;
            $existing = db_one('SELECT id FROM perniagaan_terms WHERE term = ? LIMIT 1', [$term]);
            if ($existing) {
                $termsKept++;
            } else {
                db_exec(
                    'INSERT INTO perniagaan_terms (topic_id, topic_label, term, definition, example, category, sort_order)
                     VALUES (?,?,?,?,?,?,?)',
                    [$tid, $topicName, $term, $def, $example, $category, $idx + 1]
                );
                $termsCreated++;
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
        'terms_created'      => $termsCreated,
        'terms_kept'         => $termsKept,
        'catalog_topics'     => count($catalog),
        'terms_table'        => $termsTable,
    ];
}

if (PHP_SAPI === 'cli') {
    $r = perniagaan_kssm_run();
    if (($r['status'] ?? '') !== 'ok') {
        fwrite(STDERR, ($r['message'] ?? 'unknown error') . "\n");
        exit(1);
    }
    echo "KSSM Perniagaan seeded —\n";
    echo "  Bab:       created {$r['topics_created']}, adopted {$r['topics_adopted']}, kept {$r['topics_kept']} (catalog {$r['catalog_topics']})\n";
    echo "  Subtopik:  created {$r['subtopics_created']}, kept {$r['subtopics_kept']}\n";
    echo "  Kemahiran: created {$r['skills_created']}, kept {$r['skills_kept']}\n";
    if ($r['terms_table']) {
        echo "  Istilah:   created {$r['terms_created']}, kept {$r['terms_kept']}\n";
    } else {
        echo "  Istilah:   table missing — run database migrations to enable.\n";
    }
}
