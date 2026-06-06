<?php
/**
 * KSSM SPM Geografi syllabus seeder. Tingkatan 4 (15 bab — peta
 * topografi, geomorfologi, hidrosfera, demografi) + Tingkatan 5
 * (10 bab — graf, foto, cuaca, sumber, ekonomi), plus a starter
 * locations bank (banjaran, sungai, tasik, plat, zon iklim,
 * petempatan) used by the AI Geografi Trainer.
 *
 * Idempotent: bab matched by (subject_id, name, form_level); legacy
 * same-named NULL-form bab are adopted.
 *
 *   php public_html/cron/seed_geografi_kssm.php
 */
declare(strict_types=1);

require_once __DIR__ . '/../inc/db.php';

function geografi_kssm_catalog(): array
{
    return [
        // ============= TINGKATAN 4 =============
        ['form' => 4, 'name' => 'Arah dan Kedudukan dalam Peta Topografi', 'subtopics' => [
            'Arah Mata Angin', 'Bearing Sukuan', 'Bearing Sudutan',
            'Kedudukan Koordinat', 'Rujukan Grid Empat dan Enam Angka',
        ], 'skills' => [
            ['Mengenal pasti arah pada peta topografi', 'easy'],
            ['Mengukur bearing sukuan dan sudutan', 'medium'],
            ['Menentukan kedudukan menggunakan rujukan grid', 'medium'],
        ]],
        ['form' => 4, 'name' => 'Skala, Jarak dan Luas dalam Peta Topografi', 'subtopics' => [
            'Jenis Skala', 'Mengukur Jarak Lurus', 'Mengukur Jarak Lengkok', 'Mengira Luas',
        ], 'skills' => [
            ['Menggunakan skala lurus dan pecahan wakilan', 'medium'],
            ['Mengukur jarak lurus dan lengkok', 'medium'],
            ['Mengira luas kawasan pada peta', 'medium'],
        ]],
        ['form' => 4, 'name' => 'Ketinggian dan Keratan Rentas dalam Peta Topografi', 'subtopics' => [
            'Garisan Kontur', 'Ciri Bentuk Muka Bumi', 'Cerun', 'Keratan Rentas',
        ], 'skills' => [
            ['Mentafsir garisan kontur', 'medium'],
            ['Melukis keratan rentas mudah', 'medium'],
            ['Membezakan jenis cerun', 'medium'],
        ]],
        ['form' => 4, 'name' => 'Pandang Darat Fizikal dan Pandang Darat Budaya dalam Peta Topografi', 'subtopics' => [
            'Pandang Darat Fizikal', 'Pandang Darat Budaya', 'Saling Hubungan',
        ], 'skills' => [
            ['Membandingkan pandang darat fizikal dan budaya', 'medium'],
            ['Mentafsir hubungan ciri budaya dan fizikal', 'medium'],
        ]],
        ['form' => 4, 'name' => 'Pergerakan Plat Tektonik', 'subtopics' => [
            'Struktur Bumi', 'Plat Tektonik Dunia', 'Sempadan Plat',
            'Pergerakan Plat', 'Kesan Pergerakan Plat',
        ], 'skills' => [
            ['Mengenal pasti plat tektonik utama dunia', 'medium'],
            ['Menjelaskan sempadan plat', 'medium'],
            ['Menilai kesan pergerakan plat (gempa, gunung berapi)', 'medium'],
        ]],
        ['form' => 4, 'name' => 'Pembentukan dan Kepentingan Batuan', 'subtopics' => [
            'Jenis Batuan (Igneus / Enapan / Metamorfosis)', 'Pembentukan Batuan',
            'Ciri Batuan', 'Kepentingan Batuan',
        ], 'skills' => [
            ['Membezakan tiga jenis batuan utama', 'medium'],
            ['Menjelaskan pembentukan batuan', 'medium'],
            ['Menilai kepentingan ekonomi batuan', 'medium'],
        ]],
        ['form' => 4, 'name' => 'Proses dan Kesan Luluhawa', 'subtopics' => [
            'Luluhawa Fizikal', 'Luluhawa Kimia', 'Luluhawa Biologi',
            'Faktor Mempengaruhi Luluhawa', 'Kesan Luluhawa',
        ], 'skills' => [
            ['Membandingkan jenis luluhawa', 'medium'],
            ['Menjelaskan faktor luluhawa', 'medium'],
            ['Menilai kesan luluhawa terhadap pandang darat', 'medium'],
        ]],
        ['form' => 4, 'name' => 'Proses dan Gerakan Jisim', 'subtopics' => [
            'Konsep Gerakan Jisim', 'Jenis Gerakan Jisim', 'Faktor', 'Kesan dan Langkah Mengurangkan',
        ], 'skills' => [
            ['Mengenal pasti jenis gerakan jisim', 'medium'],
            ['Menganalisis faktor gerakan jisim', 'medium'],
            ['Mencadangkan langkah mengurangkan kesan', 'medium'],
        ]],
        ['form' => 4, 'name' => 'Pembentukan dan Kelestarian Sungai', 'subtopics' => [
            'Sistem Sungai', 'Peringkat Sungai (Hulu / Tengah / Hilir)',
            'Tindakan Sungai (Hakisan / Pengangkutan / Pemendapan)',
            'Bentuk Muka Bumi Sungai', 'Kelestarian Sungai',
        ], 'skills' => [
            ['Menjelaskan peringkat aliran sungai', 'medium'],
            ['Mengenal pasti bentuk muka bumi sungai', 'medium'],
            ['Mencadangkan langkah pemuliharaan sungai', 'medium'],
        ]],
        ['form' => 4, 'name' => 'Tindakan Ombak di Pinggir Pantai', 'subtopics' => [
            'Jenis Ombak', 'Tindakan Hakisan Ombak', 'Tindakan Pemendapan',
            'Bentuk Muka Bumi Pinggir Pantai', 'Pengurusan Pinggir Pantai',
        ], 'skills' => [
            ['Membezakan tindakan hakisan dan pemendapan ombak', 'medium'],
            ['Mengenal pasti bentuk muka bumi pinggir pantai', 'medium'],
            ['Menilai pengurusan zon pinggir pantai', 'medium'],
        ]],
        ['form' => 4, 'name' => 'Taburan Penduduk Dunia', 'subtopics' => [
            'Konsep Taburan Penduduk', 'Kategori Kepadatan',
            'Faktor Fizikal', 'Faktor Manusia',
        ], 'skills' => [
            ['Mentakrifkan taburan dan kepadatan penduduk', 'easy'],
            ['Menganalisis faktor taburan penduduk', 'medium'],
        ]],
        ['form' => 4, 'name' => 'Pertumbuhan Penduduk Dunia', 'subtopics' => [
            'Konsep Pertumbuhan', 'Kadar Kelahiran dan Kematian',
            'Piramid Penduduk', 'Negara Penduduk Muda dan Tua',
        ], 'skills' => [
            ['Mentafsir piramid penduduk', 'medium'],
            ['Membandingkan negara penduduk muda dan tua', 'medium'],
            ['Menganalisis kesan pertumbuhan penduduk', 'medium'],
        ]],
        ['form' => 4, 'name' => 'Migrasi Penduduk', 'subtopics' => [
            'Konsep Migrasi', 'Migrasi Dalaman dan Antarabangsa',
            'Faktor Tolakan dan Tarikan', 'Kesan Migrasi', 'Langkah Mengurangkan Kesan',
        ], 'skills' => [
            ['Menjelaskan jenis migrasi', 'easy'],
            ['Menganalisis faktor tolakan dan tarikan', 'medium'],
            ['Menilai kesan migrasi terhadap negara', 'medium'],
        ]],
        ['form' => 4, 'name' => 'Petempatan', 'subtopics' => [
            'Jenis Petempatan', 'Hierarki Petempatan', 'Pola Petempatan',
            'Faktor Mempengaruhi Pola', 'Fungsi Bandar dan Luar Bandar',
            'Saling Bergantung antara Bandar dan Luar Bandar',
        ], 'skills' => [
            ['Membandingkan petempatan bandar dan luar bandar', 'medium'],
            ['Menjelaskan hierarki petempatan', 'medium'],
            ['Menganalisis pola petempatan', 'medium'],
        ]],
        ['form' => 4, 'name' => 'Urbanisasi', 'subtopics' => [
            'Konsep dan Proses Urbanisasi', 'Faktor Urbanisasi',
            'Kesan Urbanisasi', 'Langkah Mengurangkan Masalah Urbanisasi',
        ], 'skills' => [
            ['Menjelaskan proses urbanisasi', 'medium'],
            ['Menganalisis kesan urbanisasi', 'medium'],
            ['Mencadangkan penyelesaian masalah bandar', 'medium'],
        ]],

        // ============= TINGKATAN 5 =============
        ['form' => 5, 'name' => 'Graf Bar Berganda, Graf Garisan Berganda dan Carta Pai', 'subtopics' => [
            'Jenis Graf', 'Kegunaan Graf', 'Menghasilkan Graf', 'Menganalisis Graf',
        ], 'skills' => [
            ['Melukis graf bar dan garisan berganda', 'medium'],
            ['Melukis carta pai', 'medium'],
            ['Mentafsir data daripada graf', 'medium'],
        ]],
        ['form' => 5, 'name' => 'Foto', 'subtopics' => [
            'Foto Aras Bumi', 'Foto Udara', 'Foto Satelit',
            'Kepentingan Foto', 'Mentafsir Foto',
        ], 'skills' => [
            ['Membezakan jenis foto', 'easy'],
            ['Mentafsir foto udara dan satelit', 'medium'],
            ['Menilai kepentingan foto dalam kajian geografi', 'medium'],
        ]],
        ['form' => 5, 'name' => 'Fenomena Cuaca dan Iklim', 'subtopics' => [
            'Konsep Cuaca dan Iklim', 'Unsur Cuaca dan Iklim',
            'Fenomena Cuaca dan Iklim di Dunia', 'Persediaan Menghadapi Fenomena',
        ], 'skills' => [
            ['Membezakan cuaca dan iklim', 'easy'],
            ['Menjelaskan fenomena cuaca ekstrem', 'medium'],
            ['Menilai persediaan menghadapi bencana cuaca', 'medium'],
        ]],
        ['form' => 5, 'name' => 'Cuaca dan Iklim serta Pengaruhnya', 'subtopics' => [
            'Zon Iklim Dunia', 'Iklim Khatulistiwa', 'Iklim Monsun',
            'Pengaruh Iklim terhadap Kegiatan Manusia', 'Perubahan Iklim',
        ], 'skills' => [
            ['Mengenal pasti zon iklim utama', 'medium'],
            ['Membandingkan iklim khatulistiwa dan monsun', 'medium'],
            ['Menilai pengaruh iklim terhadap kegiatan ekonomi', 'medium'],
        ]],
        ['form' => 5, 'name' => 'Tumbuh-tumbuhan Semula Jadi dan Hidupan Liar', 'subtopics' => [
            'Jenis Tumbuh-tumbuhan Semula Jadi Dunia',
            'Hutan Khatulistiwa', 'Hutan Konifer', 'Sabana',
            'Hidupan Liar', 'Kepentingan Ekonomi dan Ekologi',
        ], 'skills' => [
            ['Mengenal pasti jenis tumbuhan semula jadi dunia', 'medium'],
            ['Membandingkan hutan khatulistiwa dan hutan konifer', 'medium'],
            ['Menilai kepentingan hidupan liar', 'medium'],
        ]],
        ['form' => 5, 'name' => 'Pemeliharaan dan Pemuliharaan Tumbuh-tumbuhan Semula Jadi dan Hidupan Liar', 'subtopics' => [
            'Konsep Pemeliharaan dan Pemuliharaan',
            'Kegiatan Manusia yang Menyebabkan Kepupusan',
            'Kepentingan Pemeliharaan dan Pemuliharaan',
            'Usaha Pemeliharaan', 'Usaha Pemuliharaan di Dunia',
        ], 'skills' => [
            ['Membezakan pemeliharaan dan pemuliharaan', 'medium'],
            ['Menganalisis sebab kepupusan hidupan liar', 'medium'],
            ['Menilai usaha pemuliharaan global', 'medium'],
        ]],
        ['form' => 5, 'name' => 'Sumber Tenaga', 'subtopics' => [
            'Sumber Tenaga Tidak Boleh Baharu', 'Sumber Tenaga Boleh Baharu',
            'Bahan Api Fosil', 'Tenaga Suria', 'Tenaga Angin',
            'Tenaga Hidroelektrik', 'Biomas dan Biogas',
        ], 'skills' => [
            ['Membezakan sumber tenaga boleh dan tidak boleh baharu', 'easy'],
            ['Menilai sumber tenaga lestari', 'medium'],
            ['Membincangkan kepentingan tenaga boleh baharu', 'medium'],
        ]],
        ['form' => 5, 'name' => 'Kesan Penerokaan dan Pengurusan Sumber Tenaga', 'subtopics' => [
            'Kesan Penerokaan Sumber Tenaga', 'Pencemaran',
            'Perubahan Pandang Darat', 'Pengurusan Sumber Tenaga',
            'Langkah Pemuliharaan',
        ], 'skills' => [
            ['Menganalisis kesan penerokaan sumber tenaga', 'medium'],
            ['Menilai langkah pengurusan sumber', 'medium'],
            ['Mencadangkan amalan tenaga lestari', 'medium'],
        ]],
        ['form' => 5, 'name' => 'Kegiatan Ekonomi Utama', 'subtopics' => [
            'Kegiatan Ekonomi Primer', 'Kegiatan Ekonomi Sekunder',
            'Kegiatan Ekonomi Tertier', 'Pelancongan',
            'Pertanian Komersial', 'Perindustrian',
        ], 'skills' => [
            ['Membezakan kegiatan ekonomi primer, sekunder dan tertier', 'medium'],
            ['Menganalisis kepentingan pelancongan', 'medium'],
            ['Menilai sumbangan sektor ekonomi terhadap negara', 'medium'],
        ]],
        ['form' => 5, 'name' => 'Kesan Kegiatan Ekonomi Terhadap Alam Sekitar', 'subtopics' => [
            'Kesan terhadap Tumbuh-tumbuhan Semula Jadi dan Hidupan Liar',
            'Pencemaran Air, Udara dan Tanah',
            'Hakisan Tanah', 'Perubahan Pandang Darat',
            'Langkah Pemuliharaan Alam Sekitar',
        ], 'skills' => [
            ['Menganalisis kesan kegiatan ekonomi terhadap alam sekitar', 'medium'],
            ['Menilai jenis pencemaran utama', 'medium'],
            ['Mencadangkan langkah pemuliharaan alam sekitar', 'medium'],
        ]],
    ];
}

