<?php
/**
 * KSSM SPM Sains Komputer syllabus seeder. Tingkatan 4 (13 sub-topik
 * dalam 3 bidang — Pengaturcaraan, Pangkalan Data, Interaksi Manusia
 * dan Komputer) + Tingkatan 5 (8 sub-topik dalam 3 bab —
 * Pengkomputeran, Pangkalan Data Lanjutan, Pengaturcaraan Web).
 * Plus starter bank konsep + code snippets (Python, SQL, HTML/CSS/JS,
 * pseudokod).
 *
 * Idempotent: topik matched by (subject_id, name, form_level); legacy
 * same-named NULL-form topik are adopted.
 *
 *   php public_html/cron/seed_sains_komputer_kssm.php
 */
declare(strict_types=1);

require_once __DIR__ . '/../inc/db.php';

function sains_komputer_kssm_catalog(): array
{
    return [
        // ============= TINGKATAN 4 =============
        // Bidang 1: Pengaturcaraan
        ['form' => 4, 'name' => 'Strategi Penyelesaian Masalah', 'subtopics' => [
            'Analisis Masalah (IPO)',
            'Carta Aliran (Flowchart)',
            'Pseudokod',
            'Pengujian dan Penyahpepijatan',
        ], 'skills' => [
            ['Menganalisis masalah menggunakan IPO (Input-Process-Output)', 'medium'],
            ['Melukis carta aliran', 'medium'],
            ['Menulis pseudokod', 'medium'],
        ]],
        ['form' => 4, 'name' => 'Algoritma', 'subtopics' => [
            'Konsep Algoritma',
            'Algoritma Mencari (Linear, Binary)',
            'Algoritma Mengisih (Bubble, Selection)',
            'Kecekapan Algoritma',
        ], 'skills' => [
            ['Menerangkan konsep algoritma', 'easy'],
            ['Mengaplikasikan algoritma mencari dan mengisih', 'hard'],
            ['Menilai kecekapan algoritma', 'hard'],
        ]],
        ['form' => 4, 'name' => 'Pemboleh Ubah, Pemalar dan Jenis Data', 'subtopics' => [
            'Pemboleh Ubah (Variable)',
            'Pemalar (Constant)',
            'Jenis Data (Integer, Float, String, Boolean, Char)',
            'Pernyataan Umpukan (Assignment Statement)',
        ], 'skills' => [
            ['Membezakan pemboleh ubah dan pemalar', 'easy'],
            ['Mengenal pasti jenis data yang sesuai', 'medium'],
            ['Menulis pernyataan umpukan', 'medium'],
        ]],
        ['form' => 4, 'name' => 'Struktur Kawalan', 'subtopics' => [
            'Struktur Jujukan (Sequence)',
            'Struktur Pilihan (Selection — if, if-else, switch-case)',
            'Struktur Ulangan (Loop — for, while, do-while)',
            'Struktur Bersarang (Nested Structures)',
        ], 'skills' => [
            ['Menulis kod menggunakan struktur jujukan', 'medium'],
            ['Mengaplikasikan struktur pilihan dan ulangan', 'hard'],
            ['Menyelesaikan masalah dengan struktur bersarang', 'hard'],
        ]],
        ['form' => 4, 'name' => 'Amalan Terbaik Pengaturcaraan', 'subtopics' => [
            'Penamaan Pemboleh Ubah yang Bermakna',
            'Komen dan Dokumentasi',
            'Indentasi dan Format Kod',
            'Pengujian dan Penyelenggaraan Kod',
        ], 'skills' => [
            ['Mengamalkan penamaan pemboleh ubah yang baik', 'easy'],
            ['Menulis komen yang berguna', 'easy'],
            ['Memformat kod dengan indentasi yang konsisten', 'medium'],
        ]],
        ['form' => 4, 'name' => 'Modular dan Struktur Data', 'subtopics' => [
            'Fungsi dan Prosedur',
            'Parameter dan Argumen',
            'Pulangan Nilai (Return Value)',
            'Tatasusunan (Array) Satu Dimensi',
        ], 'skills' => [
            ['Menulis fungsi dengan parameter', 'medium'],
            ['Menggunakan tatasusunan satu dimensi', 'medium'],
            ['Memecah masalah kepada modul-modul', 'hard'],
        ]],
        ['form' => 4, 'name' => 'Pembangunan Aplikasi', 'subtopics' => [
            'Kitar Hayat Pembangunan Sistem',
            'Analisis Keperluan',
            'Reka Bentuk Aplikasi',
            'Implementasi dan Pengujian',
            'Dokumentasi Sistem',
        ], 'skills' => [
            ['Mengikut kitar hayat pembangunan sistem', 'medium'],
            ['Menganalisis keperluan pengguna', 'medium'],
            ['Membangunkan aplikasi mudah', 'hard'],
        ]],
        // Bidang 2: Pangkalan Data
        ['form' => 4, 'name' => 'Pangkalan Data Hubungan', 'subtopics' => [
            'Konsep Pangkalan Data',
            'Sistem Pengurusan Pangkalan Data (DBMS)',
            'Jadual, Rekod dan Medan',
            'Kunci Utama dan Kunci Asing',
            'Hubungan Antara Jadual',
        ], 'skills' => [
            ['Menerangkan konsep pangkalan data hubungan', 'medium'],
            ['Mengenal pasti kunci utama dan kunci asing', 'medium'],
            ['Menjelaskan hubungan antara jadual', 'medium'],
        ]],
        ['form' => 4, 'name' => 'Reka Bentuk Pangkalan Data Hubungan', 'subtopics' => [
            'Model Hubungan Entiti (ERD)',
            'Penormalan Data (Normalisation)',
            'Bentuk Normal 1NF, 2NF, 3NF',
            'Reka Bentuk Skema Pangkalan Data',
        ], 'skills' => [
            ['Melukis Model Hubungan Entiti (ERD)', 'hard'],
            ['Mengaplikasikan penormalan data', 'hard'],
            ['Mereka bentuk skema pangkalan data', 'hard'],
        ]],
        ['form' => 4, 'name' => 'Pembangunan Pangkalan Data Hubungan', 'subtopics' => [
            'Membina Jadual',
            'Memasukkan, Mengubah dan Memadam Rekod',
            'Borang (Forms)',
            'Pertanyaan (Queries)',
            'Laporan (Reports)',
        ], 'skills' => [
            ['Membina jadual dalam DBMS', 'medium'],
            ['Membuat pertanyaan asas', 'medium'],
            ['Menjana laporan dari pangkalan data', 'medium'],
        ]],
        ['form' => 4, 'name' => 'Pembangunan Sistem Pangkalan Data', 'subtopics' => [
            'Analisis Keperluan Sistem',
            'Reka Bentuk Sistem',
            'Implementasi Pangkalan Data',
            'Pengujian Sistem',
            'Dokumentasi Pengguna',
        ], 'skills' => [
            ['Membangunkan sistem pangkalan data mudah', 'hard'],
            ['Menguji integriti data', 'medium'],
            ['Mendokumentasikan sistem', 'medium'],
        ]],
        // Bidang 3: Interaksi Manusia dan Komputer
        ['form' => 4, 'name' => 'Reka Bentuk Interaksi', 'subtopics' => [
            'Konsep Interaksi Manusia dan Komputer (HCI)',
            'Prinsip Reka Bentuk Antara Muka',
            'Faktor Pengguna',
            'Kebolehgunaan (Usability)',
        ], 'skills' => [
            ['Menerangkan prinsip HCI', 'medium'],
            ['Menilai kebolehgunaan antara muka', 'medium'],
        ]],
        ['form' => 4, 'name' => 'Paparan dan Reka Bentuk Skrin', 'subtopics' => [
            'Reka Letak (Layout)',
            'Tipografi dan Warna',
            'Navigasi dan Aliran',
            'Maklum Balas (Feedback) Pengguna',
        ], 'skills' => [
            ['Mereka bentuk paparan skrin yang berkesan', 'medium'],
            ['Mengaplikasikan prinsip tipografi dan warna', 'medium'],
        ]],

        // ============= TINGKATAN 5 =============
        // Bab 1: Pengkomputeran
        ['form' => 5, 'name' => 'Komputer dan Impak', 'subtopics' => [
            'Sejarah dan Generasi Komputer',
            'Jenis Komputer',
            'Impak Komputer terhadap Masyarakat',
            'Etika Penggunaan Komputer',
            'Jenayah Siber dan Pencegahan',
        ], 'skills' => [
            ['Menjelaskan sejarah dan generasi komputer', 'easy'],
            ['Menilai impak komputer terhadap masyarakat', 'medium'],
            ['Menganalisis isu etika dan jenayah siber', 'medium'],
        ]],
        ['form' => 5, 'name' => 'Seni Bina Komputer', 'subtopics' => [
            'Komponen Perkakasan (CPU, RAM, Storan)',
            'Sistem Pengoperasian',
            'Sistem Nombor (Binari, Heksadesimal)',
            'Penukaran Sistem Nombor',
            'Pengekodan ASCII dan Unicode',
        ], 'skills' => [
            ['Mengenal pasti komponen perkakasan komputer', 'easy'],
            ['Menukar sistem nombor binari/desimal/heksadesimal', 'medium'],
            ['Menjelaskan pengekodan ASCII dan Unicode', 'medium'],
        ]],
        ['form' => 5, 'name' => 'Get Logik', 'subtopics' => [
            'Get AND, OR, NOT',
            'Get NAND, NOR, XOR, XNOR',
            'Jadual Kebenaran',
            'Litar Logik Gabungan',
            'Algebra Boolean',
        ], 'skills' => [
            ['Mengenal pasti jenis get logik', 'easy'],
            ['Membina jadual kebenaran', 'medium'],
            ['Mereka bentuk litar logik gabungan', 'hard'],
        ]],
        ['form' => 5, 'name' => 'Masa Hadapan Pengkomputeran', 'subtopics' => [
            'Pengkomputeran Awan (Cloud Computing)',
            'Pengkomputeran Mudah Alih',
            'Kepintaran Buatan (AI) dan Pembelajaran Mesin',
            'Keselamatan Data dan Penyulitan',
            'Kerjaya dalam Pengkomputeran',
        ], 'skills' => [
            ['Menerangkan teknologi pengkomputeran terkini', 'medium'],
            ['Menilai keselamatan data dan penyulitan', 'medium'],
            ['Mengenal pasti peluang kerjaya dalam pengkomputeran', 'easy'],
        ]],
        // Bab 2: Pangkalan Data Lanjutan
        ['form' => 5, 'name' => 'Pangkalan Data Lanjutan (SQL)', 'subtopics' => [
            'Pengenalan kepada SQL',
            'Pernyataan DDL (CREATE, ALTER, DROP)',
            'Pernyataan DML (INSERT, UPDATE, DELETE)',
            'Pernyataan SELECT dan Klausa WHERE',
            'JOIN, GROUP BY, ORDER BY',
            'Subquery dan Aggregate Functions',
        ], 'skills' => [
            ['Menulis pernyataan SQL asas (CREATE, INSERT, SELECT)', 'medium'],
            ['Menggunakan klausa WHERE, ORDER BY, GROUP BY', 'hard'],
            ['Menulis pertanyaan JOIN dan subquery', 'hard'],
        ]],
        // Bab 3: Pengaturcaraan Berasaskan Web
        ['form' => 5, 'name' => 'Bahasa Penskripan Klien', 'subtopics' => [
            'HTML — Struktur Dokumen Web',
            'HTML — Tag, Atribut dan Borang',
            'CSS — Pemilih, Pengisytiharan dan Box Model',
            'CSS — Layout (Flexbox, Grid)',
            'JavaScript — Pemboleh Ubah, Fungsi, DOM',
            'JavaScript — Acara (Events)',
        ], 'skills' => [
            ['Menulis dokumen HTML asas', 'medium'],
            ['Mengaplikasikan CSS untuk reka bentuk', 'medium'],
            ['Menulis kod JavaScript untuk interaktiviti', 'hard'],
        ]],
        ['form' => 5, 'name' => 'Bahasa Penskripan Pelayan', 'subtopics' => [
            'Pengenalan kepada PHP',
            'Pemboleh Ubah dan Pernyataan PHP',
            'Borang dan Pemprosesan Data ($_POST, $_GET)',
            'Sambungan ke Pangkalan Data',
            'Sesi dan Kuki',
        ], 'skills' => [
            ['Menulis skrip PHP asas', 'medium'],
            ['Memproses data borang menggunakan PHP', 'hard'],
            ['Menyambung PHP ke pangkalan data MySQL', 'hard'],
        ]],
        ['form' => 5, 'name' => 'Laman Web Interaktif', 'subtopics' => [
            'Integrasi HTML, CSS, JavaScript, PHP',
            'Pengesahan Borang (Form Validation)',
            'Pengurusan Sesi Pengguna',
            'Keselamatan Web Asas',
            'Pembangunan Projek Laman Web Interaktif',
        ], 'skills' => [
            ['Membangunkan laman web interaktif penuh', 'hard'],
            ['Mengaplikasikan pengesahan borang sebelah klien dan pelayan', 'hard'],
            ['Menjalankan praktis keselamatan web asas', 'medium'],
        ]],
    ];
}

