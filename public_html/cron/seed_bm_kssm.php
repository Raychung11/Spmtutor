<?php
/**
 * KSSM SPM Bahasa Melayu syllabus seeder. Form 4 (7 topics) + Form 5
 * (7 topics), with subtopics + starter skills, plus a peribahasa /
 * simpulan bahasa / bidalan / pepatah / cogan kata reference bank.
 *
 * Idempotent: topics matched by (subject_id, name, form_level); legacy
 * same-named NULL-form topics are adopted.
 *
 *   php public_html/cron/seed_bm_kssm.php
 */
declare(strict_types=1);

require_once __DIR__ . '/../inc/db.php';

function bm_kssm_catalog(): array
{
    return [
        // ============= FORM 4 =============
        ['form' => 4, 'name' => 'Kemahiran Mendengar dan Bertutur', 'subtopics' => [
            'Mendengar Maklumat', 'Memberi Respons', 'Perbincangan', 'Pembentangan', 'Komunikasi Formal',
        ], 'skills' => [
            ['Mendengar maklumat penting', 'easy'],
            ['Menyampaikan pendapat', 'medium'],
            ['Berkomunikasi secara berkesan', 'medium'],
        ]],
        ['form' => 4, 'name' => 'Sistem dan Aplikasi Bahasa', 'subtopics' => [
            'Kata Nama', 'Kata Kerja', 'Kata Adjektif', 'Kata Tugas', 'Frasa', 'Klausa', 'Ayat',
        ], 'skills' => [
            ['Mengenal pasti golongan kata', 'easy'],
            ['Membina ayat gramatis', 'medium'],
            ['Membetulkan kesalahan bahasa', 'medium'],
        ]],
        ['form' => 4, 'name' => 'Tatabahasa', 'subtopics' => [
            'Kesalahan Ejaan', 'Kesalahan Imbuhan', 'Kesalahan Kata', 'Kesalahan Ayat',
        ], 'skills' => [
            ['Membetulkan kesalahan', 'medium'],
            ['Menggunakan tatabahasa yang betul', 'medium'],
        ]],
        ['form' => 4, 'name' => 'Pemahaman', 'subtopics' => [
            'Petikan Umum', 'Petikan Sastera', 'Soalan KBAT',
        ], 'skills' => [
            ['Mencari isi penting', 'easy'],
            ['Membuat inferens', 'medium'],
            ['Menjawab soalan KBAT', 'hard'],
        ]],
        ['form' => 4, 'name' => 'Rumusan', 'subtopics' => [
            'Isi Tersurat', 'Isi Tersirat', 'Kesimpulan',
        ], 'skills' => [
            ['Mengenal pasti isi', 'medium'],
            ['Menulis rumusan lengkap', 'medium'],
            ['Menepati jumlah perkataan', 'medium'],
        ]],
        ['form' => 4, 'name' => 'Karangan Pendek', 'subtopics' => [
            'Karangan Respons Terhad', 'Karangan Fakta', 'Karangan Pendapat',
        ], 'skills' => [
            ['Menulis isi tersusun', 'medium'],
            ['Menggunakan penanda wacana', 'medium'],
        ]],
        ['form' => 4, 'name' => 'KOMSAS', 'subtopics' => [
            'Puisi Tradisional', 'Puisi Moden', 'Cerpen', 'Drama', 'Novel',
        ], 'skills' => [
            ['Menganalisis karya', 'medium'],
            ['Menjawab soalan KOMSAS', 'medium'],
        ]],

        // ============= FORM 5 =============
        ['form' => 5, 'name' => 'Karangan Respons Terbuka', 'subtopics' => [
            'Karangan Fakta', 'Karangan Pendapat', 'Karangan Perbincangan', 'Karangan Ucapan',
            'Karangan Surat Rasmi', 'Karangan Surat Tidak Rasmi', 'Karangan Laporan',
        ], 'skills' => [
            ['Merancang isi', 'medium'],
            ['Mengembangkan idea', 'medium'],
            ['Menulis penutup berkesan', 'medium'],
        ]],
        ['form' => 5, 'name' => 'Karangan Cemerlang', 'subtopics' => [
            'Pendahuluan', 'Isi', 'Penutup', 'Teknik KBAT',
        ], 'skills' => [
            ['Menulis karangan 70-100 markah', 'hard'],
            ['Menggunakan peribahasa', 'medium'],
            ['Menggunakan ungkapan menarik', 'medium'],
        ]],
        ['form' => 5, 'name' => 'Rumusan Lanjutan', 'subtopics' => [
            'Gabungan Petikan', 'Rumusan KBAT',
        ], 'skills' => [
            ['Mengenal pasti tema', 'medium'],
            ['Menulis rumusan berkualiti', 'hard'],
        ]],
        ['form' => 5, 'name' => 'Pemahaman KBAT', 'subtopics' => [
            'Analisis', 'Penilaian', 'Penyelesaian Masalah',
        ], 'skills' => [
            ['Menjawab soalan aras tinggi', 'hard'],
            ['Membuat justifikasi', 'medium'],
        ]],
        ['form' => 5, 'name' => 'Tatabahasa Lanjutan', 'subtopics' => [
            'Peribahasa', 'Simpulan Bahasa', 'Bidalan', 'Pepatah', 'Cogan Kata',
        ], 'skills' => [
            ['Memilih peribahasa sesuai', 'medium'],
            ['Menggunakan dalam ayat', 'medium'],
        ]],
        ['form' => 5, 'name' => 'Novel', 'subtopics' => [
            'Tema', 'Persoalan', 'Watak', 'Perwatakan', 'Latar', 'Plot', 'Nilai', 'Pengajaran',
        ], 'skills' => [
            ['Analisis novel', 'medium'],
            ['Menjawab soalan esei', 'medium'],
        ]],
        ['form' => 5, 'name' => 'KOMSAS Tingkatan 5', 'subtopics' => [
            'Sajak', 'Syair', 'Cerpen', 'Drama', 'Novel',
        ], 'skills' => [
            ['Analisis karya', 'medium'],
            ['Menjawab soalan peperiksaan', 'medium'],
        ]],
    ];
}