/**
 * Starter geographical features bank. category =
 *   plate | mountain | river | lake | coast | climate_zone |
 *   vegetation | settlement | resource | landmark.
 */
function geografi_kssm_locations(): array
{
    return [
        // -------- Plat Tektonik --------
        ['Pergerakan Plat Tektonik',                            'plate',         'Plat Eurasia',            'Dunia',          NULL,            'Plat tektonik terbesar yang menampung Eropah dan kebanyakan Asia, termasuk Semenanjung Malaysia.', 'Sempadan plat ini bersempadan dengan Plat Pasifik dan Plat Filipina, menyebabkan aktiviti seismik tinggi di Asia Timur.'],
        ['Pergerakan Plat Tektonik',                            'plate',         'Plat Pasifik',            'Dunia',          NULL,            'Plat lautan terbesar di dunia.', 'Sempadan plat membentuk "Lingkaran Api Pasifik" — kawasan paling aktif gempa bumi dan gunung berapi.'],
        ['Pergerakan Plat Tektonik',                            'plate',         'Plat Indo-Australia',     'Dunia',          NULL,            'Plat yang menampung Australia, India dan sebahagian Lautan Hindi.', 'Pelanggaran dengan Plat Eurasia membentuk Pergunungan Himalaya.'],
        ['Pergerakan Plat Tektonik',                            'plate',         'Plat Filipina',           'Asia Tenggara',  NULL,            'Plat lautan kecil di antara Plat Eurasia dan Pasifik.', 'Bertanggungjawab terhadap gempa bumi dan tsunami di rantau Asia Tenggara.'],
        ['Pergerakan Plat Tektonik',                            'mountain',      'Gunung Berapi Krakatoa',  'Indonesia',      '6.10S, 105.42E','Gunung berapi tersohor antara Pulau Jawa dan Sumatera.', 'Letusan 1883 antara yang terdahsyat dalam sejarah moden — bukti aktiviti sempadan plat Indo-Australia dan Eurasia.'],
        ['Pergerakan Plat Tektonik',                            'mountain',      'Pergunungan Himalaya',    'Asia Selatan',   NULL,            'Banjaran gunung tertinggi di dunia, termasuk Gunung Everest.', 'Terbentuk daripada pelanggaran Plat Indo-Australia dan Plat Eurasia.'],

        // -------- Banjaran / Gunung Malaysia --------
        ['Pembentukan dan Kepentingan Batuan',                  'mountain',      'Banjaran Titiwangsa',     'Malaysia',       NULL,            'Banjaran utama Semenanjung Malaysia, membentang dari Perlis ke Negeri Sembilan.', 'Tulang belakang Semenanjung; kawasan tadahan air utama; mengandungi banyak deposit bijih.'],
        ['Pembentukan dan Kepentingan Batuan',                  'mountain',      'Gunung Kinabalu',         'Sabah',          '6.0753N, 116.5586E', 'Gunung tertinggi di Malaysia, 4,095 m.', 'Tapak Warisan Dunia UNESCO; granit muda yang masih tumbuh ~5 mm setahun.'],
        ['Pembentukan dan Kepentingan Batuan',                  'mountain',      'Gunung Tahan',            'Pahang',         '4.3833N, 102.2333E', 'Gunung tertinggi di Semenanjung Malaysia, 2,187 m.', 'Terletak di Taman Negara — kawasan hutan hujan tropika yang dilindungi.'],
        ['Pembentukan dan Kepentingan Batuan',                  'mountain',      'Banjaran Crocker',        'Sabah',          NULL,            'Banjaran utama di Sabah dari Tenom ke Kudat.', 'Kawasan tadahan utama Sungai Padas dan Sungai Papar.'],

        // -------- Sungai --------
        ['Pembentukan dan Kelestarian Sungai',                  'river',         'Sungai Pahang',           'Malaysia',       NULL,            'Sungai terpanjang di Semenanjung Malaysia, ~459 km.', 'Lembah Sungai Pahang penting untuk pertanian dan petempatan; banjir besar 1971, 2014, 2021.'],
        ['Pembentukan dan Kelestarian Sungai',                  'river',         'Sungai Rajang',           'Sarawak',        NULL,            'Sungai terpanjang di Malaysia, ~563 km.', 'Lebuhraya air utama Sarawak; pusat industri kayu balak Sibu.'],
        ['Pembentukan dan Kelestarian Sungai',                  'river',         'Sungai Kinabatangan',     'Sabah',          NULL,            'Sungai terpanjang di Sabah, ~560 km.', 'Habitat hidupan liar — orang utan, gajah pigmi, monyet belanda. Eko-pelancongan utama.'],
        ['Pembentukan dan Kelestarian Sungai',                  'river',         'Sungai Mekong',           'Asia Tenggara',  NULL,            'Sungai terpanjang di Asia Tenggara, ~4,350 km.', 'Mengalir melalui enam negara; nadi ekonomi Indochina dan Delta Mekong.'],
        ['Pembentukan dan Kelestarian Sungai',                  'river',         'Sungai Nil',              'Afrika',         NULL,            'Sungai terpanjang di dunia, ~6,650 km.', 'Tamadun Mesir kuno bergantung pada banjir Nil; kini empangan Aswan mengawal aliran.'],
        ['Pembentukan dan Kelestarian Sungai',                  'river',         'Sungai Amazon',           'Amerika Selatan',NULL,            'Sungai bervolum air terbesar di dunia.', 'Lembah Amazon merupakan hutan hujan terbesar dan paru-paru Bumi.'],

        // -------- Tasik --------
        ['Pembentukan dan Kelestarian Sungai',                  'lake',          'Tasik Kenyir',            'Terengganu',     NULL,            'Tasik buatan manusia terbesar di Asia Tenggara.', 'Empangan hidroelektrik Sultan Mahmud; eko-pelancongan dan akuakultur.'],
        ['Pembentukan dan Kelestarian Sungai',                  'lake',          'Tasik Bera',              'Pahang',         NULL,            'Tasik air tawar semula jadi terbesar di Semenanjung Malaysia.', 'Tapak Ramsar pertama Malaysia — kawasan tanah lembap antarabangsa yang dilindungi.'],
        ['Pembentukan dan Kelestarian Sungai',                  'lake',          'Tasik Chini',             'Pahang',         NULL,            'Tasik air tawar kedua terbesar di Semenanjung Malaysia, terkenal dengan legenda Naga Seri Gumum.', 'Rizab Biosfera UNESCO; teratai merah ikonik.'],

        // -------- Pinggir Pantai --------
        ['Tindakan Ombak di Pinggir Pantai',                    'coast',         'Pantai Timur Semenanjung Malaysia', 'Malaysia', NULL,         'Pantai berhakisan akibat ombak monsun timur laut Disember-Mac.', 'Kelantan, Terengganu dan Pahang terjejas hakisan pantai serius; benteng dan pemecah ombak dibina.'],
        ['Tindakan Ombak di Pinggir Pantai',                    'coast',         'Tanjung Tuan',            'Melaka',         NULL,            'Tanjung berbatu di selatan Selat Melaka.', 'Bentuk muka bumi hakisan ombak — tebing, gua, batu tunggul.'],

        // -------- Cuaca / Iklim --------
        ['Cuaca dan Iklim serta Pengaruhnya',                   'climate_zone',  'Zon Khatulistiwa',        'Dunia',          NULL,            'Kawasan antara 10°U dan 10°S; suhu seragam tinggi sepanjang tahun, hujan ~2000mm.', 'Malaysia, Indonesia dan lembah Amazon termasuk zon ini.'],
        ['Cuaca dan Iklim serta Pengaruhnya',                   'climate_zone',  'Zon Monsun Tropika',      'Dunia',          NULL,            'Kawasan dipengaruhi angin monsun bermusim.', 'India, Bangladesh, Vietnam dan Thailand mengalami iklim ini.'],
        ['Cuaca dan Iklim serta Pengaruhnya',                   'climate_zone',  'Zon Sederhana',           'Dunia',          NULL,            'Empat musim — musim bunga, panas, luruh dan sejuk.', 'Eropah, Jepun, Korea dan timur laut Amerika Syarikat.'],
        ['Cuaca dan Iklim serta Pengaruhnya',                   'climate_zone',  'Zon Kutub',               'Dunia',          NULL,            'Kawasan Artik dan Antartika dengan suhu purata di bawah takat beku.', 'Padang ais dan tundra; populasi rendah, kebanyakan bumiputera Inuit.'],

        // -------- Tumbuh-tumbuhan --------
        ['Tumbuh-tumbuhan Semula Jadi dan Hidupan Liar',        'vegetation',    'Hutan Hujan Khatulistiwa', 'Dunia',         NULL,            'Hutan tropika tebal dengan biodiversiti tertinggi di dunia.', 'Amazon, Kongo, Borneo dan Sumatera; menyerap CO2 utama dunia.'],
        ['Tumbuh-tumbuhan Semula Jadi dan Hidupan Liar',        'vegetation',    'Hutan Konifer (Taiga)',   'Dunia',          NULL,            'Hutan pokok jarum di kawasan lintang sederhana ke tinggi.', 'Kanada, Skandinavia, Russia; sumber kayu balak utama dunia.'],
        ['Tumbuh-tumbuhan Semula Jadi dan Hidupan Liar',        'vegetation',    'Sabana',                  'Afrika',         NULL,            'Padang rumput tropika dengan pokok bertaburan.', 'Afrika Timur (Serengeti, Maasai Mara) — habitat hidupan liar terkenal.'],
        ['Pemeliharaan dan Pemuliharaan Tumbuh-tumbuhan Semula Jadi dan Hidupan Liar', 'landmark', 'Taman Negara Pahang', 'Malaysia', NULL, 'Taman negara tertua Malaysia, ditubuhkan 1938/1939.', 'Salah satu hutan hujan tertua dunia (~130 juta tahun); rumah harimau, gajah dan tapir Malaya.'],

        // -------- Penduduk / Petempatan --------
        ['Taburan Penduduk Dunia',                              'settlement',    'Lembah Klang',            'Malaysia',       NULL,            'Kawasan paling padat penduduk Malaysia (>1000 orang/km²).', 'Termasuk Kuala Lumpur, Petaling Jaya, Shah Alam, Klang; pusat ekonomi negara.'],
        ['Taburan Penduduk Dunia',                              'settlement',    'Lembah Sungai Ganges',    'India',          NULL,            'Antara kawasan paling padat penduduk di dunia.', 'Tanah aluvium subur menampung > 400 juta orang.'],
        ['Urbanisasi',                                          'settlement',    'Putrajaya',               'Malaysia',       '2.9264N, 101.6964E','Pusat pentadbiran persekutuan Malaysia, ditubuhkan 1995.', 'Bandar yang dirancang dengan teliti; contoh urbanisasi terpimpin.'],
        ['Urbanisasi',                                          'settlement',    'Cyberjaya',               'Malaysia',       NULL,            'Bandar teknologi Malaysia; sebahagian Koridor Raya Multimedia (MSC).', 'Contoh urbanisasi berasaskan ekonomi pengetahuan.'],

        // -------- Sumber --------
        ['Sumber Tenaga',                                       'resource',      'Empangan Bakun',          'Sarawak',        NULL,            'Empangan hidroelektrik terbesar Malaysia (2,400 MW).', 'Bekalan tenaga utama Sarawak; kontroversi pemindahan masyarakat orang asli.'],
        ['Sumber Tenaga',                                       'resource',      'Empangan Hidroelektrik Pergau', 'Kelantan',NULL,            'Empangan hidroelektrik utama Semenanjung Timur Laut.', 'Kontroversi diplomatik dengan United Kingdom pada tahun 1990-an.'],
        ['Sumber Tenaga',                                       'resource',      'Telaga Minyak Kikeh',     'Sabah',          NULL,            'Telaga minyak luar pesisir utama Malaysia.', 'Sumber petroleum penting; dimajukan Petronas dan Murphy Oil.'],
        ['Sumber Tenaga',                                       'resource',      'Loji Solar Besar Malaysia', 'Malaysia',     NULL,            'Loji janakuasa solar berskala besar di bawah program LSS.', 'Sumbangan utama Malaysia ke arah sasaran 31% tenaga boleh baharu menjelang 2025.'],

        // -------- Kegiatan Ekonomi --------
        ['Kegiatan Ekonomi Utama',                              'landmark',      'Ladang Kelapa Sawit Felda', 'Malaysia',     NULL,            'Skim pembukaan tanah ladang kelapa sawit terbesar dunia.', 'Mengangkat ribuan peneroka keluar dari kemiskinan; tunggak ekonomi luar bandar.'],
        ['Kegiatan Ekonomi Utama',                              'landmark',      'Zon Perindustrian Pasir Gudang', 'Johor', NULL,            'Pelabuhan dan kompleks perindustrian utama selatan Malaysia.', 'Petrokimia, pembuatan dan logistik; berdekatan Singapura.'],
        ['Kegiatan Ekonomi Utama',                              'landmark',      'Langkawi',                'Kedah',          NULL,            'Geopark UNESCO dan zon pelancongan bebas cukai.', 'Eko-pelancongan, pelancongan pulau; sumbangan besar kepada PDB negeri.'],
    ];
}

