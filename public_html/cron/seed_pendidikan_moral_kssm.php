<?php
/**
 * KSSM SPM Pendidikan Moral syllabus seeder. Tingkatan 4 (12 unit dalam
 * 3 bidang) + Tingkatan 5 (12 unit dalam 3 bidang) — 24 unit total,
 * organised by Bidang 5 (Insan Bermoral), Bidang 6 (Jati Diri Moral),
 * Bidang 7 (Moral dan Kenegaraan), dengan starter bank 18 nilai utama
 * KSSM yang digunakan oleh AI Moral Trainer dan flashcard nilai.
 *
 * Idempotent: unit matched by (subject_id, name, form_level); legacy
 * same-named NULL-form unit are adopted.
 *
 *   php public_html/cron/seed_pendidikan_moral_kssm.php
 */
declare(strict_types=1);

require_once __DIR__ . '/../inc/db.php';

function pendidikan_moral_kssm_catalog(): array
{
    return [
        // ============= TINGKATAN 4 =============
        ['form' => 4, 'name' => 'Unit 1: Norma Masyarakat Pemangkin Kesejahteraan', 'subtopics' => [
            'Konsep Norma Masyarakat', 'Jenis Norma (Cara, Resam, Adat, Undang-undang)',
            'Kepentingan Norma dalam Masyarakat', 'Mematuhi Norma Mewujudkan Kesejahteraan',
        ], 'skills' => [
            ['Menjelaskan konsep norma masyarakat', 'easy'],
            ['Menganalisis kepentingan norma', 'medium'],
            ['Mengamalkan norma dalam kehidupan', 'medium'],
        ]],
        ['form' => 4, 'name' => 'Unit 2: Peribadi Mulia Hiasan Diri', 'subtopics' => [
            'Sifat-sifat Peribadi Mulia', 'Hemah Tinggi dan Budi Bahasa',
            'Hormat-Menghormati', 'Akhlak Terpuji',
        ], 'skills' => [
            ['Mengenal pasti sifat peribadi mulia', 'easy'],
            ['Menghuraikan kepentingan akhlak', 'medium'],
            ['Mengamalkan budi bahasa dalam pergaulan', 'medium'],
        ]],
        ['form' => 4, 'name' => 'Unit 3: Prinsip Keadilan dan Keprihatinan dalam Membuat Keputusan', 'subtopics' => [
            'Konsep Keadilan', 'Prinsip Keprihatinan', 'Proses Membuat Keputusan Bermoral',
            'Implikasi Keputusan terhadap Diri dan Masyarakat',
        ], 'skills' => [
            ['Menjelaskan prinsip keadilan', 'medium'],
            ['Menganalisis dilema moral', 'hard'],
            ['Membuat keputusan beretika', 'medium'],
        ]],
        ['form' => 4, 'name' => 'Unit 4: Penggunaan Teknologi Maklumat dan Komunikasi Secara Beretika', 'subtopics' => [
            'Etika Digital', 'Jenayah Siber', 'Privasi dan Keselamatan Maklumat',
            'Tanggungjawab Pengguna Internet',
        ], 'skills' => [
            ['Mengenal pasti etika penggunaan ICT', 'medium'],
            ['Menganalisis kesan jenayah siber', 'medium'],
            ['Mengamalkan etika digital', 'medium'],
        ]],
        ['form' => 4, 'name' => 'Unit 5: Mantap Integriti Mantaplah Jati Diri', 'subtopics' => [
            'Konsep Integriti', 'Ciri Individu Berintegriti',
            'Hubungan Integriti dengan Jati Diri', 'Kepentingan Integriti',
        ], 'skills' => [
            ['Mentakrifkan integriti', 'easy'],
            ['Menjelaskan ciri individu berintegriti', 'medium'],
            ['Menilai kepentingan jati diri', 'medium'],
        ]],
        ['form' => 4, 'name' => 'Unit 6: Keluarga Berintegriti Keluarga Disegani', 'subtopics' => [
            'Peranan Ahli Keluarga', 'Nilai Integriti dalam Keluarga',
            'Komunikasi Berkesan', 'Cabaran dan Penyelesaian Konflik',
        ], 'skills' => [
            ['Menjelaskan peranan ahli keluarga', 'easy'],
            ['Menganalisis cabaran institusi keluarga', 'medium'],
            ['Mencadangkan cara mengukuhkan keluarga', 'medium'],
        ]],
        ['form' => 4, 'name' => 'Unit 7: Berperikemanusiaan Membentuk Masyarakat Sejahtera', 'subtopics' => [
            'Konsep Perikemanusiaan', 'Bantuan Kemanusiaan',
            'Sumbangan kepada Masyarakat', 'Empati dan Simpati',
        ], 'skills' => [
            ['Menjelaskan konsep perikemanusiaan', 'medium'],
            ['Mengamalkan sifat empati', 'medium'],
            ['Menilai sumbangan kepada masyarakat', 'medium'],
        ]],
        ['form' => 4, 'name' => 'Unit 8: Hak dan Tanggungjawab Warganegara', 'subtopics' => [
            'Hak Asasi dalam Perlembagaan', 'Tanggungjawab Warganegara',
            'Patuh kepada Undang-undang', 'Cinta akan Negara',
        ], 'skills' => [
            ['Mengenal pasti hak warganegara', 'easy'],
            ['Menghuraikan tanggungjawab warganegara', 'medium'],
            ['Mengamalkan semangat patriotik', 'medium'],
        ]],
        ['form' => 4, 'name' => 'Unit 9: Perpaduan Masyarakat Asas Kemakmuran Negara', 'subtopics' => [
            'Konsep Perpaduan', 'Faktor Mempengaruhi Perpaduan',
            'Cabaran kepada Perpaduan', 'Usaha Mengukuhkan Perpaduan',
        ], 'skills' => [
            ['Menjelaskan konsep perpaduan', 'medium'],
            ['Menganalisis cabaran perpaduan', 'medium'],
            ['Mencadangkan usaha mengukuhkan perpaduan', 'medium'],
        ]],
        ['form' => 4, 'name' => 'Unit 10: Pengurusan Perbelanjaan Secara Beretika', 'subtopics' => [
            'Konsep Perbelanjaan Bijak', 'Belanjawan Peribadi',
            'Etika Penggunaan', 'Tabungan dan Pelaburan',
        ], 'skills' => [
            ['Menyediakan belanjawan peribadi', 'medium'],
            ['Menganalisis etika kepenggunaan', 'medium'],
            ['Mengamalkan perbelanjaan berhemah', 'medium'],
        ]],
        ['form' => 4, 'name' => 'Unit 11: Keunikan Rakyat Malaysia', 'subtopics' => [
            'Kepelbagaian Kaum dan Budaya', 'Adat dan Pakaian Tradisional',
            'Bahasa dan Bahasa Pasar', 'Toleransi Beragama',
        ], 'skills' => [
            ['Mengenal pasti keunikan budaya Malaysia', 'easy'],
            ['Menghargai kepelbagaian etnik', 'medium'],
            ['Menilai kepentingan toleransi', 'medium'],
        ]],
        ['form' => 4, 'name' => 'Unit 12: Kedaulatan Negara Tanggungjawab Bersama', 'subtopics' => [
            'Konsep Kedaulatan Negara', 'Lambang Kedaulatan',
            'Ancaman terhadap Kedaulatan', 'Peranan Rakyat Mempertahankan Negara',
        ], 'skills' => [
            ['Menjelaskan konsep kedaulatan', 'medium'],
            ['Menghuraikan lambang negara', 'easy'],
            ['Menilai peranan rakyat mempertahankan negara', 'medium'],
        ]],

        // ============= TINGKATAN 5 =============
        ['form' => 5, 'name' => 'Unit 1: Norma Masyarakat Global Membentuk Keharmonian Sejagat', 'subtopics' => [
            'Norma Masyarakat Global', 'Perbezaan Budaya Antarabangsa',
            'Keharmonian Sejagat', 'Globalisasi dan Nilai Universal',
        ], 'skills' => [
            ['Menjelaskan norma masyarakat global', 'medium'],
            ['Menganalisis kesan globalisasi', 'medium'],
            ['Menghargai keharmonian sejagat', 'medium'],
        ]],
        ['form' => 5, 'name' => 'Unit 2: Ukir Nama di Mata Dunia, Jati Diri Berpisah Tiada', 'subtopics' => [
            'Kecemerlangan Diri di Persada Dunia', 'Memelihara Jati Diri',
            'Tokoh Malaysia Cemerlang Dunia', 'Cabaran Mempertahankan Jati Diri',
        ], 'skills' => [
            ['Menjelaskan kepentingan jati diri', 'medium'],
            ['Menilai tokoh Malaysia di pentas dunia', 'medium'],
            ['Mencadangkan cara mengukir nama negara', 'medium'],
        ]],
        ['form' => 5, 'name' => 'Unit 3: Kerohanian Membentuk Individu Bermoral', 'subtopics' => [
            'Konsep Kerohanian', 'Nilai Agama dalam Kehidupan',
            'Disiplin Rohani', 'Hubungan Kerohanian dengan Moral',
        ], 'skills' => [
            ['Menjelaskan konsep kerohanian', 'medium'],
            ['Mengamalkan nilai keagamaan', 'medium'],
            ['Menilai kesan kerohanian terhadap moral', 'medium'],
        ]],
        ['form' => 5, 'name' => 'Unit 4: Penggunaan Sumber dan Penyebaran Maklumat demi Kesejahteraan Sejagat', 'subtopics' => [
            'Pengurusan Sumber Lestari', 'Penyebaran Maklumat Beretika',
            'Sumber Terhad dan Tanggungjawab Generasi', 'Media Sosial dan Maklumat Palsu',
        ], 'skills' => [
            ['Menjelaskan pengurusan sumber lestari', 'medium'],
            ['Mengenal pasti maklumat palsu', 'medium'],
            ['Menyebarkan maklumat secara beretika', 'medium'],
        ]],
        ['form' => 5, 'name' => 'Unit 5: Integriti Pemacu Kecemerlangan Organisasi', 'subtopics' => [
            'Integriti dalam Organisasi', 'Tadbir Urus Baik',
            'Anti-Rasuah', 'Budaya Kerja Berintegriti',
        ], 'skills' => [
            ['Menjelaskan integriti dalam organisasi', 'medium'],
            ['Menganalisis kesan rasuah', 'medium'],
            ['Membincangkan budaya kerja beretika', 'medium'],
        ]],
        ['form' => 5, 'name' => 'Unit 6: Pembangunan Negara Bertunjangkan Integriti', 'subtopics' => [
            'Integriti dalam Pembangunan Negara', 'Peranan Pelan Integriti Nasional',
            'Suruhanjaya Pencegahan Rasuah Malaysia (SPRM)', 'Membina Negara Bebas Rasuah',
        ], 'skills' => [
            ['Menjelaskan kepentingan integriti dalam pembangunan', 'medium'],
            ['Menilai peranan SPRM', 'medium'],
            ['Mencadangkan langkah membina negara berintegriti', 'medium'],
        ]],
        ['form' => 5, 'name' => 'Unit 7: Berperikemanusiaan Pemangkin Kesejahteraan Global', 'subtopics' => [
            'Bantuan Kemanusiaan Antarabangsa', 'Organisasi Kemanusiaan (PBB, ICRC, MERCY Malaysia)',
            'Pelarian dan Mangsa Bencana', 'Sumbangan Malaysia dalam Misi Kemanusiaan',
        ], 'skills' => [
            ['Menjelaskan peranan organisasi kemanusiaan', 'medium'],
            ['Menilai sumbangan Malaysia dalam misi kemanusiaan', 'medium'],
            ['Membincangkan tanggungjawab terhadap pelarian', 'medium'],
        ]],
        ['form' => 5, 'name' => 'Unit 8: Penglibatan Diri Peneraju Komuniti', 'subtopics' => [
            'Konsep Sukarelawan', 'Khidmat Komuniti',
            'Kepimpinan Belia', 'Sumbangan kepada Komuniti',
        ], 'skills' => [
            ['Menjelaskan konsep sukarelawan', 'easy'],
            ['Membincangkan kepimpinan belia', 'medium'],
            ['Mencadangkan aktiviti khidmat komuniti', 'medium'],
        ]],
        ['form' => 5, 'name' => 'Unit 9: Kerjasama Masyarakat Global', 'subtopics' => [
            'Kerjasama Antarabangsa', 'Pertubuhan Antarabangsa (PBB, ASEAN, OIC, NAM)',
            'Sustainable Development Goals (SDG)', 'Cabaran Global Bersama',
        ], 'skills' => [
            ['Menjelaskan kerjasama antarabangsa', 'medium'],
            ['Menilai peranan pertubuhan antarabangsa', 'medium'],
            ['Membincangkan SDG dan tanggungjawab global', 'medium'],
        ]],
        ['form' => 5, 'name' => 'Unit 10: Pengurusan Kewangan Secara Beretika Menjamin Keharmonian Hidup', 'subtopics' => [
            'Belanjawan dan Tabungan', 'Pinjaman dan Hutang Beretika',
            'Pelaburan Berhemah', 'Mengelak Penipuan Kewangan',
        ], 'skills' => [
            ['Menyediakan belanjawan kewangan', 'medium'],
            ['Mengenal pasti pelaburan beretika', 'medium'],
            ['Mengelak risiko penipuan kewangan', 'medium'],
        ]],
        ['form' => 5, 'name' => 'Unit 11: Malaysia Unggul di Mata Dunia', 'subtopics' => [
            'Kecemerlangan Malaysia di Pelbagai Bidang', 'Tokoh Negara di Persada Dunia',
            'Sukan, Sains dan Teknologi', 'Diplomasi dan Pengaruh Global',
        ], 'skills' => [
            ['Menilai kecemerlangan Malaysia', 'medium'],
            ['Menghuraikan sumbangan tokoh Malaysia', 'medium'],
            ['Mengamalkan semangat patriotik', 'medium'],
        ]],
        ['form' => 5, 'name' => 'Unit 12: Malaysia dalam Hubungan Antarabangsa', 'subtopics' => [
            'Dasar Luar Malaysia', 'Hubungan Diplomatik',
            'Misi Pengaman dan Sumbangan PBB', 'Cabaran Hubungan Antarabangsa',
        ], 'skills' => [
            ['Menjelaskan dasar luar Malaysia', 'medium'],
            ['Menilai sumbangan Malaysia dalam misi pengaman', 'medium'],
            ['Menganalisis cabaran hubungan antarabangsa', 'medium'],
        ]],
    ];
}