/** Starter Sains Komputer concepts bank (istilah + code snippets). */
function sains_komputer_kssm_concepts(): array
{
    return [
        // topic, type, name, definition, code_snippet, language, example, category

        // --- Pengaturcaraan asas ---
        ['Strategi Penyelesaian Masalah', 'istilah', 'IPO (Input-Process-Output)',
         'Model analisis masalah yang membahagikan masalah kepada tiga bahagian: input (data masuk), process (langkah pemprosesan), output (hasil).',
         null, null, 'Masalah: Jumlah dua nombor → Input: a, b; Process: jumlah = a + b; Output: jumlah.', 'analisis'],

        ['Strategi Penyelesaian Masalah', 'pseudokod', 'Pseudokod Mencari Maksimum',
         'Algoritma untuk mencari nilai maksimum dalam senarai nombor.',
         "MULA\n  maks ← senarai[0]\n  UNTUK setiap nombor DALAM senarai\n    JIKA nombor > maks MAKA\n      maks ← nombor\n    TAMAT JIKA\n  TAMAT UNTUK\n  PAPAR maks\nTAMAT",
         'pseudocode', 'Senarai = [3, 7, 1, 9, 4] → Output: 9', 'pseudokod'],

        ['Algoritma', 'istilah', 'Algoritma Binary Search',
         'Algoritma mencari yang membahagi senarai berurutan separuh-separuh untuk mencari item dengan kecekapan O(log n).',
         null, null, 'Mencari nombor 7 dalam [1,3,5,7,9,11,13] — semak tengah, kemudian separuh kiri/kanan.', 'algoritma'],

        ['Algoritma', 'pseudokod', 'Bubble Sort',
         'Algoritma mengisih dengan menukar nombor bersebelahan yang tidak teratur, berulang sehingga senarai diisih.',
         "UNTUK i DARI 0 HINGGA n-1\n  UNTUK j DARI 0 HINGGA n-i-2\n    JIKA arr[j] > arr[j+1] MAKA\n      tukar arr[j] dan arr[j+1]\n    TAMAT JIKA\n  TAMAT UNTUK\nTAMAT UNTUK",
         'pseudocode', '[5,2,4,1,3] → [1,2,3,4,5] selepas isihan menaik', 'algoritma'],

        // --- Pemboleh ubah / data types ---
        ['Pemboleh Ubah, Pemalar dan Jenis Data', 'syntax', 'Python — Pernyataan Umpukan',
         'Memberikan nilai kepada pemboleh ubah dalam Python.',
         "umur = 17\nnama = \"Ali\"\nharga = 2.50\nadalah_pelajar = True",
         'python', 'Mencipta empat pemboleh ubah dengan jenis data berbeza (int, str, float, bool).', 'syntax'],

        ['Pemboleh Ubah, Pemalar dan Jenis Data', 'istilah', 'Pemboleh Ubah (Variable)',
         'Lokasi memori bernama yang menyimpan nilai yang boleh berubah semasa pelaksanaan program.',
         null, null, 'umur = 17 (boleh diubah menjadi umur = 18 kemudian).', 'asas'],

        ['Pemboleh Ubah, Pemalar dan Jenis Data', 'istilah', 'Pemalar (Constant)',
         'Pemboleh ubah yang nilainya tidak boleh diubah setelah ditetapkan.',
         null, null, 'PI = 3.14159 — tidak boleh ditukar.', 'asas'],

        // --- Struktur kawalan ---
        ['Struktur Kawalan', 'syntax', 'Python — If-Else',
         'Struktur pilihan untuk membuat keputusan berdasarkan syarat.',
         "if markah >= 80:\n    print(\"Cemerlang\")\nelif markah >= 60:\n    print(\"Lulus\")\nelse:\n    print(\"Gagal\")",
         'python', 'Menentukan gred berdasarkan markah pelajar.', 'kawalan'],

        ['Struktur Kawalan', 'syntax', 'Python — For Loop',
         'Struktur ulangan yang melaksanakan blok kod untuk setiap item dalam senarai/julat.',
         "for i in range(1, 11):\n    print(i)\n\n# atau ulangan ke atas senarai\nbuah = [\"epal\", \"pisang\", \"oren\"]\nfor b in buah:\n    print(b)",
         'python', 'Cetak nombor 1 hingga 10, atau cetak setiap buah dalam senarai.', 'kawalan'],

        ['Struktur Kawalan', 'syntax', 'Python — While Loop',
         'Struktur ulangan yang melaksanakan blok kod selagi syarat adalah benar.',
         "i = 1\nwhile i <= 5:\n    print(i)\n    i = i + 1",
         'python', 'Cetak nombor 1 hingga 5 menggunakan ulangan while.', 'kawalan'],

        // --- Modular / functions ---
        ['Modular dan Struktur Data', 'syntax', 'Python — Fungsi dengan Parameter',
         'Mendefinisikan fungsi yang menerima parameter dan memulangkan nilai.',
         "def kira_luas(panjang, lebar):\n    luas = panjang * lebar\n    return luas\n\nhasil = kira_luas(5, 3)\nprint(hasil)  # Output: 15",
         'python', 'Fungsi untuk mengira luas segi empat tepat.', 'fungsi'],

        ['Modular dan Struktur Data', 'syntax', 'Python — Tatasusunan (List)',
         'Struktur data untuk menyimpan koleksi item bersiri.',
         "nombor = [10, 20, 30, 40, 50]\nprint(nombor[0])      # 10 (item pertama)\nprint(nombor[-1])     # 50 (item terakhir)\nnombor.append(60)     # tambah item baharu\nprint(len(nombor))    # 6",
         'python', 'Membuat list, mengakses item, menambah dan mengira saiz.', 'struktur_data'],

        // --- Pangkalan Data ---
        ['Pangkalan Data Hubungan', 'istilah', 'Kunci Utama (Primary Key)',
         'Medan dalam jadual yang mengenal pasti setiap rekod secara unik dan tidak boleh NULL.',
         null, null, 'Dalam jadual Pelajar: id_pelajar (1, 2, 3, ...) adalah kunci utama.', 'pangkalan_data'],

        ['Pangkalan Data Hubungan', 'istilah', 'Kunci Asing (Foreign Key)',
         'Medan dalam satu jadual yang merujuk kepada kunci utama jadual lain, mewujudkan hubungan antara jadual.',
         null, null, 'Dalam jadual Pinjaman: id_pelajar adalah kunci asing yang merujuk Pelajar.id_pelajar.', 'pangkalan_data'],

        ['Reka Bentuk Pangkalan Data Hubungan', 'istilah', 'Normalisasi 1NF',
         'Bentuk normal pertama: setiap medan mengandungi satu nilai atomik sahaja (tiada nilai berulang dalam satu sel).',
         null, null, 'Pisahkan medan "no_telefon = 012-345,013-678" kepada dua rekod berasingan.', 'pangkalan_data'],

        ['Reka Bentuk Pangkalan Data Hubungan', 'istilah', 'Normalisasi 3NF',
         'Bentuk normal ketiga: setiap medan bukan-kunci mestilah bergantung sepenuhnya kepada kunci utama sahaja, bukan kepada medan bukan-kunci lain.',
         null, null, 'Buang medan jabatan_alamat dari jadual Pekerja kerana ia bergantung pada jabatan_id, bukan pekerja_id.', 'pangkalan_data'],

        // --- SQL ---
        ['Pangkalan Data Lanjutan (SQL)', 'syntax', 'SQL — CREATE TABLE',
         'Mencipta jadual baharu dengan medan dan jenis data yang ditakrifkan.',
         "CREATE TABLE pelajar (\n    id_pelajar INT PRIMARY KEY,\n    nama VARCHAR(100) NOT NULL,\n    umur INT,\n    kelas VARCHAR(20)\n);",
         'sql', 'Mencipta jadual pelajar dengan 4 medan.', 'sql'],

        ['Pangkalan Data Lanjutan (SQL)', 'syntax', 'SQL — INSERT INTO',
         'Memasukkan rekod baharu ke dalam jadual.',
         "INSERT INTO pelajar (id_pelajar, nama, umur, kelas)\nVALUES (1, 'Ahmad', 16, '4 Bestari');",
         'sql', 'Tambah satu pelajar baharu ke dalam jadual.', 'sql'],

        ['Pangkalan Data Lanjutan (SQL)', 'syntax', 'SQL — SELECT dengan WHERE',
         'Mendapatkan rekod yang memenuhi syarat tertentu.',
         "SELECT nama, umur\nFROM pelajar\nWHERE kelas = '4 Bestari'\nORDER BY umur DESC;",
         'sql', 'Mendapatkan nama dan umur semua pelajar dari kelas 4 Bestari, diisih dari termuda dahulu.', 'sql'],

        ['Pangkalan Data Lanjutan (SQL)', 'syntax', 'SQL — UPDATE',
         'Mengubah suai rekod sedia ada dalam jadual.',
         "UPDATE pelajar\nSET kelas = '5 Bestari'\nWHERE id_pelajar = 1;",
         'sql', 'Menukar kelas pelajar dengan id 1.', 'sql'],

        ['Pangkalan Data Lanjutan (SQL)', 'syntax', 'SQL — DELETE',
         'Memadam rekod yang memenuhi syarat dari jadual.',
         "DELETE FROM pelajar\nWHERE id_pelajar = 1;",
         'sql', 'Memadam pelajar dengan id 1 dari jadual.', 'sql'],

        ['Pangkalan Data Lanjutan (SQL)', 'syntax', 'SQL — JOIN',
         'Menggabungkan rekod dari dua jadual berdasarkan medan yang berkaitan.',
         "SELECT p.nama, k.nama_kelas\nFROM pelajar p\nINNER JOIN kelas k ON p.id_kelas = k.id_kelas;",
         'sql', 'Mendapatkan nama pelajar dengan nama kelas yang berkaitan.', 'sql'],

        ['Pangkalan Data Lanjutan (SQL)', 'syntax', 'SQL — GROUP BY dengan Aggregate',
         'Mengumpulkan rekod dan mengaplikasikan fungsi agregat (COUNT, SUM, AVG, MIN, MAX).',
         "SELECT kelas, COUNT(*) AS bilangan_pelajar, AVG(umur) AS purata_umur\nFROM pelajar\nGROUP BY kelas;",
         'sql', 'Mengira bilangan pelajar dan purata umur untuk setiap kelas.', 'sql'],

        // --- Pengkomputeran ---
        ['Seni Bina Komputer', 'istilah', 'CPU (Central Processing Unit)',
         'Otak komputer yang melaksanakan arahan program. Mengandungi unit kawalan (CU) dan unit aritmetik/logik (ALU).',
         null, null, 'Intel Core i7, AMD Ryzen 5, Apple M2.', 'perkakasan'],

        ['Seni Bina Komputer', 'istilah', 'RAM (Random Access Memory)',
         'Ingatan utama komputer yang menyimpan data dan arahan yang sedang digunakan; bersifat tidak meruap (volatile) — kandungan hilang apabila kuasa dimatikan.',
         null, null, 'DDR4 8GB, DDR5 16GB.', 'perkakasan'],

        ['Seni Bina Komputer', 'syntax', 'Penukaran Binari ke Desimal',
         'Setiap digit binari didarab dengan kuasa 2 mengikut kedudukannya, kemudian semua hasil dijumlahkan.',
         "1101₂ = (1×2³) + (1×2²) + (0×2¹) + (1×2⁰)\n      = 8 + 4 + 0 + 1\n      = 13₁₀",
         null, '1101 binari = 13 desimal', 'sistem_nombor'],

        ['Seni Bina Komputer', 'syntax', 'Penukaran Desimal ke Binari',
         'Bahagi nombor desimal dengan 2 berulang kali, kumpul bakinya dari bawah ke atas.',
         "25 ÷ 2 = 12 baki 1\n12 ÷ 2 = 6  baki 0\n 6 ÷ 2 = 3  baki 0\n 3 ÷ 2 = 1  baki 1\n 1 ÷ 2 = 0  baki 1\n\n25₁₀ = 11001₂",
         null, '25 desimal = 11001 binari', 'sistem_nombor'],

        ['Get Logik', 'istilah', 'Get AND',
         'Get logik yang menghasilkan output 1 hanya apabila SEMUA inputnya adalah 1.',
         "A | B | A AND B\n0 | 0 | 0\n0 | 1 | 0\n1 | 0 | 0\n1 | 1 | 1",
         null, 'Jadual kebenaran get AND dengan 2 input.', 'get_logik'],

        ['Get Logik', 'istilah', 'Get OR',
         'Get logik yang menghasilkan output 1 apabila SEKURANG-KURANGNYA satu inputnya adalah 1.',
         "A | B | A OR B\n0 | 0 | 0\n0 | 1 | 1\n1 | 0 | 1\n1 | 1 | 1",
         null, 'Jadual kebenaran get OR dengan 2 input.', 'get_logik'],

        ['Get Logik', 'istilah', 'Get NOT',
         'Get logik songsang (inverter) yang menukar 1 kepada 0 dan sebaliknya.',
         "A | NOT A\n0 |   1\n1 |   0",
         null, 'Jadual kebenaran get NOT.', 'get_logik'],

        ['Get Logik', 'istilah', 'Get XOR',
         'Get logik exclusive OR — output 1 apabila inputnya BERBEZA.',
         "A | B | A XOR B\n0 | 0 |   0\n0 | 1 |   1\n1 | 0 |   1\n1 | 1 |   0",
         null, 'Jadual kebenaran get XOR.', 'get_logik'],

        ['Masa Hadapan Pengkomputeran', 'istilah', 'Penyulitan (Encryption)',
         'Proses menukar maklumat kepada bentuk kod yang tidak boleh dibaca untuk melindunginya daripada capaian tidak dibenarkan.',
         null, null, 'HTTPS menggunakan TLS untuk menyulitkan trafik web; AES-256 untuk fail.', 'keselamatan'],

        ['Masa Hadapan Pengkomputeran', 'istilah', 'Pengkomputeran Awan (Cloud Computing)',
         'Penyampaian perkhidmatan pengkomputeran (storan, kuasa pemprosesan, perisian) melalui Internet.',
         null, null, 'AWS, Google Cloud, Microsoft Azure, Dropbox.', 'pengkomputeran'],

        // --- Web ---
        ['Bahasa Penskripan Klien', 'syntax', 'HTML — Struktur Dokumen',
         'Struktur asas dokumen HTML5.',
         "<!DOCTYPE html>\n<html>\n<head>\n    <title>Tajuk Halaman</title>\n</head>\n<body>\n    <h1>Selamat Datang</h1>\n    <p>Ini adalah perenggan pertama.</p>\n</body>\n</html>",
         'html', 'Dokumen HTML asas dengan tajuk dan satu perenggan.', 'web_klien'],

        ['Bahasa Penskripan Klien', 'syntax', 'HTML — Borang',
         'Borang HTML untuk menerima input pengguna.',
         "<form method=\"POST\" action=\"proses.php\">\n    <label>Nama: <input type=\"text\" name=\"nama\" required></label>\n    <label>Umur: <input type=\"number\" name=\"umur\" min=\"1\"></label>\n    <button type=\"submit\">Hantar</button>\n</form>",
         'html', 'Borang dengan input teks dan nombor.', 'web_klien'],

        ['Bahasa Penskripan Klien', 'syntax', 'CSS — Pemilih Asas',
         'Pemilih CSS untuk menggayakan elemen HTML.',
         "body { font-family: Arial; background: #f5f5f5; }\nh1 { color: #4527a0; text-align: center; }\n.btn { padding: 8px 16px; background: #6a3de8; color: white; border-radius: 6px; }\n#main { max-width: 800px; margin: auto; }",
         'css', 'Menggayakan body, h1, kelas .btn, dan id #main.', 'web_klien'],

        ['Bahasa Penskripan Klien', 'syntax', 'JavaScript — Fungsi dan DOM',
         'Manipulasi DOM dengan JavaScript untuk interaktiviti.',
         "function ucapSalam() {\n    const nama = document.getElementById('nama').value;\n    document.getElementById('output').textContent = 'Hai, ' + nama + '!';\n}\n\ndocument.getElementById('btnSalam').addEventListener('click', ucapSalam);",
         'javascript', 'Mengambil nilai input dan memaparkan ucapan apabila butang diklik.', 'web_klien'],

        // --- PHP ---
        ['Bahasa Penskripan Pelayan', 'syntax', 'PHP — Pemboleh Ubah dan Echo',
         'Pemboleh ubah PHP bermula dengan tanda $; echo digunakan untuk memaparkan output.',
         "<?php\n\$nama = \"Ali\";\n\$umur = 17;\necho \"Hai \$nama, anda berumur \$umur tahun.\";\n?>",
         'php', 'Mencipta dua pemboleh ubah dan memaparkan ayat.', 'web_pelayan'],

        ['Bahasa Penskripan Pelayan', 'syntax', 'PHP — Memproses Borang ($_POST)',
         'Mengambil data yang dihantar dari borang HTML melalui kaedah POST.',
         "<?php\nif (\$_SERVER['REQUEST_METHOD'] === 'POST') {\n    \$nama = \$_POST['nama'];\n    \$umur = (int) \$_POST['umur'];\n    echo \"Nama: \$nama, Umur: \$umur\";\n}\n?>",
         'php', 'Memproses borang HTML yang dihantar melalui POST.', 'web_pelayan'],

        ['Bahasa Penskripan Pelayan', 'syntax', 'PHP — Sambungan MySQL',
         'Menyambung ke pangkalan data MySQL menggunakan PDO.',
         "<?php\n\$dsn = 'mysql:host=localhost;dbname=sekolah;charset=utf8mb4';\n\$pdo = new PDO(\$dsn, 'root', '', [\n    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,\n]);\n\n\$stmt = \$pdo->prepare('SELECT * FROM pelajar WHERE kelas = ?');\n\$stmt->execute(['4 Bestari']);\n\$rekod = \$stmt->fetchAll(PDO::FETCH_ASSOC);\n?>",
         'php', 'Sambungan ke MySQL dan pertanyaan dengan parameter terikat (selamat daripada SQL injection).', 'web_pelayan'],

        ['Laman Web Interaktif', 'istilah', 'SQL Injection',
         'Serangan keselamatan di mana input pengguna jahat digunakan untuk mengubah pertanyaan SQL dan mendapat akses data tidak dibenarkan.',
         null, null, 'Pencegahan: gunakan prepared statements (PDO bind parameter) dan jangan sambung input ke dalam SQL secara langsung.', 'keselamatan_web'],

        ['Laman Web Interaktif', 'istilah', 'XSS (Cross-Site Scripting)',
         'Serangan di mana penyerang menyuntik skrip jahat ke dalam laman web yang akan dilaksanakan dalam pelayar pengguna lain.',
         null, null, 'Pencegahan: escape output (htmlspecialchars), validate input, gunakan Content Security Policy.', 'keselamatan_web'],
    ];
}

