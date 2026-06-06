<?php
/**
 * KSSM SPM Sains (integrated science) syllabus seeder.
 * Tingkatan 4 (12 bab — keselamatan, kesihatan, teknologi hijau,
 * genetik, koordinasi, kimia, daya & gerakan, tenaga nuklear) +
 * Tingkatan 5 (9 bab — mikroorganisma, nutrisi, kelestarian, kadar
 * tindak balas, sebatian karbon, elektrokimia, optik, daya & tekanan,
 * angkasa lepas) — 21 bab total, dengan starter bank konsep merentas
 * biologi/kimia/fizik.
 *
 * Idempotent: bab matched by (subject_id, name, form_level); legacy
 * same-named NULL-form bab are adopted.
 *
 *   php public_html/cron/seed_sains_kssm.php
 */
declare(strict_types=1);

require_once __DIR__ . '/../inc/db.php';

function sains_kssm_catalog(): array
{
    return [
        // ============= TINGKATAN 4 =============
        ['form' => 4, 'name' => 'Langkah Keselamatan di dalam Makmal', 'subtopics' => [
            'Peraturan Keselamatan Makmal',
            'Simbol Bahaya (Toksik, Mudah Terbakar, Hakis)',
            'Peralatan Keselamatan (Cermin Mata, Sarung Tangan, Pancut Mata)',
            'Tindakan dalam Situasi Kecemasan',
        ], 'skills' => [
            ['Mengenal pasti simbol bahaya', 'easy'],
            ['Mengamalkan peraturan keselamatan makmal', 'easy'],
            ['Mengendalikan situasi kecemasan makmal', 'medium'],
        ]],
        ['form' => 4, 'name' => 'Bantuan Kecemasan', 'subtopics' => [
            'Konsep Bantuan Kecemasan',
            'CPR (Cardiopulmonary Resuscitation)',
            'Heimlich Manoeuvre',
            'Rawatan Luka, Lebam dan Patah Tulang',
            'Rawatan Renjatan dan Pengsan',
        ], 'skills' => [
            ['Menjelaskan langkah CPR', 'medium'],
            ['Mengaplikasikan teknik Heimlich', 'medium'],
            ['Memberi rawatan asas untuk luka dan lebam', 'medium'],
        ]],
        ['form' => 4, 'name' => 'Teknik Mengukur Parameter Kesihatan Badan', 'subtopics' => [
            'Indeks Jisim Badan (BMI)',
            'Tekanan Darah',
            'Denyutan Nadi',
            'Suhu Badan',
            'Kadar Pernafasan',
        ], 'skills' => [
            ['Mengira BMI', 'medium'],
            ['Mengukur tekanan darah dan denyutan nadi', 'medium'],
            ['Mentafsir keputusan parameter kesihatan', 'medium'],
        ]],
        ['form' => 4, 'name' => 'Teknologi Hijau dalam Melestarikan Alam', 'subtopics' => [
            'Konsep Teknologi Hijau',
            'Sumber Tenaga Boleh Diperbaharui',
            'Bangunan Hijau dan Reka Bentuk Lestari',
            'Pengurusan Sisa dan Kitar Semula',
            'Pengangkutan Mesra Alam',
        ], 'skills' => [
            ['Menjelaskan konsep teknologi hijau', 'medium'],
            ['Menilai aplikasi teknologi hijau', 'medium'],
            ['Mencadangkan langkah mesra alam', 'medium'],
        ]],
        ['form' => 4, 'name' => 'Genetik', 'subtopics' => [
            'Konsep Genetik dan DNA',
            'Kromosom dan Gen',
            'Pewarisan Mendel (Monohibrid)',
            'Penyakit Genetik (Talasemia, Hemofilia)',
            'Bioteknologi Genetik',
        ], 'skills' => [
            ['Menerangkan struktur DNA dan kromosom', 'medium'],
            ['Mengaplikasikan Hukum Mendel', 'hard'],
            ['Membincangkan implikasi penyakit genetik', 'medium'],
        ]],
        ['form' => 4, 'name' => 'Sokongan, Pergerakan dan Pertumbuhan', 'subtopics' => [
            'Sistem Rangka Manusia',
            'Otot dan Pergerakan',
            'Sendi dan Jenis Pergerakan',
            'Sokongan dan Pergerakan dalam Tumbuhan',
            'Tropisme',
        ], 'skills' => [
            ['Mengenal pasti tulang utama rangka', 'easy'],
            ['Menjelaskan mekanisme pergerakan otot', 'medium'],
            ['Membezakan jenis tropisme', 'medium'],
        ]],
        ['form' => 4, 'name' => 'Koordinasi Badan', 'subtopics' => [
            'Sistem Saraf (CNS dan PNS)',
            'Otak dan Saraf Tunjang',
            'Tindak Balas Refleks',
            'Sistem Endokrin dan Hormon',
            'Homeostasis',
        ], 'skills' => [
            ['Menerangkan struktur sistem saraf', 'medium'],
            ['Membezakan tindak balas refleks dan terkawal', 'medium'],
            ['Menjelaskan peranan hormon dalam homeostasis', 'hard'],
        ]],
        ['form' => 4, 'name' => 'Unsur dan Bahan', 'subtopics' => [
            'Struktur Atom dan Konfigurasi Elektron',
            'Jadual Berkala Unsur',
            'Ikatan Kimia (Ionik dan Kovalen)',
            'Logam dan Bukan Logam',
            'Bahan dan Aloi',
        ], 'skills' => [
            ['Mengenal pasti unsur dalam jadual berkala', 'easy'],
            ['Membezakan ikatan ionik dan kovalen', 'medium'],
            ['Menjelaskan kegunaan aloi', 'medium'],
        ]],
        ['form' => 4, 'name' => 'Kimia Industri', 'subtopics' => [
            'Proses Pembuatan Asid Sulfurik (Proses Sentuhan)',
            'Pembuatan Ammonia (Proses Haber)',
            'Pembuatan Bahan Pencuci (Sabun dan Detergen)',
            'Polimer dan Plastik',
            'Petrokimia',
        ], 'skills' => [
            ['Menerangkan proses pembuatan asid sulfurik', 'medium'],
            ['Membezakan sabun dan detergen', 'medium'],
            ['Menjelaskan kepentingan petrokimia', 'medium'],
        ]],
        ['form' => 4, 'name' => 'Kimia dalam Perubatan dan Kesihatan', 'subtopics' => [
            'Antibiotik dan Antiseptik',
            'Analgesik dan Antipiretik',
            'Suplemen Pemakanan dan Vitamin',
            'Kosmetik',
            'Penyalahgunaan Dadah',
        ], 'skills' => [
            ['Membezakan antibiotik dan antiseptik', 'medium'],
            ['Menjelaskan fungsi suplemen pemakanan', 'medium'],
            ['Menilai kesan penyalahgunaan dadah', 'medium'],
        ]],
        ['form' => 4, 'name' => 'Daya dan Gerakan', 'subtopics' => [
            'Daya dan Hukum Newton',
            'Momentum',
            'Daya Geseran',
            'Tekanan dalam Cecair dan Gas',
            'Prinsip Archimedes dan Bernoulli',
        ], 'skills' => [
            ['Mengaplikasikan Hukum Newton', 'hard'],
            ['Mengira momentum', 'medium'],
            ['Menjelaskan prinsip Archimedes', 'medium'],
        ]],
        ['form' => 4, 'name' => 'Tenaga Nuklear', 'subtopics' => [
            'Struktur Nukleus dan Isotop',
            'Reputan Radioaktif',
            'Pembelahan Nuklear (Fission)',
            'Pelakuran Nuklear (Fusion)',
            'Aplikasi dan Bahaya Tenaga Nuklear',
        ], 'skills' => [
            ['Menerangkan reputan radioaktif', 'medium'],
            ['Membezakan pembelahan dan pelakuran nuklear', 'medium'],
            ['Menilai aplikasi dan risiko tenaga nuklear', 'medium'],
        ]],

        // ============= TINGKATAN 5 =============
        ['form' => 5, 'name' => 'Mikroorganisma', 'subtopics' => [
            'Jenis Mikroorganisma (Bakteria, Virus, Kulat, Protozoa)',
            'Pertumbuhan dan Pembiakan Mikroorganisma',
            'Penyakit Akibat Mikroorganisma',
            'Aplikasi Berfaedah Mikroorganisma',
            'Imuniti dan Vaksinasi',
        ], 'skills' => [
            ['Mengenal pasti jenis mikroorganisma', 'easy'],
            ['Menjelaskan kitar pembiakan mikroorganisma', 'medium'],
            ['Membincangkan aplikasi mikroorganisma dalam industri', 'medium'],
        ]],
        ['form' => 5, 'name' => 'Nutrisi dan Teknologi Makanan', 'subtopics' => [
            'Nutrien Utama (Karbohidrat, Protein, Lipid, Vitamin, Mineral)',
            'Pencernaan dan Penyerapan',
            'Pengawetan Makanan (Pembekuan, Pengeringan, Penyalaian)',
            'Bahan Tambah Makanan',
            'Bioteknologi Makanan',
        ], 'skills' => [
            ['Mengenal pasti nutrien dalam makanan', 'easy'],
            ['Menjelaskan kaedah pengawetan makanan', 'medium'],
            ['Menilai kesan bahan tambah makanan', 'medium'],
        ]],
        ['form' => 5, 'name' => 'Kelestarian Alam', 'subtopics' => [
            'Pencemaran Alam (Air, Udara, Tanah, Bunyi)',
            'Kesan Rumah Hijau dan Pemanasan Global',
            'Penipisan Lapisan Ozon',
            'Hujan Asid',
            'Langkah Pemuliharaan Alam Sekitar',
        ], 'skills' => [
            ['Menerangkan jenis pencemaran', 'easy'],
            ['Menganalisis kesan rumah hijau', 'medium'],
            ['Mencadangkan langkah pemuliharaan', 'medium'],
        ]],
        ['form' => 5, 'name' => 'Kadar Tindak Balas', 'subtopics' => [
            'Konsep Kadar Tindak Balas',
            'Teori Perlanggaran',
            'Faktor Mempengaruhi Kadar (Suhu, Kepekatan, Saiz, Mangkin)',
            'Pengiraan Kadar Tindak Balas',
        ], 'skills' => [
            ['Menjelaskan teori perlanggaran', 'medium'],
            ['Menganalisis faktor yang mempengaruhi kadar', 'medium'],
            ['Mengira kadar tindak balas', 'hard'],
        ]],
        ['form' => 5, 'name' => 'Sebatian Karbon', 'subtopics' => [
            'Hidrokarbon (Alkana, Alkena, Alkuna)',
            'Alkohol dan Asid Karboksilik',
            'Ester dan Lemak',
            'Karbohidrat dan Protein',
            'Polimer Asli dan Sintetik',
        ], 'skills' => [
            ['Mengenal pasti siri homolog hidrokarbon', 'medium'],
            ['Menulis formula struktur sebatian karbon', 'medium'],
            ['Membezakan polimer asli dan sintetik', 'medium'],
        ]],
        ['form' => 5, 'name' => 'Elektrokimia', 'subtopics' => [
            'Sel Elektrolisis',
            'Sel Kimia (Sel Voltaik)',
            'Siri Elektrokimia',
            'Aplikasi Elektrokimia (Penyaduran, Penulenan Logam)',
            'Bateri dan Sel Bahan Api',
        ], 'skills' => [
            ['Menjelaskan proses elektrolisis', 'medium'],
            ['Mengenal pasti elektrod aktif dan inert', 'medium'],
            ['Membincangkan aplikasi elektrokimia', 'medium'],
        ]],
        ['form' => 5, 'name' => 'Cahaya dan Optik', 'subtopics' => [
            'Pantulan dan Pembiasan Cahaya',
            'Kanta Cembung dan Cekung',
            'Pembentukan Imej',
            'Pembesar Mudah, Mikroskop, Teleskop',
            'Cahaya dan Penglihatan',
        ], 'skills' => [
            ['Mengaplikasikan hukum pantulan dan pembiasan', 'medium'],
            ['Melukis gambar rajah sinar bagi kanta', 'hard'],
            ['Menjelaskan pembentukan imej dalam alat optik', 'medium'],
        ]],
        ['form' => 5, 'name' => 'Daya dan Tekanan', 'subtopics' => [
            'Tekanan dalam Pepejal',
            'Tekanan dalam Cecair (Hidrostatik)',
            'Tekanan Atmosfera',
            'Prinsip Pascal dan Aplikasi',
            'Prinsip Bernoulli dan Aerodinamik',
        ], 'skills' => [
            ['Mengira tekanan dalam pepejal dan cecair', 'medium'],
            ['Menjelaskan prinsip Pascal dan Bernoulli', 'medium'],
            ['Menganalisis aplikasi tekanan dalam kehidupan', 'medium'],
        ]],
        ['form' => 5, 'name' => 'Teknologi Angkasa Lepas', 'subtopics' => [
            'Sistem Suria dan Galaksi',
            'Roket dan Satelit',
            'Penerokaan Angkasa Lepas',
            'Astronot Malaysia (Sheikh Muszaphar)',
            'Aplikasi Teknologi Angkasa dalam Kehidupan',
        ], 'skills' => [
            ['Menjelaskan struktur sistem suria', 'easy'],
            ['Menerangkan fungsi roket dan satelit', 'medium'],
            ['Menilai sumbangan teknologi angkasa', 'medium'],
        ]],
    ];
}