/**
 * Starter peribahasa bank. type = peribahasa | simpulan_bahasa | bidalan
 *                              | pepatah | cogan_kata.
 */
function bm_kssm_peribahasa(): array
{
    return [
        // ---------- Peribahasa (umum) ----------
        ['peribahasa', 'Bagai aur dengan tebing', 'Hubungan yang sangat erat dan saling memerlukan antara satu sama lain.', 'Hubungan adik-beradik itu bagai aur dengan tebing.', 'persahabatan'],
        ['peribahasa', 'Berat sama dipikul, ringan sama dijinjing', 'Sehidup semati; sama-sama menanggung susah dan senang.', 'Penduduk kampung itu berat sama dipikul, ringan sama dijinjing semasa banjir.', 'kerjasama'],
        ['peribahasa', 'Bulat air kerana pembentung, bulat manusia kerana muafakat', 'Kesepakatan menghasilkan keputusan yang baik.', 'Muafakat ahli mesyuarat itu membuktikan bulat air kerana pembentung, bulat manusia kerana muafakat.', 'kerjasama'],
        ['peribahasa', 'Hujan emas di negeri orang, hujan batu di negeri sendiri, lebih baik di negeri sendiri', 'Tanah air lebih baik daripada negeri orang walaupun di sana lebih mewah.', 'Walaupun bekerja di luar negara, dia tetap pulang kerana hujan emas di negeri orang, hujan batu di negeri sendiri.', 'patriotik'],
        ['peribahasa', 'Bagai pinang dibelah dua', 'Dua orang yang sangat serupa.', 'Wajah kembar itu bagai pinang dibelah dua.', 'persamaan'],
        ['peribahasa', 'Bagaikan kaca terhempas ke batu', 'Sangat sedih atau hancur hati.', 'Hatinya bagaikan kaca terhempas ke batu apabila ibunya meninggal dunia.', 'kesedihan'],
        ['peribahasa', 'Sedikit-sedikit, lama-lama jadi bukit', 'Kerja yang sedikit jika dilakukan terus-menerus akan menjadi banyak.', 'Ali menabung wang sakunya kerana sedikit-sedikit, lama-lama jadi bukit.', 'usaha'],
        ['peribahasa', 'Di mana ada kemahuan, di situ ada jalan', 'Jika ada kemahuan yang kuat, pasti ada jalan untuk mencapainya.', 'Dia berjaya menamatkan pengajiannya kerana di mana ada kemahuan, di situ ada jalan.', 'usaha'],
        ['peribahasa', 'Air dicincang takkan putus', 'Pertengkaran antara saudara tidak akan memutuskan tali persaudaraan.', 'Walaupun bergaduh, mereka tetap berbaik kerana air dicincang takkan putus.', 'kekeluargaan'],
        ['peribahasa', 'Genggam bara api biar sampai jadi arang', 'Membuat sesuatu pekerjaan hendaklah sehingga selesai walaupun susah.', 'Ali berazam menghabiskan tugasannya kerana genggam bara api biar sampai jadi arang.', 'ketekunan'],

        // ---------- Simpulan Bahasa ----------
        ['simpulan_bahasa', 'Kaki ayam', 'Tidak berkasut atau bertelanjang kaki.', 'Anak kampung itu sering berjalan kaki ayam ke sekolah.', 'fizikal'],
        ['simpulan_bahasa', 'Kaki bangku', 'Orang yang tidak pandai bermain sukan.', 'Walaupun kaki bangku, dia tetap menyertai pasukan bola sepak.', 'sukan'],
        ['simpulan_bahasa', 'Buah tangan', 'Hadiah atau pemberian.', 'Ibu membawa buah tangan untuk nenek di kampung.', 'pemberian'],
        ['simpulan_bahasa', 'Buah hati', 'Orang yang dikasihi atau kekasih.', 'Adik kecil itu memang buah hati keluarga kami.', 'kasih sayang'],
        ['simpulan_bahasa', 'Anak emas', 'Orang yang sangat dikasihi atau dimanjakan.', 'Aiman menjadi anak emas guru disiplin sekolah.', 'kasih sayang'],
        ['simpulan_bahasa', 'Anak buah', 'Orang bawahan atau pekerja.', 'Pengurus itu sentiasa menjaga kebajikan anak buahnya.', 'pekerjaan'],
        ['simpulan_bahasa', 'Berat tangan', 'Malas bekerja atau enggan menolong.', 'Pelajar yang berat tangan sukar untuk berjaya.', 'sifat'],
        ['simpulan_bahasa', 'Ringan tulang', 'Rajin bekerja atau suka menolong.', 'Ali memang ringan tulang membantu jirannya yang uzur.', 'sifat'],
        ['simpulan_bahasa', 'Buah fikiran', 'Pendapat atau idea.', 'Mesyuarat itu memerlukan buah fikiran daripada semua ahli.', 'akal'],
        ['simpulan_bahasa', 'Lipat kain', 'Pekerjaan yang sangat mudah dilakukan.', 'Bagi ahli silap mata, helah itu seperti lipat kain sahaja.', 'kemudahan'],
        ['simpulan_bahasa', 'Mata duitan', 'Orang yang sangat mementingkan wang.', 'Jangan jadi mata duitan; persahabatan lebih berharga.', 'sifat'],
        ['simpulan_bahasa', 'Otak udang', 'Orang yang bodoh atau lambat berfikir.', 'Janganlah cepat menggelar orang lain otak udang.', 'sifat'],

        // ---------- Bidalan ----------
        ['bidalan', 'Biar lambat asalkan selamat', 'Lebih baik melakukan sesuatu dengan perlahan tetapi selamat daripada terburu-buru.', 'Pemandu itu berhati-hati di jalan licin kerana biar lambat asalkan selamat.', 'keselamatan'],
        ['bidalan', 'Sediakan payung sebelum hujan', 'Bersedia menghadapi sesuatu sebelum ia berlaku.', 'Belajar bersungguh-sungguh sekarang ibarat sediakan payung sebelum hujan.', 'persediaan'],
        ['bidalan', 'Carik-carik bulu ayam, lama-lama bercantum juga', 'Pergaduhan antara saudara akhirnya akan didamaikan juga.', 'Adik-beradik itu kini baik semula; carik-carik bulu ayam, lama-lama bercantum juga.', 'kekeluargaan'],
        ['bidalan', 'Bagai melepaskan batuk di tangga', 'Melakukan kerja sambil lewa, tidak sungguh-sungguh.', 'Tugasan yang dilakukan bagai melepaskan batuk di tangga itu tidak akan cemerlang.', 'sifat'],
        ['bidalan', 'Berakit-rakit ke hulu, berenang-renang ke tepian, bersakit-sakit dahulu, bersenang-senang kemudian', 'Bekerja keras dahulu, hasil baik akan datang kemudian.', 'Berakit-rakit ke hulu, berenang-renang ke tepian — kita perlu belajar bersungguh-sungguh sekarang untuk berjaya nanti.', 'usaha'],

        // ---------- Pepatah ----------
        ['pepatah', 'Hendak seribu daya, tak hendak seribu dalih', 'Jika sungguh-sungguh mahu, banyak cara; jika tidak, banyak alasan.', 'Tanpa usaha, kita hanya akan jadi seperti hendak seribu daya, tak hendak seribu dalih.', 'usaha'],
        ['pepatah', 'Yang berbukit ditambun, yang berlurah ditimbus', 'Berusaha bersungguh-sungguh sehingga sesuatu kerja itu sempurna.', 'Penduduk gotong-royong sehingga yang berbukit ditambun, yang berlurah ditimbus.', 'kerjasama'],
        ['pepatah', 'Ke bukit sama didaki, ke lurah sama dituruni', 'Sama-sama menanggung susah dan senang.', 'Kawan baik itu ke bukit sama didaki, ke lurah sama dituruni.', 'persahabatan'],
        ['pepatah', 'Bulat air kerana pembentung, bulat kata kerana muafakat', 'Keputusan yang baik datang dari permuafakatan.', 'Penduduk kampung berbincang sehingga bulat air kerana pembentung, bulat kata kerana muafakat.', 'kerjasama'],
        ['pepatah', 'Hujan tak boleh ditampung dengan tangan', 'Sesuatu yang tidak dapat dielakkan.', 'Musibah itu hujan tak boleh ditampung dengan tangan.', 'takdir'],

        // ---------- Cogan Kata ----------
        ['cogan_kata', 'Bersatu Padu', 'Slogan menyeru rakyat berganding bahu.', 'Cogan kata "Bersatu Padu" sering disebut sempena Hari Kebangsaan.', 'patriotik'],
        ['cogan_kata', 'Malaysia Berjaya', 'Slogan keberhasilan negara.', '"Malaysia Berjaya" merupakan harapan setiap warganegara.', 'patriotik'],
        ['cogan_kata', 'Sehati Sejiwa', 'Slogan perpaduan rakyat.', 'Tema sambutan tahun ini ialah "Sehati Sejiwa".', 'patriotik'],
        ['cogan_kata', 'Cintailah Bahasa Kita', 'Slogan menyeru rakyat mengangkat martabat bahasa Melayu.', 'Kempen "Cintailah Bahasa Kita" digerakkan oleh DBP.', 'bahasa'],
        ['cogan_kata', 'Hidup Bersih, Hidup Sihat', 'Slogan menyeru amalan kebersihan.', 'Sekolah memasang sepanduk "Hidup Bersih, Hidup Sihat" di kafeteria.', 'kesihatan'],

        // ---------- Tambahan peribahasa popular ----------
        ['peribahasa', 'Alah bisa tegal biasa', 'Sesuatu yang sukar akan menjadi mudah jika selalu dilakukan.', 'Berlari setiap pagi kini terasa mudah; alah bisa tegal biasa.', 'tabiat'],
        ['peribahasa', 'Tak kenal maka tak cinta', 'Untuk mencintai sesuatu, kita perlu mengenalinya dahulu.', 'Cintailah seni warisan kerana tak kenal maka tak cinta.', 'pengetahuan'],
        ['peribahasa', 'Yang kurik kundi, yang merah saga; yang baik budi, yang indah bahasa', 'Budi pekerti dan tutur kata yang baik adalah perhiasan diri.', 'Pemuda itu disenangi semua kerana yang baik budi, yang indah bahasa.', 'budi pekerti'],
        ['peribahasa', 'Bagai melukut di tepi gantang', 'Orang yang tidak mempunyai pengaruh atau kepentingan.', 'Dalam mesyuarat itu, suaranya bagai melukut di tepi gantang.', 'kedudukan'],
        ['peribahasa', 'Bermain api hangus, bermain air basah', 'Apa-apa perbuatan pasti ada kesannya.', 'Berhati-hati dengan ujaran kebencian — bermain api hangus, bermain air basah.', 'akibat'],
        ['peribahasa', 'Diam-diam ubi berisi, diam-diam besi berkarat', 'Orang yang pendiam mungkin berilmu; orang yang malas akan lebih jahil.', 'Pelajar pendiam itu juara debat — diam-diam ubi berisi.', 'sifat'],
        ['peribahasa', 'Indah khabar daripada rupa', 'Cerita lebih hebat daripada keadaan sebenar.', 'Resort itu indah khabar daripada rupa; biliknya sempit.', 'penilaian'],
        ['peribahasa', 'Sepandai-pandai tupai melompat, akhirnya jatuh ke tanah juga', 'Orang yang pintar pun ada kalanya tersilap.', 'Ahli silap mata itu terdedah helahnya — sepandai-pandai tupai melompat, akhirnya jatuh ke tanah juga.', 'kerendahan diri'],
        ['peribahasa', 'Kerana nila setitik, rosak susu sebelanga', 'Kerana kesilapan kecil, semuanya jadi rosak.', 'Satu salah laku boleh menjejaskan reputasi sekolah — kerana nila setitik, rosak susu sebelanga.', 'akibat'],
        ['peribahasa', 'Ada gula, ada semut', 'Di mana ada keuntungan, di situlah orang berkumpul.', 'Promosi jualan murah itu ramai pengunjung — ada gula, ada semut.', 'sosial'],
    ];
}

