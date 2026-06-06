<?php
/**
 * KSSM SPM Reka Cipta (Mata Pelajaran Elektif Ikhtisas — RBT slug)
 * syllabus seeder. Tingkatan 4 (8 bab — Asas Reka Cipta) + Tingkatan 5
 * (8 bab dalam dua kluster — Teknologi Pembuatan + Strategi Pemasaran)
 * — 16 bab total, dengan starter bank konsep reka cipta.
 *
 * Note: full RBT is a Tingkatan 1-3 subject; the SPM-level equivalent
 * under the same `rbt` subject slug is Reka Cipta.
 *
 * Idempotent: bab matched by (subject_id, name, form_level); legacy
 * same-named NULL-form bab are adopted.
 *
 *   php public_html/cron/seed_rbt_kssm.php
 */
declare(strict_types=1);

require_once __DIR__ . '/../inc/db.php';

function rbt_kssm_catalog(): array
{
    return [
        // ============= TINGKATAN 4 — Asas Reka Cipta =============
        ['form' => 4, 'name' => 'Pengenalan kepada Reka Cipta', 'subtopics' => [
            'Definisi Inventif, Inovasi dan Kreativiti',
            'Sejarah Perkembangan Reka Cipta (Prarevolusi, Revolusi Industri, Revolusi Industri Kedua)',
            'Perkembangan Reka Cipta dalam Pelbagai Bidang (Pendidikan, Pembinaan, Komunikasi, Pengangkutan)',
            'Peranan Pereka Cipta',
            'Kerjaya dalam Bidang Reka Cipta',
        ], 'skills' => [
            ['Mentakrifkan inventif, inovasi dan kreativiti', 'easy'],
            ['Menghuraikan sejarah perkembangan reka cipta', 'medium'],
            ['Mengenal pasti peluang kerjaya dalam bidang reka cipta', 'easy'],
        ]],
        ['form' => 4, 'name' => 'Asas Reka Bentuk dalam Reka Cipta', 'subtopics' => [
            'Elemen Reka Bentuk (Garisan, Bentuk, Rupa, Warna, Tekstur, Ruang)',
            'Prinsip Reka Bentuk (Keseimbangan, Penekanan, Pergerakan, Rentak, Kesatuan, Kontras, Kepelbagaian, Kadar Banding)',
            'Aplikasi Elemen dan Prinsip dalam Produk',
        ], 'skills' => [
            ['Mengenal pasti elemen reka bentuk', 'medium'],
            ['Mengaplikasikan prinsip reka bentuk pada produk', 'medium'],
            ['Menganalisis reka bentuk produk sedia ada', 'medium'],
        ]],
        ['form' => 4, 'name' => 'Faktor Pemilihan Reka Bentuk dalam Reka Cipta', 'subtopics' => [
            'Fungsi dan Kepenggunaan',
            'Estetika',
            'Ergonomik',
            'Bahan dan Kos',
            'Kemampanan dan Mesra Alam',
            'Keselamatan',
        ], 'skills' => [
            ['Menilai faktor reka bentuk produk', 'medium'],
            ['Menerangkan kepentingan ergonomik', 'medium'],
            ['Menganalisis kemampanan reka bentuk', 'medium'],
        ]],
        ['form' => 4, 'name' => 'Pengenalpastian Masalah', 'subtopics' => [
            'Kaedah Pengenalpastian Masalah',
            'Pemerhatian dan Tinjauan',
            'Pernyataan Masalah (Problem Statement)',
            'Analisis SWOT',
            'Carta Tulang Ikan (Ishikawa)',
        ], 'skills' => [
            ['Mengenal pasti masalah dalam kehidupan harian', 'medium'],
            ['Menyediakan pernyataan masalah yang jelas', 'medium'],
            ['Menggunakan analisis SWOT', 'medium'],
        ]],
        ['form' => 4, 'name' => 'Penyelidikan dan Kajian Produk', 'subtopics' => [
            'Sumber Maklumat (Primer dan Sekunder)',
            'Kajian Pasaran',
            'Analisis Produk Sedia Ada',
            'Pengumpulan Data Pengguna',
            'Soal Selidik dan Temubual',
        ], 'skills' => [
            ['Menjalankan kajian pasaran', 'medium'],
            ['Menganalisis produk sedia ada', 'medium'],
            ['Membentuk soal selidik untuk kajian pengguna', 'medium'],
        ]],
        ['form' => 4, 'name' => 'Penjanaan Idea', 'subtopics' => [
            'Teknik Sumbang Saran (Brainstorming)',
            'Pemetaan Minda (Mind Mapping)',
            'Lakaran Idea',
            'Penilaian dan Pemilihan Idea',
            'Lakaran Reka Bentuk Terpilih',
        ], 'skills' => [
            ['Mengaplikasikan teknik sumbang saran', 'easy'],
            ['Melakar pelbagai idea reka bentuk', 'medium'],
            ['Memilih idea terbaik berdasarkan kriteria', 'medium'],
        ]],
        ['form' => 4, 'name' => 'Model Olokan (Mock-Up)', 'subtopics' => [
            'Konsep dan Tujuan Model Olokan',
            'Jenis Model Olokan',
            'Bahan untuk Model Olokan',
            'Teknik Pembinaan Model Olokan',
            'Ujian Awal dan Penambahbaikan',
        ], 'skills' => [
            ['Membezakan jenis model olokan', 'medium'],
            ['Memilih bahan yang sesuai untuk model olokan', 'medium'],
            ['Membina model olokan ringkas', 'hard'],
        ]],
        ['form' => 4, 'name' => 'Lukisan Kerja', 'subtopics' => [
            'Jenis Lukisan Kerja',
            'Lukisan Ortografik',
            'Lukisan Isometrik dan Oblik',
            'Ukuran dan Skala',
            'Tanda dan Simbol Lukisan',
        ], 'skills' => [
            ['Membezakan jenis lukisan kerja', 'medium'],
            ['Melukis pandangan ortografik mudah', 'hard'],
            ['Mengaplikasikan skala dan ukuran', 'medium'],
        ]],

        // ============= TINGKATAN 5 — Kluster 1: Teknologi Pembuatan =============
        ['form' => 5, 'name' => 'Lukisan Terbantu Komputer (LTK)', 'subtopics' => [
            'Pengenalan kepada CAD',
            'Perisian CAD (AutoCAD, SketchUp, Fusion 360)',
            'Menggambar 2D',
            'Pemodelan 3D',
            'Render dan Pengeluaran Pelan',
        ], 'skills' => [
            ['Menggunakan perisian CAD asas', 'medium'],
            ['Menyediakan lukisan 2D menggunakan CAD', 'hard'],
            ['Membangunkan model 3D mudah', 'hard'],
        ]],
        ['form' => 5, 'name' => 'Bahan, Peralatan dan Mesin Pembinaan Produk', 'subtopics' => [
            'Jenis Bahan (Logam, Kayu, Plastik, Komposit)',
            'Ciri Bahan',
            'Peralatan Tangan',
            'Mesin Pembinaan',
            'Keselamatan Bengkel',
        ], 'skills' => [
            ['Membezakan jenis bahan dan cirinya', 'medium'],
            ['Mengenal pasti peralatan dan mesin yang sesuai', 'medium'],
            ['Mengamalkan keselamatan bengkel', 'easy'],
        ]],
        ['form' => 5, 'name' => 'Sistem', 'subtopics' => [
            'Konsep Sistem (Input, Proses, Output)',
            'Sistem Mekanikal',
            'Sistem Elektrikal dan Elektronik',
            'Sistem Pneumatik dan Hidraulik',
            'Integrasi Sistem dalam Produk',
        ], 'skills' => [
            ['Menjelaskan konsep sistem (IPO)', 'medium'],
            ['Membezakan jenis sistem (mekanikal, elektrikal, pneumatik)', 'medium'],
            ['Mengintegrasikan sistem dalam produk', 'hard'],
        ]],
        ['form' => 5, 'name' => 'Pembinaan dan Pengujian Model Berfungsi dan Prototaip', 'subtopics' => [
            'Perbezaan Model Berfungsi dan Prototaip',
            'Proses Pembinaan Prototaip',
            'Kaedah Pengujian Produk',
            'Pengukuran Prestasi',
            'Penambahbaikan Berdasarkan Hasil Ujian',
        ], 'skills' => [
            ['Membezakan model berfungsi dan prototaip', 'medium'],
            ['Membina prototaip produk', 'hard'],
            ['Menjalankan ujian dan menganalisis hasil', 'hard'],
        ]],

        // ============= TINGKATAN 5 — Kluster 2: Strategi Pemasaran =============
        ['form' => 5, 'name' => 'Penjenamaan Produk', 'subtopics' => [
            'Konsep Penjenamaan',
            'Elemen Jenama (Nama, Logo, Slogan, Warna)',
            'Pembangunan Identiti Jenama',
            'Pembungkusan (Packaging)',
            'Kesetiaan Pelanggan terhadap Jenama',
        ], 'skills' => [
            ['Menerangkan konsep penjenamaan', 'medium'],
            ['Mereka bentuk identiti jenama (logo, slogan)', 'medium'],
            ['Mereka bentuk pembungkusan yang berkesan', 'medium'],
        ]],
        ['form' => 5, 'name' => 'Pemasaran Produk', 'subtopics' => [
            'Strategi Pemasaran 4P (Product, Price, Place, Promotion)',
            'Segmentasi Pasaran',
            'Sasaran Pengguna',
            'Saluran Pengedaran',
            'Promosi dan Iklan (termasuk Media Sosial)',
        ], 'skills' => [
            ['Mengaplikasikan strategi 4P', 'medium'],
            ['Mengenal pasti segmentasi pasaran sasaran', 'medium'],
            ['Membangunkan kempen promosi produk', 'medium'],
        ]],
        ['form' => 5, 'name' => 'Harta Intelek', 'subtopics' => [
            'Konsep Harta Intelek',
            'Paten (Patent)',
            'Cap Dagangan (Trademark)',
            'Hak Cipta (Copyright)',
            'Reka Bentuk Perindustrian',
            'Perbadanan Harta Intelek Malaysia (MyIPO)',
        ], 'skills' => [
            ['Membezakan jenis harta intelek', 'medium'],
            ['Menjelaskan proses pendaftaran paten', 'medium'],
            ['Menilai kepentingan harta intelek bagi pereka cipta', 'medium'],
        ]],
        ['form' => 5, 'name' => 'Pendokumentasian', 'subtopics' => [
            'Tujuan Dokumentasi Reka Cipta',
            'Format Laporan Reka Cipta',
            'Penulisan Pernyataan Masalah dan Latar Belakang',
            'Rekod Penjanaan Idea dan Reka Bentuk',
            'Lampiran (Lukisan, Foto, Ujian)',
            'Pembentangan Projek',
        ], 'skills' => [
            ['Menyediakan dokumentasi projek reka cipta', 'medium'],
            ['Menulis laporan teknikal yang jelas', 'medium'],
            ['Mempersembahkan projek dengan berkesan', 'medium'],
        ]],
    ];
}