/** Starter Sains concepts bank — campuran istilah + formula. */
function sains_kssm_concepts(): array
{
    return [
        // topic, type, name, definition, formula, example, category

        // --- Keselamatan / Kecemasan ---
        ['Bantuan Kecemasan', 'istilah', 'CPR', 'Cardiopulmonary Resuscitation — teknik gabungan tekanan dada dan bantuan pernafasan untuk mangsa yang berhenti bernafas.', null, '30 tekanan dada + 2 bantuan nafas berulang sehingga bantuan tiba.', 'kecemasan'],
        ['Bantuan Kecemasan', 'istilah', 'Heimlich Manoeuvre', 'Teknik memberi tekanan abdomen untuk membantu mangsa tercekik mengeluarkan objek yang tersekat dalam saluran nafas.', null, 'Berdiri di belakang mangsa, genggam abdomen dan tekan ke atas dengan kuat.', 'kecemasan'],

        // --- Kesihatan ---
        ['Teknik Mengukur Parameter Kesihatan Badan', 'formula', 'Indeks Jisim Badan (BMI)', 'Ukuran nisbah jisim badan kepada ketinggian — penunjuk berat badan sihat.', 'BMI = Jisim (kg) ÷ Tinggi² (m²)', 'Jisim 60 kg, tinggi 1.65 m: BMI = 60 ÷ 2.7225 ≈ 22.0 (normal).', 'kesihatan'],
        ['Teknik Mengukur Parameter Kesihatan Badan', 'istilah', 'Tekanan Darah Normal', 'Bacaan tekanan darah yang sihat untuk dewasa.', null, 'Sistolik < 120 mmHg, Diastolik < 80 mmHg (120/80).', 'kesihatan'],
        ['Teknik Mengukur Parameter Kesihatan Badan', 'istilah', 'Hipertensi', 'Keadaan tekanan darah tinggi secara berterusan — risiko penyakit jantung dan strok.', null, 'Tekanan darah ≥ 140/90 mmHg.', 'kesihatan'],

        // --- Teknologi Hijau ---
        ['Teknologi Hijau dalam Melestarikan Alam', 'istilah', 'Sumber Tenaga Boleh Diperbaharui', 'Sumber tenaga yang tidak akan habis kerana diisi semula secara semula jadi.', null, 'Tenaga suria, angin, hidro, biomas, biogas.', 'teknologi_hijau'],
        ['Teknologi Hijau dalam Melestarikan Alam', 'istilah', 'Jejak Karbon', 'Jumlah pelepasan gas rumah hijau (terutama CO₂) yang dihasilkan oleh aktiviti manusia.', null, 'Pengangkutan kereta peribadi menyumbang besar kepada jejak karbon harian.', 'teknologi_hijau'],

        // --- Genetik ---
        ['Genetik', 'istilah', 'DNA', 'Deoxyribonucleic Acid — molekul yang membawa maklumat genetik dalam semua organisma hidup.', null, 'Struktur heliks berganda dengan bes A-T dan G-C.', 'genetik'],
        ['Genetik', 'istilah', 'Gen Dominan', 'Gen yang akan ditunjukkan dalam fenotip walaupun hanya satu alel hadir (heterozigot atau homozigot).', null, 'Bagi tinggi tumbuhan: gen tinggi (T) adalah dominan terhadap rendah (t).', 'genetik'],
        ['Genetik', 'istilah', 'Hukum Mendel Pertama', 'Hukum Pemisahan — pasangan alel berasingan semasa pembentukan gamet sehingga setiap gamet mengandungi hanya satu alel.', null, 'Tt → gamet T atau t (50:50).', 'genetik'],
        ['Genetik', 'istilah', 'Talasemia', 'Penyakit genetik di mana badan tidak menghasilkan hemoglobin secukupnya — diwarisi secara resesif.', null, 'Pesakit memerlukan transfusi darah berkala.', 'penyakit_genetik'],

        // --- Koordinasi ---
        ['Koordinasi Badan', 'istilah', 'Sistem Saraf Pusat (CNS)', 'Terdiri daripada otak dan saraf tunjang — pusat kawalan utama badan.', null, 'Otak memproses maklumat dan menghantar arahan; saraf tunjang menyampaikan isyarat.', 'sistem_saraf'],
        ['Koordinasi Badan', 'istilah', 'Tindak Balas Refleks', 'Tindak balas pantas dan automatik kepada rangsangan tanpa melibatkan otak.', null, 'Menarik tangan dari objek panas; lutut menyentak apabila dipukul ringan.', 'sistem_saraf'],
        ['Koordinasi Badan', 'istilah', 'Homeostasis', 'Proses mengekalkan keadaan dalaman badan (suhu, pH, glukosa) yang stabil walaupun persekitaran berubah.', null, 'Berpeluh untuk menurunkan suhu badan; hormon insulin mengawal gula darah.', 'koordinasi'],

        // --- Unsur dan Bahan ---
        ['Unsur dan Bahan', 'istilah', 'Ikatan Ionik', 'Ikatan yang terbentuk apabila atom logam menderma elektron kepada atom bukan logam — menghasilkan ion positif dan negatif yang tertarik secara elektrostatik.', null, 'NaCl: Na menderma 1 elektron kepada Cl.', 'ikatan_kimia'],
        ['Unsur dan Bahan', 'istilah', 'Ikatan Kovalen', 'Ikatan yang terbentuk apabila dua atom bukan logam berkongsi pasangan elektron.', null, 'H₂O: dua atom H berkongsi elektron dengan satu O.', 'ikatan_kimia'],
        ['Unsur dan Bahan', 'istilah', 'Aloi', 'Bahan logam yang terdiri daripada gabungan dua atau lebih unsur — sekurang-kurangnya satu adalah logam.', null, 'Keluli (besi + karbon), gangsa (kuprum + timah), loyang (kuprum + zink).', 'bahan'],

        // --- Kimia Industri ---
        ['Kimia Industri', 'istilah', 'Proses Sentuhan', 'Proses pembuatan asid sulfurik (H₂SO₄) menggunakan mangkin vanadium(V) oksida untuk menukar SO₂ kepada SO₃.', null, 'Tindak balas: 2SO₂ + O₂ → 2SO₃ (mangkin V₂O₅).', 'industri'],
        ['Kimia Industri', 'istilah', 'Proses Haber', 'Proses pembuatan ammonia (NH₃) daripada nitrogen dan hidrogen dengan mangkin besi pada suhu dan tekanan tinggi.', null, 'N₂ + 3H₂ → 2NH₃ (mangkin Fe, 450°C, 200 atm).', 'industri'],
        ['Kimia Industri', 'istilah', 'Sabun', 'Garam logam alkali bagi asid karboksilik rantai panjang — dihasilkan melalui saponifikasi lemak dengan natrium hidroksida.', null, 'Sabun mandi, sabun cuci pakaian.', 'industri'],
        ['Kimia Industri', 'istilah', 'Detergen', 'Bahan pencuci sintetik yang berkesan dalam air liat berbanding sabun.', null, 'Serbuk cuci, sabun pencuci pinggan.', 'industri'],

        // --- Kimia Perubatan ---
        ['Kimia dalam Perubatan dan Kesihatan', 'istilah', 'Antibiotik', 'Ubat yang membunuh atau menghalang pertumbuhan bakteria — tidak berkesan terhadap virus.', null, 'Penicillin, amoxicillin, tetracycline.', 'ubat'],
        ['Kimia dalam Perubatan dan Kesihatan', 'istilah', 'Analgesik', 'Ubat yang menghilangkan kesakitan tanpa hilangkan kesedaran.', null, 'Paracetamol, aspirin, ibuprofen.', 'ubat'],
        ['Kimia dalam Perubatan dan Kesihatan', 'istilah', 'Antipiretik', 'Ubat yang menurunkan demam.', null, 'Paracetamol, ibuprofen.', 'ubat'],

        // --- Daya dan Gerakan ---
        ['Daya dan Gerakan', 'formula', 'Hukum Newton Kedua', 'Daya yang dikenakan ke atas objek adalah hasil darab jisim dan pecutan.', 'F = ma', '5 kg objek dengan pecutan 3 m/s²: F = 5 × 3 = 15 N.', 'daya'],
        ['Daya dan Gerakan', 'formula', 'Momentum', 'Hasil darab jisim dan halaju — diukur dalam kg·m/s.', 'p = mv', 'Kereta 1000 kg bergerak 20 m/s: p = 20,000 kg·m/s.', 'daya'],
        ['Daya dan Gerakan', 'istilah', 'Prinsip Archimedes', 'Daya tujah ke atas objek dalam bendalir sama dengan berat bendalir yang disesarkan.', 'Daya Tujah = ρgV', 'Objek di dalam air mengalami daya tujah ke atas; jika daya tujah > berat, ia terapung.', 'tekanan'],
        ['Daya dan Gerakan', 'istilah', 'Prinsip Bernoulli', 'Apabila halaju cecair/gas bertambah, tekanannya berkurang.', null, 'Sayap kapal terbang direka supaya udara di atas bergerak laju → tekanan rendah → daya angkat ke atas.', 'tekanan'],

        // --- Tenaga Nuklear ---
        ['Tenaga Nuklear', 'istilah', 'Isotop', 'Atom-atom unsur sama yang mempunyai bilangan proton sama tetapi bilangan neutron berbeza.', null, 'Karbon-12 dan Karbon-14 (kedua-duanya 6 proton, tetapi 6 dan 8 neutron).', 'nuklear'],
        ['Tenaga Nuklear', 'istilah', 'Pembelahan Nuklear (Fission)', 'Pembahagian nukleus berat kepada dua nukleus lebih ringan, membebaskan tenaga yang besar.', null, 'Uranium-235 dibedil neutron → Barium + Krypton + 3 neutron + tenaga (loji janakuasa nuklear).', 'nuklear'],
        ['Tenaga Nuklear', 'istilah', 'Pelakuran Nuklear (Fusion)', 'Penggabungan dua nukleus ringan menjadi nukleus lebih berat, membebaskan tenaga yang sangat besar.', null, 'Matahari mengeluarkan tenaga melalui pelakuran hidrogen menjadi helium.', 'nuklear'],
        ['Tenaga Nuklear', 'istilah', 'Setengah Hayat', 'Masa yang diperlukan untuk separuh daripada bahan radioaktif mereput.', null, 'Karbon-14 mempunyai setengah hayat ~5,730 tahun — digunakan untuk penentuan umur arkeologi.', 'nuklear'],

        // --- T5: Mikroorganisma ---
        ['Mikroorganisma', 'istilah', 'Bakteria', 'Organisma sel tunggal prokariot tanpa nukleus berlapis membran — boleh berfaedah atau membawa penyakit.', null, 'Lactobacillus (yogurt), Salmonella (keracunan makanan).', 'mikroorganisma'],
        ['Mikroorganisma', 'istilah', 'Virus', 'Zarah bukan sel yang hanya boleh membiak dalam sel hos hidup.', null, 'Virus COVID-19 (SARS-CoV-2), virus influenza, virus HIV.', 'mikroorganisma'],
        ['Mikroorganisma', 'istilah', 'Vaksin', 'Persediaan yang mengandungi antigen lemah/mati untuk merangsang badan menghasilkan antibodi tanpa menyebabkan penyakit.', null, 'Vaksin BCG (tuberkulosis), vaksin polio, vaksin COVID-19.', 'mikroorganisma'],

        // --- T5: Kelestarian Alam ---
        ['Kelestarian Alam', 'istilah', 'Kesan Rumah Hijau', 'Pemanasan permukaan bumi akibat gas rumah hijau (CO₂, CH₄, H₂O) yang memerangkap haba di atmosfera.', null, 'Pertambahan CO₂ daripada bahan api fosil meningkatkan suhu global ~1°C sejak 1900.', 'alam_sekitar'],
        ['Kelestarian Alam', 'istilah', 'Hujan Asid', 'Hujan dengan pH kurang dari 5.6 akibat pencemaran SO₂ dan NO₂ di atmosfera.', null, 'Asid sulfurik dan nitrik dalam hujan merosakkan hutan dan bangunan.', 'alam_sekitar'],

        // --- T5: Kadar Tindak Balas ---
        ['Kadar Tindak Balas', 'formula', 'Kadar Tindak Balas', 'Perubahan kepekatan bahan tindak balas atau hasil dalam tempoh masa tertentu.', 'Kadar = Perubahan kuantiti ÷ Masa', 'Kadar penghasilan gas H₂: 50 cm³ dalam 10 saat = 5 cm³/s.', 'kadar'],
        ['Kadar Tindak Balas', 'istilah', 'Mangkin', 'Bahan yang meningkatkan kadar tindak balas tetapi tidak terlibat dalam tindak balas itu sendiri.', null, 'Mangkin V₂O₅ dalam Proses Sentuhan; enzim amilase dalam saliva.', 'kadar'],

        // --- T5: Sebatian Karbon ---
        ['Sebatian Karbon', 'istilah', 'Hidrokarbon', 'Sebatian organik yang hanya mengandungi karbon dan hidrogen.', null, 'Metana (CH₄), petrol (campuran hidrokarbon), gas asli.', 'organik'],
        ['Sebatian Karbon', 'istilah', 'Alkana', 'Siri homolog hidrokarbon tepu dengan formula am CₙH₂ₙ₊₂.', null, 'Metana CH₄, etana C₂H₆, propana C₃H₈, butana C₄H₁₀.', 'organik'],
        ['Sebatian Karbon', 'istilah', 'Alkena', 'Siri homolog hidrokarbon tak tepu dengan satu ikatan ganda dua karbon-karbon (C=C). Formula am CₙH₂ₙ.', null, 'Etena C₂H₄, propena C₃H₆.', 'organik'],
        ['Sebatian Karbon', 'istilah', 'Polimer', 'Molekul besar yang terdiri daripada banyak unit kecil berulang (monomer) yang berikatan.', null, 'Polietilena (dari etena), nilon, kanji (asli).', 'organik'],

        // --- T5: Elektrokimia ---
        ['Elektrokimia', 'istilah', 'Elektrolisis', 'Proses penguraian sebatian ionik (lebur atau larutan) oleh arus elektrik kepada unsur-unsurnya.', null, 'Elektrolisis air masin menghasilkan klorin dan natrium hidroksida.', 'elektrokimia'],
        ['Elektrokimia', 'istilah', 'Sel Voltaik', 'Sel kimia yang menukar tenaga kimia kepada tenaga elektrik melalui tindak balas redoks spontan.', null, 'Bateri kering, bateri kereta, sel Daniell.', 'elektrokimia'],
        ['Elektrokimia', 'istilah', 'Penyaduran', 'Proses melapisi permukaan logam dengan lapisan tipis logam lain melalui elektrolisis.', null, 'Penyaduran emas pada barang kemas, kromium pada bumper kereta.', 'elektrokimia'],

        // --- T5: Cahaya & Optik ---
        ['Cahaya dan Optik', 'formula', 'Hukum Pembiasan (Snell)', 'Nisbah sinus sudut tuju kepada sinus sudut bias adalah malar untuk dua medium yang sama.', 'n = sin θ₁ ÷ sin θ₂', 'Cahaya dari udara ke kaca: n ≈ 1.5.', 'optik'],
        ['Cahaya dan Optik', 'istilah', 'Pantulan Dalam Penuh', 'Berlaku apabila cahaya tuju dari medium tumpat ke medium kurang tumpat melebihi sudut genting — semua cahaya dipantul semula.', null, 'Asas operasi gentian optik dan prisma binokular.', 'optik'],
        ['Cahaya dan Optik', 'formula', 'Persamaan Kanta', 'Hubungan antara jarak objek, imej dan panjang fokus kanta.', '1/f = 1/u + 1/v', 'Kanta dengan f = 10 cm, objek pada 30 cm: 1/v = 1/10 − 1/30 → v = 15 cm.', 'optik'],

        // --- T5: Daya dan Tekanan ---
        ['Daya dan Tekanan', 'formula', 'Tekanan', 'Daya tegak ke atas seunit luas permukaan.', 'P = F ÷ A', 'Daya 100 N pada luas 0.5 m²: P = 200 Pa.', 'tekanan'],
        ['Daya dan Tekanan', 'formula', 'Tekanan dalam Cecair', 'Tekanan pada kedalaman dalam cecair bergantung kepada ketumpatan cecair dan kedalaman.', 'P = ρgh', 'Air (ρ = 1000 kg/m³) pada kedalaman 10 m: P = 1000 × 10 × 10 = 100,000 Pa.', 'tekanan'],
        ['Daya dan Tekanan', 'istilah', 'Prinsip Pascal', 'Tekanan yang dikenakan pada cecair tertutup dipindahkan secara seragam ke semua bahagian.', null, 'Sistem brek kereta dan jek hidraulik beroperasi berdasarkan prinsip ini.', 'tekanan'],

        // --- T5: Teknologi Angkasa ---
        ['Teknologi Angkasa Lepas', 'istilah', 'Halaju Lepas (Escape Velocity)', 'Halaju minimum yang diperlukan untuk objek meninggalkan medan graviti planet.', null, 'Halaju lepas Bumi ≈ 11.2 km/s; Bulan ~2.4 km/s.', 'angkasa'],
        ['Teknologi Angkasa Lepas', 'istilah', 'Satelit Geostatik', 'Satelit yang mengorbit Bumi pada ketinggian ~36,000 km dengan tempoh putaran 24 jam — sentiasa berada di atas titik yang sama.', null, 'Satelit komunikasi MEASAT, satelit cuaca.', 'angkasa'],
        ['Teknologi Angkasa Lepas', 'istilah', 'Sheikh Muszaphar Shukor', 'Angkasawan Malaysia pertama yang ke Stesen Angkasa Antarabangsa (ISS) pada Oktober 2007.', 'Malaysia', '2007', 'Menjalankan eksperimen sel kanser dan mikroorganisma dalam graviti sifar.', 'angkasa'],
    ];
}

