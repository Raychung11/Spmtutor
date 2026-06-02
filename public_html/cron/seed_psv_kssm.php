<?php
/**
 * KSSM SPM Pendidikan Seni Visual (PSV) syllabus seeder.
 * Tingkatan 4 (9 tajuk dalam 5 bidang — Sejarah/Apresiasi, Seni Halus,
 * Reka Bentuk, Seni Kraf, Komunikasi Visual) + Tingkatan 5 (8 tajuk)
 * — 17 tajuk total, dengan starter bank rujukan seni (tokoh seni,
 * teknik, istilah, bahan).
 *
 * Idempotent: tajuk matched by (subject_id, name, form_level); legacy
 * same-named NULL-form tajuk are adopted.
 *
 *   php public_html/cron/seed_psv_kssm.php
 */
declare(strict_types=1);

require_once __DIR__ . '/../inc/db.php';

function psv_kssm_catalog(): array
{
    return [
        // ============= TINGKATAN 4 =============
        ['form' => 4, 'name' => 'Alat Kebesaran dan Perhiasan Diraja', 'subtopics' => [
            'Sejarah Alat Kebesaran Diraja Malaysia',
            'Jenis Alat Kebesaran (Cogan, Keris, Tengkolok, Mahkota)',
            'Motif dan Simbolisme',
            'Apresiasi Karya Seni Diraja',
        ], 'skills' => [
            ['Mengenal pasti jenis alat kebesaran diraja', 'easy'],
            ['Menerangkan simbolisme motif diraja', 'medium'],
            ['Menganalisis nilai estetik dan budaya', 'medium'],
        ]],
        ['form' => 4, 'name' => 'Seni Lukisan (Tingkatan 4)', 'subtopics' => [
            'Konsep dan Sejarah Lukisan',
            'Media dan Bahan Lukisan (Pensel, Arang, Pen, Berus)',
            'Teknik Lukisan (Garisan, Lorekan, Cross-hatching, Stippling)',
            'Komposisi dan Perspektif',
            'Apresiasi Karya Lukisan Tokoh',
        ], 'skills' => [
            ['Mengaplikasikan teknik lorekan dan cross-hatching', 'medium'],
            ['Melukis komposisi dengan perspektif', 'hard'],
            ['Mengapresiasi karya lukisan tokoh tempatan dan antarabangsa', 'medium'],
        ]],
        ['form' => 4, 'name' => 'Seni Catan', 'subtopics' => [
            'Konsep dan Sejarah Catan',
            'Media Catan (Cat Air, Akrilik, Minyak, Pastel)',
            'Teknik Catan (Wash, Impasto, Glazing, Wet-on-Wet)',
            'Teori Warna (Warna Primer, Sekunder, Komplementari)',
            'Apresiasi Tokoh Catan Malaysia',
        ], 'skills' => [
            ['Mengaplikasikan teknik catan air dan akrilik', 'hard'],
            ['Menggunakan teori warna dalam catan', 'medium'],
            ['Menganalisis karya tokoh catan Malaysia', 'medium'],
        ]],
        ['form' => 4, 'name' => 'Seni Cetakan', 'subtopics' => [
            'Konsep dan Sejarah Cetakan',
            'Jenis Cetakan (Cetakan Timbul, Cetakan Cekung, Cetakan Saring, Cetakan Foto)',
            'Bahan dan Alatan Cetakan',
            'Proses Penghasilan Cetakan',
            'Apresiasi Karya Cetakan',
        ], 'skills' => [
            ['Membezakan jenis cetakan (timbul, cekung, saring)', 'medium'],
            ['Menjalankan proses cetakan timbul mudah', 'hard'],
            ['Mengapresiasi karya cetakan tradisional dan moden', 'medium'],
        ]],
        ['form' => 4, 'name' => 'Reka Bentuk Landskap', 'subtopics' => [
            'Konsep Reka Bentuk Landskap',
            'Elemen Landskap (Tumbuhan, Air, Batu, Bangunan, Laluan)',
            'Prinsip Reka Bentuk Landskap',
            'Pelan Landskap (Pelan Atas, Lakaran Perspektif)',
            'Aplikasi: Reka Bentuk Taman / Halaman',
        ], 'skills' => [
            ['Menerangkan elemen reka bentuk landskap', 'medium'],
            ['Melukis pelan landskap mudah', 'hard'],
            ['Mereka bentuk taman kecil yang berfungsi', 'hard'],
        ]],
        ['form' => 4, 'name' => 'Reka Bentuk Hiasan Dalaman', 'subtopics' => [
            'Konsep Reka Bentuk Hiasan Dalaman',
            'Elemen Hiasan Dalaman (Ruang, Warna, Cahaya, Perabot, Tekstil)',
            'Prinsip Reka Bentuk Hiasan Dalaman',
            'Tema dan Gaya (Moden, Tradisional, Minimalis)',
            'Lakaran Pelan dan Perspektif',
        ], 'skills' => [
            ['Mengenal pasti elemen hiasan dalaman', 'medium'],
            ['Mereka bentuk pelan hiasan dalaman bilik', 'hard'],
            ['Memilih warna dan tema yang sesuai', 'medium'],
        ]],
        ['form' => 4, 'name' => 'Seni Ukiran', 'subtopics' => [
            'Sejarah dan Tradisi Seni Ukiran Melayu',
            'Jenis Ukiran (Ukiran Tebuk, Timbul, Tenggelam, Bulat)',
            'Motif Ukiran (Awan Larat, Bunga Tanjung, Pucuk Rebung)',
            'Bahan dan Alatan (Kayu, Pisau Wali, Pahat)',
            'Tokoh Ukiran Tradisional Malaysia',
        ], 'skills' => [
            ['Membezakan jenis ukiran (tebuk, timbul, tenggelam, bulat)', 'medium'],
            ['Mengenal pasti motif ukiran Melayu', 'easy'],
            ['Menghuraikan sumbangan tokoh ukiran', 'medium'],
        ]],
        ['form' => 4, 'name' => 'Seni Reka Grafik (Ilustrasi)', 'subtopics' => [
            'Konsep Ilustrasi',
            'Jenis Ilustrasi (Naratif, Informatif, Saintifik, Kartun)',
            'Teknik Penghasilan Ilustrasi (Manual dan Digital)',
            'Tipografi dan Layout',
            'Aplikasi Ilustrasi dalam Iklan dan Buku',
        ], 'skills' => [
            ['Mengenal pasti jenis ilustrasi', 'easy'],
            ['Menghasilkan ilustrasi naratif mudah', 'hard'],
            ['Mengaplikasikan tipografi dalam reka grafik', 'medium'],
        ]],
        ['form' => 4, 'name' => 'Seni Foto (Genre Fotografi)', 'subtopics' => [
            'Sejarah Fotografi',
            'Genre Fotografi (Potret, Landskap, Senibina, Jurnalistik, Makanan)',
            'Komposisi Foto (Rule of Thirds, Leading Lines, Framing)',
            'Pencahayaan dan Suntingan',
            'Apresiasi Karya Tokoh Fotografi',
        ], 'skills' => [
            ['Mengenal pasti genre fotografi', 'easy'],
            ['Mengaplikasikan komposisi foto (Rule of Thirds)', 'medium'],
            ['Mengambil foto bertema dengan komposisi baik', 'hard'],
        ]],

        // ============= TINGKATAN 5 =============
        ['form' => 5, 'name' => 'Seni Bina', 'subtopics' => [
            'Sejarah Seni Bina Tradisional Malaysia',
            'Seni Bina Klasik Dunia (Yunani, Rom, Islam)',
            'Elemen Seni Bina (Tiang, Lengkungan, Atap, Hiasan)',
            'Bahan Binaan Tradisional dan Moden',
            'Apresiasi Bangunan Bersejarah Malaysia',
        ], 'skills' => [
            ['Mengenal pasti elemen seni bina', 'medium'],
            ['Membandingkan seni bina tradisional dan moden', 'medium'],
            ['Mengapresiasi bangunan bersejarah Malaysia', 'medium'],
        ]],
        ['form' => 5, 'name' => 'Seni Lukisan (Tingkatan 5)', 'subtopics' => [
            'Aliran Lukisan Dunia (Realisme, Impresionisme, Kubisme, Abstrak)',
            'Tokoh Lukisan Dunia',
            'Tokoh Lukisan Malaysia',
            'Teknik Lukisan Lanjutan',
            'Analisis Karya Lukisan',
        ], 'skills' => [
            ['Mengenal pasti aliran lukisan dunia', 'medium'],
            ['Mengapresiasi karya tokoh lukisan', 'medium'],
            ['Menganalisis komposisi dan teknik karya', 'hard'],
        ]],
        ['form' => 5, 'name' => 'Seni Catan (Tingkatan 5)', 'subtopics' => [
            'Aliran Catan (Realisme, Impresionisme, Ekspresionisme, Abstrak)',
            'Tokoh Catan Antarabangsa (Van Gogh, Picasso, Monet)',
            'Tokoh Catan Malaysia (Syed Ahmad Jamal, Latiff Mohidin)',
            'Teknik Catan Lanjutan',
            'Penghasilan Karya Catan Bertema',
        ], 'skills' => [
            ['Mengenal pasti aliran catan utama dunia', 'medium'],
            ['Mengapresiasi karya tokoh catan tempatan dan dunia', 'medium'],
            ['Menghasilkan karya catan bertema', 'hard'],
        ]],
        ['form' => 5, 'name' => 'Seni Arca', 'subtopics' => [
            'Konsep dan Sejarah Seni Arca',
            'Jenis Arca (Bulat, Timbul, Mobile, Kinetik, Pemasangan)',
            'Bahan Arca (Tanah Liat, Logam, Kayu, Batu, Mendapan)',
            'Teknik Arca (Modeling, Carving, Casting, Assemblage)',
            'Tokoh Arca Malaysia dan Dunia',
        ], 'skills' => [
            ['Membezakan jenis arca', 'medium'],
            ['Mengenal pasti teknik arca', 'medium'],
            ['Menghasilkan arca tanah liat ringkas', 'hard'],
        ]],
        ['form' => 5, 'name' => 'Reka Bentuk Industri', 'subtopics' => [
            'Konsep Reka Bentuk Industri',
            'Faktor Reka Bentuk (Fungsi, Estetika, Ergonomik, Bahan)',
            'Proses Reka Bentuk Industri',
            'Reka Bentuk Produk Pengguna',
            'Tokoh Reka Bentuk Industri Dunia',
        ], 'skills' => [
            ['Menerangkan proses reka bentuk industri', 'medium'],
            ['Mereka bentuk produk berdasarkan keperluan pengguna', 'hard'],
            ['Menilai produk industri yang sedia ada', 'medium'],
        ]],
        ['form' => 5, 'name' => 'Seni Batik', 'subtopics' => [
            'Sejarah Batik di Malaysia',
            'Jenis Batik (Batik Canting, Batik Tjap, Batik Lukis)',
            'Motif Batik Malaysia',
            'Bahan dan Alatan (Lilin, Pewarna, Kain, Canting)',
            'Proses Penghasilan Batik',
        ], 'skills' => [
            ['Membezakan jenis batik (canting, tjap, lukis)', 'medium'],
            ['Mengenal pasti motif batik Malaysia', 'easy'],
            ['Menghasilkan reka bentuk batik mudah', 'hard'],
        ]],
        ['form' => 5, 'name' => 'Seni Reka Grafik / Infografik', 'subtopics' => [
            'Konsep Reka Grafik dan Infografik',
            'Elemen Infografik (Ikon, Carta, Graf, Tipografi)',
            'Prinsip Reka Bentuk Infografik',
            'Perisian Reka Grafik (Photoshop, Illustrator, Canva)',
            'Aplikasi Infografik dalam Komunikasi',
        ], 'skills' => [
            ['Mengenal pasti elemen infografik', 'medium'],
            ['Mereka bentuk infografik bertema', 'hard'],
            ['Menggunakan perisian reka grafik asas', 'medium'],
        ]],
        ['form' => 5, 'name' => 'Seni Foto (Manipulasi Imej dan e-Portfolio)', 'subtopics' => [
            'Manipulasi Imej Digital',
            'Perisian Suntingan (Photoshop, Lightroom, Snapseed)',
            'Teknik Suntingan (Cropping, Layering, Filter, Komposit)',
            'Etika Manipulasi Imej',
            'Penyediaan e-Portfolio',
        ], 'skills' => [
            ['Mengaplikasikan teknik suntingan asas', 'medium'],
            ['Menghasilkan komposit foto kreatif', 'hard'],
            ['Menyediakan e-portfolio karya seni', 'medium'],
        ]],
    ];
}