/** Starter Reka Cipta concepts bank. */
function rbt_kssm_concepts(): array
{
    return [
        // topic, type, name, definition, example, category

        // --- Pengenalan ---
        ['Pengenalan kepada Reka Cipta', 'istilah', 'Inventif (Invention)', 'Penciptaan sesuatu yang baharu atau pertama kalinya wujud — sesuatu yang belum pernah dicipta sebelum ini.', 'Penemuan lampu mentol oleh Thomas Edison.', 'reka_cipta'],
        ['Pengenalan kepada Reka Cipta', 'istilah', 'Inovasi (Innovation)', 'Penambahbaikan atau penghasilan idea baharu daripada produk atau proses sedia ada untuk memberi nilai tambah.', 'Inovasi telefon bimbit kepada telefon pintar dengan ciri kamera dan internet.', 'reka_cipta'],
        ['Pengenalan kepada Reka Cipta', 'istilah', 'Kreativiti (Creativity)', 'Kebolehan menghasilkan idea, penyelesaian atau produk yang baharu, unik dan berguna.', 'Pereka mengubah botol plastik terbuang menjadi pasu bunga hiasan.', 'reka_cipta'],
        ['Pengenalan kepada Reka Cipta', 'istilah', 'Revolusi Industri', 'Tempoh perubahan besar dalam masyarakat akibat peralihan daripada pengeluaran berasaskan tangan kepada pengeluaran berasaskan mesin pada akhir abad ke-18.', 'Penciptaan enjin stim oleh James Watt mengubah industri kilang.', 'sejarah'],

        // --- Asas Reka Bentuk ---
        ['Asas Reka Bentuk dalam Reka Cipta', 'istilah', 'Garisan', 'Elemen reka bentuk paling asas — kesan jejak titik yang bergerak; ada garisan lurus, lengkung, putus-putus.', 'Garisan kontur pada peta topografi, garisan jahitan pada baju.', 'elemen_reka_bentuk'],
        ['Asas Reka Bentuk dalam Reka Cipta', 'istilah', 'Bentuk (Form)', 'Objek 3D yang mempunyai panjang, lebar dan tinggi.', 'Kiub, sfera, silinder.', 'elemen_reka_bentuk'],
        ['Asas Reka Bentuk dalam Reka Cipta', 'istilah', 'Rupa (Shape)', 'Bidang 2D yang dihasilkan oleh garisan tertutup; mempunyai panjang dan lebar sahaja.', 'Segi empat, bulatan, segi tiga.', 'elemen_reka_bentuk'],
        ['Asas Reka Bentuk dalam Reka Cipta', 'istilah', 'Tekstur', 'Sifat permukaan sesuatu objek sama ada kasar, licin, lembut atau keras — sentuhan atau visual.', 'Tekstur kayu jati, tekstur kain sutera.', 'elemen_reka_bentuk'],
        ['Asas Reka Bentuk dalam Reka Cipta', 'istilah', 'Keseimbangan', 'Prinsip reka bentuk yang memberikan kesan stabil — boleh simetri atau asimetri.', 'Reka bentuk muka manusia adalah simetri; reka bentuk taman boleh asimetri tetapi seimbang.', 'prinsip_reka_bentuk'],
        ['Asas Reka Bentuk dalam Reka Cipta', 'istilah', 'Penekanan (Emphasis)', 'Prinsip yang menarik perhatian kepada satu unsur utama dalam reka bentuk.', 'Tajuk besar pada poster, butang "Beli Sekarang" yang berwarna terang.', 'prinsip_reka_bentuk'],
        ['Asas Reka Bentuk dalam Reka Cipta', 'istilah', 'Kadar Banding (Proportion)', 'Hubungan saiz antara satu bahagian dengan bahagian lain atau dengan keseluruhan.', 'Nisbah keemasan 1:1.618 dalam reka bentuk.', 'prinsip_reka_bentuk'],
        ['Asas Reka Bentuk dalam Reka Cipta', 'istilah', 'Kontras', 'Perbezaan ketara antara dua unsur yang menarik perhatian.', 'Kontras hitam dan putih, besar dan kecil, tebal dan nipis.', 'prinsip_reka_bentuk'],

        // --- Faktor Pemilihan Reka Bentuk ---
        ['Faktor Pemilihan Reka Bentuk dalam Reka Cipta', 'istilah', 'Ergonomik', 'Kajian tentang reka bentuk produk yang sesuai dan selesa dengan tubuh manusia, mengurangkan tekanan dan keletihan.', 'Kerusi pejabat dengan sokongan lumbar yang boleh dilaraskan.', 'faktor'],
        ['Faktor Pemilihan Reka Bentuk dalam Reka Cipta', 'istilah', 'Estetika', 'Kualiti visual produk yang menarik perhatian, kreatif dan indah dipandang.', 'iPhone direka dengan estetika minimalis premium.', 'faktor'],
        ['Faktor Pemilihan Reka Bentuk dalam Reka Cipta', 'istilah', 'Kemampanan (Sustainability)', 'Reka bentuk yang mengambil kira impak alam sekitar dan generasi akan datang.', 'Beg yang diperbuat daripada plastik kitar semula, bateri boleh diisi semula.', 'faktor'],

        // --- Pengenalpastian Masalah ---
        ['Pengenalpastian Masalah', 'istilah', 'Analisis SWOT', 'Kaedah analisis yang mengkaji Strengths, Weaknesses, Opportunities dan Threats sesebuah produk atau idea.', 'SWOT untuk botol air baharu: kekuatan = mesra alam; kelemahan = kos tinggi; peluang = trend hijau; ancaman = pesaing besar.', 'analisis'],
        ['Pengenalpastian Masalah', 'istilah', 'Carta Tulang Ikan (Ishikawa)', 'Carta yang mengkaji sebab dan akibat sesuatu masalah dalam bentuk tulang ikan.', 'Analisis sebab pengeluaran lemah: bahan, kaedah, manusia, mesin.', 'analisis'],

        // --- Penyelidikan ---
        ['Penyelidikan dan Kajian Produk', 'istilah', 'Data Primer', 'Data yang dikumpul terus dari sumber asal melalui pemerhatian, soal selidik, temubual.', 'Soal selidik kepada 100 pelajar tentang penggunaan beg sekolah.', 'penyelidikan'],
        ['Penyelidikan dan Kajian Produk', 'istilah', 'Data Sekunder', 'Data yang diperoleh daripada sumber sedia ada seperti buku, jurnal, internet.', 'Maklumat statistik dari laporan SIRIM mengenai trend pengguna.', 'penyelidikan'],

        // --- Penjanaan Idea ---
        ['Penjanaan Idea', 'istilah', 'Sumbang Saran (Brainstorming)', 'Teknik mengumpul idea sebanyak mungkin secara bebas dalam masa singkat tanpa kritikan.', 'Sesi 15 minit untuk menjana 50 idea botol air baharu — semua idea diterima.', 'penjanaan_idea'],
        ['Penjanaan Idea', 'istilah', 'Pemetaan Minda (Mind Mapping)', 'Teknik visual untuk menyusun idea bercabang dari satu konsep pusat.', 'Konsep "beg sekolah pintar" → cabang: bahan, ciri, sasaran, harga, ergonomik.', 'penjanaan_idea'],

        // --- Model & Prototaip ---
        ['Model Olokan (Mock-Up)', 'istilah', 'Model Olokan', 'Model awal produk yang menunjukkan rupa luaran dan saiz sebenar tetapi belum berfungsi sepenuhnya.', 'Model kerusi yang diperbuat daripada kadbod untuk menguji saiz dan bentuk sebelum membuat versi sebenar.', 'model'],

        // --- Lukisan Kerja ---
        ['Lukisan Kerja', 'istilah', 'Lukisan Ortografik', 'Sistem lukisan kejuruteraan yang menunjukkan objek dari pelbagai pandangan (depan, atas, sisi) pada satah berasingan.', 'Pandangan depan, atas dan sisi sebuah kotak kayu, semua dengan skala dan ukuran.', 'lukisan'],
        ['Lukisan Kerja', 'istilah', 'Lukisan Isometrik', 'Lukisan 3D di mana ketiga-tiga paksi membentuk sudut 120° antara satu sama lain.', 'Lukisan kotak yang menunjukkan ketiga-tiga muka pada satu paparan.', 'lukisan'],
        ['Lukisan Kerja', 'istilah', 'Skala 1:50', 'Setiap 1 unit pada lukisan mewakili 50 unit pada objek sebenar.', 'Lukisan rumah dengan skala 1:50 — 1 cm pada lukisan = 50 cm pada bangunan sebenar.', 'lukisan'],

        // --- CAD ---
        ['Lukisan Terbantu Komputer (LTK)', 'istilah', 'CAD (Computer-Aided Design)', 'Penggunaan perisian komputer untuk mereka bentuk dan menghasilkan lukisan teknikal 2D dan model 3D.', 'AutoCAD untuk pelan bangunan; SketchUp untuk reka bentuk perabot; Fusion 360 untuk pencetakan 3D.', 'cad'],
        ['Lukisan Terbantu Komputer (LTK)', 'istilah', 'Pemodelan 3D', 'Proses menghasilkan representasi tiga dimensi sesuatu objek menggunakan perisian komputer.', 'Model 3D kerusi sebelum dihantar ke kilang untuk dikeluarkan.', 'cad'],

        // --- Bahan ---
        ['Bahan, Peralatan dan Mesin Pembinaan Produk', 'istilah', 'Bahan Komposit', 'Bahan yang terdiri daripada dua atau lebih bahan yang digabungkan untuk menghasilkan sifat baharu.', 'Gentian karbon (carbon fibre), konkrit bertetulang.', 'bahan'],
        ['Bahan, Peralatan dan Mesin Pembinaan Produk', 'istilah', 'Termoplastik', 'Plastik yang boleh dilembut dan dibentuk semula apabila dipanaskan.', 'PET (botol minuman), PVC (paip), polietilena (beg plastik).', 'bahan'],

        // --- Sistem ---
        ['Sistem', 'istilah', 'Sistem Pneumatik', 'Sistem yang menggunakan udara mampat untuk menghasilkan kerja mekanikal.', 'Pemudah cara pintu bas, alat pemampat udara (compressor).', 'sistem'],
        ['Sistem', 'istilah', 'Sistem Hidraulik', 'Sistem yang menggunakan bendalir (biasanya minyak) bertekanan untuk menggerakkan piston dan menjalankan kerja.', 'Jek hidraulik, brek kereta, eskavator.', 'sistem'],
        ['Sistem', 'istilah', 'Get Logik dalam Litar', 'Komponen elektronik yang membuat keputusan berdasarkan input binari (0 atau 1).', 'Get AND untuk litar penggera yang memerlukan dua input aktif.', 'sistem'],

        // --- Prototaip ---
        ['Pembinaan dan Pengujian Model Berfungsi dan Prototaip', 'istilah', 'Prototaip', 'Versi awal produk yang berfungsi sepenuhnya, digunakan untuk pengujian sebelum pengeluaran besar-besaran.', 'Prototaip kereta elektrik diuji di trek sebelum dijual di pasaran.', 'prototaip'],
        ['Pembinaan dan Pengujian Model Berfungsi dan Prototaip', 'istilah', 'Iterasi Reka Bentuk', 'Proses menambah baik reka bentuk berulang kali berdasarkan maklum balas ujian.', 'Versi 1.0 → uji → kenal pasti kelemahan → versi 1.1 → uji semula.', 'prototaip'],

        // --- Penjenamaan ---
        ['Penjenamaan Produk', 'istilah', 'Jenama (Brand)', 'Identiti yang membezakan produk daripada pesaing — gabungan nama, logo, slogan, warna dan persepsi.', 'Nike — logo swoosh + slogan "Just Do It" + warna ikonik.', 'penjenamaan'],
        ['Penjenamaan Produk', 'istilah', 'Logo', 'Simbol grafik yang mewakili sesuatu jenama atau organisasi.', 'Logo Apple (epal digigit), logo Petronas (titisan).', 'penjenamaan'],
        ['Penjenamaan Produk', 'istilah', 'Pembungkusan (Packaging)', 'Reka bentuk bekas dan pembalut produk yang melindungi, mempromosi dan mempersembahkan produk kepada pengguna.', 'Kotak biru Tiffany & Co — pembungkusan menjadi ikon jenama.', 'penjenamaan'],

        // --- Pemasaran ---
        ['Pemasaran Produk', 'istilah', 'Strategi 4P', 'Strategi pemasaran berdasarkan empat unsur: Product, Price, Place, Promotion.', 'Produk: botol air mampan; Harga: RM35; Tempat: kedai eko + online; Promosi: media sosial.', 'pemasaran'],
        ['Pemasaran Produk', 'istilah', 'Sasaran Pengguna', 'Kumpulan pengguna spesifik yang menjadi tumpuan utama produk berdasarkan demografi, lokasi atau gaya hidup.', 'Sasaran: pelajar universiti, 18-25 tahun, peduli alam sekitar.', 'pemasaran'],

        // --- Harta Intelek ---
        ['Harta Intelek', 'istilah', 'Paten (Patent)', 'Hak eksklusif untuk menggunakan dan mengkomersialkan sesuatu reka cipta selama 20 tahun selepas pendaftaran.', 'Paten reka cipta mesin yang menukar plastik kepada minyak.', 'harta_intelek'],
        ['Harta Intelek', 'istilah', 'Cap Dagangan (Trademark)', 'Tanda atau simbol yang membezakan produk satu pengeluar daripada yang lain — boleh diperbaharui selama-lamanya.', 'Nama "Petronas" dan logo titisan adalah cap dagangan berdaftar.', 'harta_intelek'],
        ['Harta Intelek', 'istilah', 'Hak Cipta (Copyright)', 'Hak eksklusif pencipta karya asli (buku, muzik, perisian, lukisan) untuk menggunakan dan mengagihkan karyanya.', 'Buku novel automatik dilindungi hak cipta sebaik sahaja ditulis — tiada perlu daftar.', 'harta_intelek'],
        ['Harta Intelek', 'istilah', 'MyIPO', 'Perbadanan Harta Intelek Malaysia — agensi kerajaan yang menguruskan pendaftaran paten, cap dagangan, hak cipta dan reka bentuk perindustrian di Malaysia.', 'Pereka cipta mendaftarkan paten produk baharu melalui portal MyIPO.', 'harta_intelek'],

        // --- Dokumentasi ---
        ['Pendokumentasian', 'istilah', 'Pernyataan Masalah', 'Penyataan ringkas dan jelas yang mengenal pasti masalah yang ingin diselesaikan oleh reka cipta.', '"Beg sekolah berat menyebabkan sakit belakang pada pelajar — perlu reka bentuk ringan dengan agihan beban sekata."', 'dokumentasi'],
        ['Pendokumentasian', 'istilah', 'Pembentangan Projek', 'Sesi persembahan projek reka cipta secara lisan dengan bantuan visual kepada panel atau penonton.', 'Pembentangan 10 minit + soal jawab tentang projek SPM Reka Cipta.', 'dokumentasi'],
    ];
}