function sains_kssm_run(): array
{
    $subject = db_one("SELECT id FROM subjects WHERE slug = 'sains' LIMIT 1");
    if (!$subject) {
        return ['status' => 'no_subject', 'message' => 'Sains subject not found. Run install / seed_subjects first.'];
    }
    $sid = (int) $subject['id'];

    $conceptsTable = (bool) db_one("SELECT 1 AS x FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'sains_concepts'");

    $catalog = sains_kssm_catalog();
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
        foreach (sains_kssm_concepts() as $idx => [$topicName, $type, $name, $def, $formula, $example, $category]) {
            $tid = $topicIds[$topicName] ?? null;
            $existing = db_one('SELECT id FROM sains_concepts WHERE name = ? LIMIT 1', [$name]);
            if ($existing) {
                $conceptsKept++;
            } else {
                db_exec(
                    'INSERT INTO sains_concepts (topic_id, topic_label, type, name, definition, formula, example, category, sort_order)
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
    $r = sains_kssm_run();
    if (($r['status'] ?? '') !== 'ok') {
        fwrite(STDERR, ($r['message'] ?? 'unknown error') . "\n");
        exit(1);
    }
    echo "KSSM Sains seeded —\n";
    echo "  Bab:        created {$r['topics_created']}, adopted {$r['topics_adopted']}, kept {$r['topics_kept']} (catalog {$r['catalog_topics']})\n";
    echo "  Subtopik:   created {$r['subtopics_created']}, kept {$r['subtopics_kept']}\n";
    echo "  Kemahiran:  created {$r['skills_created']}, kept {$r['skills_kept']}\n";
    if ($r['concepts_table']) {
        echo "  Konsep:     created {$r['concepts_created']}, kept {$r['concepts_kept']}\n";
    } else {
        echo "  Konsep:     table missing — run database migrations to enable.\n";
    }
}