/** Starter PSV references bank — tokoh, karya, teknik, istilah. */
function psv_kssm_references(): array
{
    return [
        // topic, type, name, description, origin, era, example, category

        // ===== Asas Seni Reka / Elemen / Prinsip =====
        ['Alat Kebesaran dan Perhiasan Diraja', 'istilah', 'Cogan', 'Lambang kebesaran diraja yang dibawa dalam istiadat — biasanya berbentuk tongkat panjang dengan hiasan emas atau perak di puncak.', 'Malaysia', 'Kesultanan Melayu', 'Cogan Negara semasa Pertabalan Yang di-Pertuan Agong.', 'alat_diraja'],
        ['Alat Kebesaran dan Perhiasan Diraja', 'istilah', 'Tengkolok', 'Hiasan kepala diraja diperbuat daripada kain songket yang dibentuk dengan teknik tertentu (contoh: Dendam Tak Sudah).', 'Malaysia', 'Tradisi Melayu', 'Tengkolok Sultan Selangor pada hari istiadat.', 'alat_diraja'],

        ['Seni Lukisan (Tingkatan 4)', 'teknik', 'Cross-hatching', 'Teknik lorekan menggunakan dua atau lebih set garisan bersilang untuk menghasilkan ton gelap dan tekstur.', null, null, 'Lukisan pensel dengan kawasan bayang dihasilkan oleh garisan bersilang.', 'teknik_lukisan'],
        ['Seni Lukisan (Tingkatan 4)', 'teknik', 'Stippling', 'Teknik menghasilkan ton dan tekstur menggunakan titik-titik kecil yang banyak.', null, null, 'Lukisan pen dengan kawasan gelap dihasilkan oleh ketumpatan titik.', 'teknik_lukisan'],
        ['Seni Lukisan (Tingkatan 4)', 'istilah', 'Perspektif Satu Titik Lenyap', 'Sistem perspektif di mana semua garisan menuju ke satu titik di garis ufuk — memberi ilusi kedalaman.', null, null, 'Lukisan jalan raya lurus menuju ke ufuk yang jauh.', 'komposisi'],

        // ===== Catan =====
        ['Seni Catan', 'teknik', 'Impasto', 'Teknik catan dengan menyapu lapisan cat yang tebal sehingga membentuk tekstur timbul pada permukaan kanvas.', 'Eropah', 'Renaissance', 'Karya Vincent van Gogh banyak menggunakan teknik impasto.', 'teknik_catan'],
        ['Seni Catan', 'teknik', 'Glazing', 'Teknik melapis cat transparen di atas lapisan kering untuk menghasilkan kesan kedalaman dan kilauan warna.', 'Eropah', 'Renaissance', 'Karya Rembrandt menunjukkan teknik glazing yang halus.', 'teknik_catan'],
        ['Seni Catan', 'teknik', 'Wet-on-Wet', 'Teknik catan air di mana cat dilukis di atas kertas yang telah dibasahkan untuk kesan lembut dan menyatu.', null, null, 'Karya cat air landskap dengan kesan kabus.', 'teknik_catan'],
        ['Seni Catan', 'istilah', 'Warna Primer', 'Warna asas yang tidak boleh dihasilkan dengan mencampur warna lain: merah, kuning, biru.', null, null, 'Cat warna primer dalam set cat air.', 'teori_warna'],
        ['Seni Catan', 'istilah', 'Warna Komplementari', 'Pasangan warna yang berlawanan pada roda warna — apabila dicampurkan menghasilkan warna kelabu.', null, null, 'Merah & hijau, biru & jingga, ungu & kuning.', 'teori_warna'],
        ['Seni Catan (Tingkatan 5)', 'tokoh', 'Vincent van Gogh', 'Pelukis Belanda terkenal dengan teknik impasto dan warna kuat. Karya seperti "Starry Night" dan "Sunflowers" terkenal di seluruh dunia.', 'Belanda', '1853-1890, Post-Impresionisme', 'The Starry Night (1889), Sunflowers (1888).', 'tokoh_dunia'],
        ['Seni Catan (Tingkatan 5)', 'tokoh', 'Pablo Picasso', 'Pelukis Sepanyol — pelopor Kubisme dan banyak aliran moden. Banyak menghasilkan karya melalui pelbagai fasa.', 'Sepanyol', '1881-1973, Kubisme', 'Les Demoiselles d\'Avignon (1907), Guernica (1937).', 'tokoh_dunia'],
        ['Seni Catan (Tingkatan 5)', 'tokoh', 'Claude Monet', 'Pelukis Perancis — pelopor Impresionisme. Terkenal dengan siri karya bunga teratai dan landskap.', 'Perancis', '1840-1926, Impresionisme', 'Water Lilies series, Impression Sunrise (1872).', 'tokoh_dunia'],
        ['Seni Catan (Tingkatan 5)', 'tokoh', 'Syed Ahmad Jamal', 'Pelukis dan akademik Malaysia — diiktiraf sebagai bapa seni moden Malaysia. Menggunakan teknik abstrak ekspresionisme dengan unsur Melayu.', 'Malaysia', '1929-2011', 'Tulisan (1961), Sirih Pinang (1969).', 'tokoh_malaysia'],
        ['Seni Catan (Tingkatan 5)', 'tokoh', 'Latiff Mohidin', 'Pelukis dan penyair Malaysia — terkenal dengan siri "Pago Pago" dan karya bertema alam dan rohani.', 'Malaysia', '1941-kini', 'Siri Pago Pago (1960an), Mindscape series.', 'tokoh_malaysia'],
        ['Seni Catan (Tingkatan 5)', 'tokoh', 'Ibrahim Hussein', 'Pelukis Malaysia yang terkenal di pentas antarabangsa — gaya unik dengan teknik kolaj dan figura.', 'Malaysia', '1936-2009', 'My Father and the Astronaut (1970), karya kolaj besar.', 'tokoh_malaysia'],

        // ===== Cetakan =====
        ['Seni Cetakan', 'istilah', 'Cetakan Timbul (Relief Print)', 'Cetakan di mana bahagian timbul pada plat menerima dakwat dan dicetak ke kertas. Kawasan rendah tidak dicetak.', null, null, 'Cetakan blok kayu (woodcut), cetakan lino (linocut).', 'jenis_cetakan'],
        ['Seni Cetakan', 'istilah', 'Cetakan Cekung (Intaglio)', 'Cetakan di mana garisan dikorek ke dalam plat logam, diisi dakwat dan dicetak ke kertas lembap dengan tekanan tinggi.', null, null, 'Etsa (etching), engraving, drypoint.', 'jenis_cetakan'],
        ['Seni Cetakan', 'istilah', 'Cetakan Saring (Screen Printing)', 'Cetakan menggunakan skrin sutera dengan stensil — dakwat ditekan melalui kawasan terbuka.', null, null, 'Cetakan T-shirt, poster, karya Andy Warhol.', 'jenis_cetakan'],

        // ===== Reka Bentuk =====
        ['Reka Bentuk Landskap', 'istilah', 'Elemen Lembut (Softscape)', 'Elemen landskap yang hidup dan boleh tumbuh: tumbuhan, pokok, rumput, bunga.', null, null, 'Pokok bunga raya, rumput jenis Bermuda, semak hias.', 'landskap'],
        ['Reka Bentuk Landskap', 'istilah', 'Elemen Keras (Hardscape)', 'Elemen landskap bukan hidup: laluan, dinding, kolam, lampu, perabot taman.', null, null, 'Jalan batu, kolam air pancut, jambatan kecil.', 'landskap'],
        ['Reka Bentuk Hiasan Dalaman', 'istilah', 'Gaya Minimalis', 'Pendekatan reka bentuk yang mengurangkan elemen kepada perlu sahaja — palette warna terbatas, ruang lapang, garisan bersih.', 'Jepun / Skandinavia', 'Abad ke-20', 'Bilik dengan dinding putih, perabot kayu mudah, sedikit hiasan.', 'gaya'],
        ['Reka Bentuk Industri', 'tokoh', 'Dieter Rams', 'Pereka bentuk industri Jerman — terkenal dengan 10 Prinsip Reka Bentuk Baik dan kerja-kerja untuk Braun.', 'Jerman', '1932-kini', 'Pemain rekod Braun SK4, kalkulator ET 66.', 'tokoh_reka_bentuk'],
        ['Reka Bentuk Industri', 'tokoh', 'Jony Ive', 'Pereka bentuk industri British — dahulunya Ketua Reka Bentuk Apple, bertanggungjawab atas reka bentuk iPhone, iPad, iMac.', 'United Kingdom', '1967-kini', 'iPhone (2007), iMac G3 (1998), AirPods.', 'tokoh_reka_bentuk'],

        // ===== Seni Kraf =====
        ['Seni Ukiran', 'istilah', 'Ukiran Tebuk', 'Teknik ukiran di mana sebahagian kayu ditebuk hingga tembus — dilihat dari kedua-dua sisi.', 'Malaysia', 'Tradisi Melayu', 'Ukiran dinding masjid tradisional, bingkai jendela.', 'jenis_ukiran'],
        ['Seni Ukiran', 'istilah', 'Ukiran Timbul', 'Ukiran di mana motif menonjol keluar dari permukaan latar belakang.', 'Malaysia', 'Tradisi Melayu', 'Hiasan pintu istana, jambar ukiran.', 'jenis_ukiran'],
        ['Seni Ukiran', 'istilah', 'Motif Awan Larat', 'Motif ukiran Melayu tradisional yang menggambarkan tumbuhan menjalar dengan daun dan bunga yang bersambung tanpa putus.', 'Malaysia', 'Tradisi Melayu', 'Hiasan dinding rumah Melayu tradisional, mimbar masjid.', 'motif_melayu'],
        ['Seni Ukiran', 'istilah', 'Motif Pucuk Rebung', 'Motif segi tiga yang menggambarkan pucuk muda buluh — simbol pertumbuhan dan kesuburan.', 'Malaysia', 'Tradisi Melayu', 'Sempadan kain songket, hiasan tepi ukiran.', 'motif_melayu'],
        ['Seni Batik', 'istilah', 'Batik Canting', 'Batik yang dihasilkan dengan menggunakan canting (alat berhujung tirus) untuk menulis lilin cair pada kain.', 'Malaysia / Indonesia', 'Tradisi Nusantara', 'Batik Kelantan dan Terengganu yang halus, batik Jawa.', 'jenis_batik'],
        ['Seni Batik', 'istilah', 'Batik Tjap (Blok)', 'Batik yang dihasilkan dengan mengecap lilin menggunakan blok logam yang telah diukir motif.', 'Malaysia / Indonesia', 'Tradisi Nusantara', 'Sarung batik tjap dengan motif bunga berulang.', 'jenis_batik'],
        ['Seni Batik', 'istilah', 'Batik Lukis', 'Batik yang dilukis terus dengan berus ke atas kain, biasanya bermotif besar dan bebas.', 'Malaysia', 'Moden', 'Karya batik kontemporari Malaysia.', 'jenis_batik'],

        // ===== Seni Arca =====
        ['Seni Arca', 'istilah', 'Arca Mobile', 'Arca yang bergerak — biasanya bergantung pada keseimbangan dan udara untuk bergerak perlahan.', null, 'Abad ke-20', 'Karya Alexander Calder.', 'jenis_arca'],
        ['Seni Arca', 'istilah', 'Arca Kinetik', 'Arca yang menggunakan unsur gerakan sebagai sebahagian penting karya — boleh digerakkan oleh motor, angin, air.', null, 'Abad ke-20', 'Karya Anthony Howe.', 'jenis_arca'],
        ['Seni Arca', 'teknik', 'Casting (Tuangan)', 'Teknik menghasilkan arca dengan menuangkan bahan cair (gangsa, gipsum) ke dalam acuan dan membiarkannya keras.', null, null, 'Arca gangsa Tugu Negara, patung tradisional Hindu.', 'teknik_arca'],
        ['Seni Arca', 'teknik', 'Assemblage', 'Teknik menghasilkan arca dengan menyusun pelbagai objek atau bahan terbuang menjadi satu komposisi.', null, 'Abad ke-20', 'Arca Picasso "Bull\'s Head" daripada tempat duduk basikal.', 'teknik_arca'],

        // ===== Seni Bina =====
        ['Seni Bina', 'istilah', 'Tiang Doric', 'Salah satu daripada tiga gaya tiang klasik Yunani — paling ringkas, dengan kepala tiang yang sederhana.', 'Yunani', 'Klasik', 'Parthenon di Athens.', 'seni_bina_klasik'],
        ['Seni Bina', 'istilah', 'Lengkungan Tetingkap (Arch)', 'Struktur lengkung dalam seni bina yang membahagikan beban kepada dua sisi.', null, null, 'Lengkungan masjid, sungai jambatan tradisional.', 'elemen_seni_bina'],
        ['Seni Bina', 'tokoh', 'Masjid Putra', 'Masjid persekutuan Malaysia di Putrajaya — terkenal dengan kubah dan menara berwarna pink (granit merah jambu).', 'Malaysia', '1999', 'Pengaruh seni bina Parsi dan Islam moden.', 'bangunan_malaysia'],
        ['Seni Bina', 'tokoh', 'Bangunan Sultan Abdul Samad', 'Bangunan ikonik di Dataran Merdeka, Kuala Lumpur — siap tahun 1897 dengan gaya Moorish/Indo-Saracenic.', 'Malaysia', '1897', 'Direka oleh A.C. Norman.', 'bangunan_malaysia'],

        // ===== Seni Reka Grafik =====
        ['Seni Reka Grafik (Ilustrasi)', 'istilah', 'Tipografi', 'Seni dan teknik mengatur huruf untuk menjadikan teks yang dibaca jelas dan menarik.', null, null, 'Pilih font sans-serif untuk tajuk besar; serif untuk teks badan.', 'reka_grafik'],
        ['Seni Reka Grafik (Ilustrasi)', 'istilah', 'Layout', 'Susunan elemen visual (teks, gambar, ruang) pada halaman untuk komunikasi berkesan.', null, null, 'Layout majalah dengan tajuk besar, foto utama, dan ruang kosong yang seimbang.', 'reka_grafik'],
        ['Seni Reka Grafik / Infografik', 'istilah', 'Infografik', 'Persembahan visual maklumat dan data — menggabungkan teks, carta, ikon dan grafik untuk menyampaikan idea kompleks dengan jelas.', null, null, 'Infografik tentang kadar vaksinasi Malaysia dengan ikon, peratus dan peta.', 'infografik'],

        // ===== Seni Foto =====
        ['Seni Foto (Genre Fotografi)', 'istilah', 'Rule of Thirds', 'Prinsip komposisi yang membahagikan bingkai kepada 9 bahagian dengan dua garis menegak dan dua garis mendatar — subjek diletak di titik persilangan.', null, null, 'Foto landskap dengan ufuk pada garis bawah Rule of Thirds.', 'komposisi_foto'],
        ['Seni Foto (Genre Fotografi)', 'istilah', 'Leading Lines', 'Teknik komposisi yang menggunakan garisan dalam gambar untuk membimbing mata pemerhati ke subjek utama.', null, null, 'Foto jalan raya yang menuju ke gunung di latar.', 'komposisi_foto'],
        ['Seni Foto (Genre Fotografi)', 'istilah', 'Framing', 'Teknik komposisi di mana elemen sekitar (tingkap, dahan pokok, pintu) "membingkai" subjek utama.', null, null, 'Subjek dilihat melalui lengkungan dinding kuno.', 'komposisi_foto'],
        ['Seni Foto (Manipulasi Imej dan e-Portfolio)', 'istilah', 'Layering', 'Teknik menyusun beberapa imej dalam lapisan (layer) berasingan dalam perisian suntingan untuk gabungan kreatif.', null, null, 'Photoshop dengan layer untuk background, subjek, teks, kesan.', 'suntingan'],
        ['Seni Foto (Manipulasi Imej dan e-Portfolio)', 'istilah', 'e-Portfolio', 'Koleksi karya digital dalam talian — pelajar mempamerkan hasil kerja seni untuk semakan dan rujukan.', null, null, 'Behance, Adobe Portfolio, laman web peribadi.', 'portfolio'],
    ];
}