function geografi_kssm_run(): array
{
    $subject = db_one("SELECT id FROM subjects WHERE slug = 'geografi' LIMIT 1");
    if (!$subject) {
        return ['status' => 'no_subject', 'message' => 'Geografi subject not found. Run install / seed_subjects first.'];
    }
    $sid = (int) $subject['id'];

    $locationsTable = (bool) db_one("SELECT 1 AS x FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'geografi_locations'");

    $catalog = geografi_kssm_catalog();
    $topicsCreated = $topicsKept = $topicsAdopted = 0;
    $subCreated = $subKept = 0;
    $skillsCreated = $skillsKept = 0;
    $locationsCreated = $locationsKept = 0;

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

    if ($locationsTable) {
        foreach (geografi_kssm_locations() as $idx => [$topicName, $category, $name, $region, $coords, $desc, $significance]) {
            $tid = $topicIds[$topicName] ?? null;
            $existing = db_one('SELECT id FROM geografi_locations WHERE name = ? LIMIT 1', [$name]);
            if ($existing) {
                $locationsKept++;
            } else {
                db_exec(
                    'INSERT INTO geografi_locations (topic_id, topic_label, category, name, region, coordinates, description, significance, sort_order)
                     VALUES (?,?,?,?,?,?,?,?,?)',
                    [$tid, $topicName, $category, $name, $region, $coords, $desc, $significance, $idx + 1]
                );
                $locationsCreated++;
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
        'locations_created'  => $locationsCreated,
        'locations_kept'     => $locationsKept,
        'catalog_topics'     => count($catalog),
        'locations_table'    => $locationsTable,
    ];
}

if (PHP_SAPI === 'cli') {
    $r = geografi_kssm_run();
    if (($r['status'] ?? '') !== 'ok') {
        fwrite(STDERR, ($r['message'] ?? 'unknown error') . "\n");
        exit(1);
    }
    echo "KSSM Geografi seeded —\n";
    echo "  Bab:        created {$r['topics_created']}, adopted {$r['topics_adopted']}, kept {$r['topics_kept']} (catalog {$r['catalog_topics']})\n";
    echo "  Subtopik:   created {$r['subtopics_created']}, kept {$r['subtopics_kept']}\n";
    echo "  Kemahiran:  created {$r['skills_created']}, kept {$r['skills_kept']}\n";
    if ($r['locations_table']) {
        echo "  Lokasi:     created {$r['locations_created']}, kept {$r['locations_kept']}\n";
    } else {
        echo "  Lokasi:     table missing — run database migrations to enable.\n";
    }
}