/** 18 nilai utama KSSM Pendidikan Moral. */
function pendidikan_moral_kssm_values(): array
{
    return [
        // value_name, definition, example, keywords, category
        ['Kepercayaan kepada Tuhan',
         'Keyakinan akan adanya Tuhan sebagai pencipta alam dan mematuhi segala suruhan-Nya berlandaskan pegangan agama masing-masing.',
         'Beribadah, berdoa, mengamalkan ajaran agama dengan ikhlas.',
         'iman, taat, ibadat, agama, ketuhanan',
         'kerohanian'],
        ['Baik hati',
         'Kepekaan terhadap perasaan dan kebajikan diri sendiri dan orang lain dengan memberi bantuan serta sokongan moral secara tulus.',
         'Menderma kepada mangsa banjir, membantu rakan yang sakit.',
         'belas kasihan, bertimbang rasa, murah hati, simpati',
         'sesama'],
        ['Bertanggungjawab',
         'Kesanggupan diri seseorang untuk memikul dan melaksanakan tugas serta kewajipan dengan sempurna.',
         'Menyiapkan kerja sekolah tepat pada masanya, menjaga adik-adik.',
         'amanah, berdisiplin, dedikasi, akauntabiliti',
         'diri'],
        ['Berterima kasih',
         'Perasaan dan perlakuan untuk menunjukkan pengiktirafan dan penghargaan terhadap sesuatu jasa, sumbangan atau pemberian.',
         'Mengucapkan "terima kasih" kepada guru selepas pelajaran.',
         'menghargai, mengenang jasa, mengiktiraf',
         'sesama'],
        ['Hemah tinggi',
         'Beradab sopan dan berbudi pekerti mulia dalam pergaulan seharian.',
         'Memberi salam kepada orang tua, bertutur dengan lembut.',
         'sopan, beradab, budi pekerti, lemah lembut',
         'diri'],
        ['Hormat',
         'Menghargai dan memuliakan seseorang atau sesuatu institusi dengan memberi layanan yang sopan.',
         'Hormati pendapat orang lain dalam perbincangan kelas.',
         'menghargai, memuliakan, taat, sopan santun',
         'sesama'],
        ['Kasih sayang',
         'Perasaan cinta, kasih dan sayang yang mendalam dan berkekalan terhadap diri, keluarga, sahabat dan negara.',
         'Menyayangi adik-beradik, prihatin terhadap ibu bapa.',
         'cinta, sayang, kasihan, mesra',
         'sesama'],
        ['Keadilan',
         'Tindakan dan keputusan yang saksama serta tidak berat sebelah berlandaskan prinsip moral dan undang-undang.',
         'Membahagi tugas secara saksama, tidak pilih kasih.',
         'saksama, ragsam, hak, tidak berat sebelah',
         'sesama'],
        ['Kebebasan',
         'Kebenaran melakukan sesuatu dalam ruang lingkup undang-undang, peraturan dan adat resam.',
         'Bebas memilih kerjaya selepas SPM, dengan mematuhi undang-undang.',
         'hak, kebebasan bersuara, demokrasi',
         'diri'],
        ['Keberanian',
         'Kesanggupan untuk menghadapi cabaran dengan yakin dan tabah berlandaskan prinsip yang benar.',
         'Berani menegur kawan yang merokok, berani membantu mangsa kemalangan.',
         'tabah, yakin, gagah, berani moral',
         'diri'],
        ['Kebersihan fizikal dan mental',
         'Kebersihan diri, persekitaran, perlakuan, pertuturan dan pemikiran.',
         'Mandi dua kali sehari, mengelak kata-kata kesat, berfikir positif.',
         'kebersihan, kesihatan, positif, suci',
         'diri'],
        ['Kejujuran',
         'Bercakap benar, bersikap amanah dan ikhlas tanpa menipu dalam apa-apa keadaan.',
         'Mengembalikan duit baki, mengakui kesilapan.',
         'amanah, ikhlas, benar, telus',
         'diri'],
        ['Kerajinan',
         'Usaha berterusan dengan penuh semangat dan ketekunan untuk mencapai sesuatu kejayaan.',
         'Mengulang kaji setiap hari, membantu kerja rumah tangga.',
         'tekun, gigih, berusaha, dedikasi',
         'diri'],
        ['Kerjasama',
         'Usaha yang baik dan membina secara bersama-sama pada peringkat individu, komuniti dan negara.',
         'Gotong-royong membersih kawasan sekolah, kerja kumpulan dalam projek.',
         'gotong-royong, kolaborasi, muafakat, bersepadu',
         'sesama'],
        ['Kesederhanaan',
         'Bersikap tidak keterlaluan dalam membuat pertimbangan dan tindakan, sama ada dalam pemikiran atau perlakuan.',
         'Berbelanja mengikut kemampuan, bersikap tidak melampau dalam reaksi.',
         'sederhana, berpada, seimbang, neutral',
         'diri'],
        ['Toleransi',
         'Kesanggupan bertolak ansur, sabar dan mengawal diri demi mengelakkan perselisihan demi keharmonian.',
         'Bertolak ansur dengan jiran berlainan agama, menerima perbezaan budaya.',
         'tolak ansur, sabar, terima, saling menghormati',
         'sesama'],
        ['Patriotisme',
         'Perasaan cinta yang mendalam dan berkekalan terhadap tanah air.',
         'Menyanyikan lagu Negaraku dengan bersungguh-sungguh, menghormati bendera Jalur Gemilang.',
         'cinta negara, taat setia, bangga, semangat kebangsaan',
         'negara'],
        ['Rasional',
         'Boleh berfikir secara waras dan adil berdasarkan alasan dan bukti yang nyata.',
         'Mengkaji fakta sebelum mempercayai berita, membuat keputusan berasaskan logik.',
         'logik, waras, adil, berfikir kritis',
         'diri'],
    ];
}