function psv_kssm_run(): array
{
    $subject = db_one("SELECT id FROM subjects WHERE slug = 'psv' LIMIT 1");
    if (!$subject) {
        return ['status' => 'no_subject', 'message' => 'PSV subject not found. Run install / seed_subjects first.'];
    }
    $sid = (int) $subject['id'];

    $refsTable = (bool) db_one("SELECT 1 AS x FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'psv_references'");

    $catalog = psv_kssm_catalog();
    $topicsCreated = $topicsKept = $topicsAdopted = 0;
    $subCreated = $subKept = 0;
    $skillsCreated = $skillsKept = 0;
    $refsCreated = $refsKept = 0;

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

    if ($refsTable) {
        foreach (psv_kssm_references() as $idx => [$topicName, $type, $name, $desc, $origin, $era, $example, $category]) {
            $tid = $topicIds[$topicName] ?? null;
            $existing = db_one('SELECT id FROM psv_references WHERE name = ? LIMIT 1', [$name]);
            if ($existing) {
                $refsKept++;
            } else {
                db_exec(
                    'INSERT INTO psv_references (topic_id, topic_label, type, name, description, origin, era, example, category, sort_order)
                     VALUES (?,?,?,?,?,?,?,?,?,?)',
                    [$tid, $topicName, $type, $name, $desc, $origin, $era, $example, $category, $idx + 1]
                );
                $refsCreated++;
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
        'refs_created'       => $refsCreated,
        'refs_kept'          => $refsKept,
        'catalog_topics'     => count($catalog),
        'refs_table'         => $refsTable,
    ];
}

if (PHP_SAPI === 'cli') {
    $r = psv_kssm_run();
    if (($r['status'] ?? '') !== 'ok') {
        fwrite(STDERR, ($r['message'] ?? 'unknown error') . "\n");
        exit(1);
    }
    echo "KSSM Pendidikan Seni Visual seeded —\n";
    echo "  Tajuk:     created {$r['topics_created']}, adopted {$r['topics_adopted']}, kept {$r['topics_kept']} (catalog {$r['catalog_topics']})\n";
    echo "  Subtopik:  created {$r['subtopics_created']}, kept {$r['subtopics_kept']}\n";
    echo "  Kemahiran: created {$r['skills_created']}, kept {$r['skills_kept']}\n";
    if ($r['refs_table']) {
        echo "  Rujukan:   created {$r['refs_created']}, kept {$r['refs_kept']}\n";
    } else {
        echo "  Rujukan:   table missing — run database migrations to enable.\n";
    }
}