function bm_kssm_run(): array
{
    $subject = db_one("SELECT id FROM subjects WHERE slug = 'bahasa-melayu' LIMIT 1");
    if (!$subject) {
        return ['status' => 'no_subject', 'message' => 'Bahasa Melayu subject not found. Run install / seed_subjects first.'];
    }
    $sid = (int) $subject['id'];

    $peribahasaTable = (bool) db_one("SELECT 1 AS x FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'bm_peribahasa'");

    $catalog = bm_kssm_catalog();
    $topicsCreated = $topicsKept = $topicsAdopted = 0;
    $subCreated = $subKept = 0;
    $skillsCreated = $skillsKept = 0;
    $peribahasaCreated = $peribahasaKept = 0;

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

    if ($peribahasaTable) {
        foreach (bm_kssm_peribahasa() as $idx => [$type, $expr, $meaning, $example, $theme]) {
            $existing = db_one('SELECT id FROM bm_peribahasa WHERE expression = ? LIMIT 1', [$expr]);
            if ($existing) {
                $peribahasaKept++;
            } else {
                db_exec(
                    'INSERT INTO bm_peribahasa (type, expression, meaning, example, theme, sort_order)
                     VALUES (?, ?, ?, ?, ?, ?)',
                    [$type, $expr, $meaning, $example, $theme, $idx + 1]
                );
                $peribahasaCreated++;
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
        'peribahasa_created' => $peribahasaCreated,
        'peribahasa_kept'    => $peribahasaKept,
        'catalog_topics'     => count($catalog),
        'peribahasa_table'   => $peribahasaTable,
    ];
}

if (PHP_SAPI === 'cli') {
    $r = bm_kssm_run();
    if (($r['status'] ?? '') !== 'ok') {
        fwrite(STDERR, ($r['message'] ?? 'unknown error') . "\n");
        exit(1);
    }
    echo "KSSM Bahasa Melayu seeded —\n";
    echo "  Topics:     created {$r['topics_created']}, adopted {$r['topics_adopted']}, kept {$r['topics_kept']} (catalog {$r['catalog_topics']})\n";
    echo "  Subtopics:  created {$r['subtopics_created']}, kept {$r['subtopics_kept']}\n";
    echo "  Skills:     created {$r['skills_created']}, kept {$r['skills_kept']}\n";
    if ($r['peribahasa_table']) {
        echo "  Peribahasa: created {$r['peribahasa_created']}, kept {$r['peribahasa_kept']}\n";
    } else {
        echo "  Peribahasa: table missing — run database migrations to enable.\n";
    }
}