function sains_komputer_kssm_run(): array
{
    $subject = db_one("SELECT id FROM subjects WHERE slug = 'sains-komputer' LIMIT 1");
    if (!$subject) {
        return ['status' => 'no_subject', 'message' => 'Sains Komputer subject not found. Run install / seed_subjects first.'];
    }
    $sid = (int) $subject['id'];

    $conceptsTable = (bool) db_one("SELECT 1 AS x FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'cs_concepts'");

    $catalog = sains_komputer_kssm_catalog();
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
        foreach (sains_komputer_kssm_concepts() as $idx => [$topicName, $type, $name, $def, $code, $lang, $example, $category]) {
            $tid = $topicIds[$topicName] ?? null;
            $existing = db_one('SELECT id FROM cs_concepts WHERE name = ? LIMIT 1', [$name]);
            if ($existing) {
                $conceptsKept++;
            } else {
                db_exec(
                    'INSERT INTO cs_concepts (topic_id, topic_label, type, name, definition, code_snippet, language, example, category, sort_order)
                     VALUES (?,?,?,?,?,?,?,?,?,?)',
                    [$tid, $topicName, $type, $name, $def, $code, $lang, $example, $category, $idx + 1]
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
    $r = sains_komputer_kssm_run();
    if (($r['status'] ?? '') !== 'ok') {
        fwrite(STDERR, ($r['message'] ?? 'unknown error') . "\n");
        exit(1);
    }
    echo "KSSM Sains Komputer seeded —\n";
    echo "  Topik:     created {$r['topics_created']}, adopted {$r['topics_adopted']}, kept {$r['topics_kept']} (catalog {$r['catalog_topics']})\n";
    echo "  Subtopik:  created {$r['subtopics_created']}, kept {$r['subtopics_kept']}\n";
    echo "  Kemahiran: created {$r['skills_created']}, kept {$r['skills_kept']}\n";
    if ($r['concepts_table']) {
        echo "  Konsep + code: created {$r['concepts_created']}, kept {$r['concepts_kept']}\n";
    } else {
        echo "  Konsep + code: table missing — run database migrations to enable.\n";
    }
}