function pendidikan_moral_kssm_run(): array
{
    $subject = db_one("SELECT id FROM subjects WHERE slug = 'pendidikan-moral' LIMIT 1");
    if (!$subject) {
        return ['status' => 'no_subject', 'message' => 'Pendidikan Moral subject not found. Run install / seed_subjects first.'];
    }
    $sid = (int) $subject['id'];

    $valuesTable = (bool) db_one("SELECT 1 AS x FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'moral_values'");

    $catalog = pendidikan_moral_kssm_catalog();
    $topicsCreated = $topicsKept = $topicsAdopted = 0;
    $subCreated = $subKept = 0;
    $skillsCreated = $skillsKept = 0;
    $valuesCreated = $valuesKept = 0;

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

    if ($valuesTable) {
        foreach (pendidikan_moral_kssm_values() as $idx => [$name, $def, $example, $keywords, $category]) {
            $existing = db_one('SELECT id FROM moral_values WHERE value_name = ? LIMIT 1', [$name]);
            if ($existing) {
                $valuesKept++;
            } else {
                db_exec(
                    'INSERT INTO moral_values (value_name, definition, example, keywords, category, sort_order)
                     VALUES (?,?,?,?,?,?)',
                    [$name, $def, $example, $keywords, $category, $idx + 1]
                );
                $valuesCreated++;
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
        'values_created'     => $valuesCreated,
        'values_kept'        => $valuesKept,
        'catalog_topics'     => count($catalog),
        'values_table'       => $valuesTable,
    ];
}

if (PHP_SAPI === 'cli') {
    $r = pendidikan_moral_kssm_run();
    if (($r['status'] ?? '') !== 'ok') {
        fwrite(STDERR, ($r['message'] ?? 'unknown error') . "\n");
        exit(1);
    }
    echo "KSSM Pendidikan Moral seeded —\n";
    echo "  Unit:        created {$r['topics_created']}, adopted {$r['topics_adopted']}, kept {$r['topics_kept']} (catalog {$r['catalog_topics']})\n";
    echo "  Subtopik:    created {$r['subtopics_created']}, kept {$r['subtopics_kept']}\n";
    echo "  Kemahiran:   created {$r['skills_created']}, kept {$r['skills_kept']}\n";
    if ($r['values_table']) {
        echo "  Nilai Utama: created {$r['values_created']}, kept {$r['values_kept']}\n";
    } else {
        echo "  Nilai Utama: table missing — run database migrations to enable.\n";
    }
}