function rbt_kssm_run(): array
{
    $subject = db_one("SELECT id FROM subjects WHERE slug = 'rbt' LIMIT 1");
    if (!$subject) {
        return ['status' => 'no_subject', 'message' => 'RBT / Reka Cipta subject not found. Run install / seed_subjects first.'];
    }
    $sid = (int) $subject['id'];

    $conceptsTable = (bool) db_one("SELECT 1 AS x FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'rbt_concepts'");

    $catalog = rbt_kssm_catalog();
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
        foreach (rbt_kssm_concepts() as $idx => [$topicName, $type, $name, $def, $example, $category]) {
            $tid = $topicIds[$topicName] ?? null;
            $existing = db_one('SELECT id FROM rbt_concepts WHERE name = ? LIMIT 1', [$name]);
            if ($existing) {
                $conceptsKept++;
            } else {
                db_exec(
                    'INSERT INTO rbt_concepts (topic_id, topic_label, type, name, definition, example, category, sort_order)
                     VALUES (?,?,?,?,?,?,?,?)',
                    [$tid, $topicName, $type, $name, $def, $example, $category, $idx + 1]
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
    $r = rbt_kssm_run();
    if (($r['status'] ?? '') !== 'ok') {
        fwrite(STDERR, ($r['message'] ?? 'unknown error') . "\n");
        exit(1);
    }
    echo "KSSM Reka Cipta (RBT SPM) seeded —\n";
    echo "  Bab:        created {$r['topics_created']}, adopted {$r['topics_adopted']}, kept {$r['topics_kept']} (catalog {$r['catalog_topics']})\n";
    echo "  Subtopik:   created {$r['subtopics_created']}, kept {$r['subtopics_kept']}\n";
    echo "  Kemahiran:  created {$r['skills_created']}, kept {$r['skills_kept']}\n";
    if ($r['concepts_table']) {
        echo "  Konsep:     created {$r['concepts_created']}, kept {$r['concepts_kept']}\n";
    } else {
        echo "  Konsep:     table missing — run database migrations to enable.\n";
    }
}
