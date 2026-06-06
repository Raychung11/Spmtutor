<?php
/**
 * KSSM SPM Pendidikan Islam syllabus seeder. Tingkatan 4 + Tingkatan 5
 * organised by the six bidang DSKP — Al-Quran, Hadis, Akidah, Fiqah,
 * Sirah dan Tamadun Islam, Akhlak Islamiah — with their pelajaran
 * (lessons) as subtopik and a starter ayat / hadis / doa reference
 * bank used by the AI Pendidikan Islam Trainer.
 *
 * Idempotent: topics matched by (subject_id, name, form_level); legacy
 * same-named NULL-form topics are adopted.
 *
 *   php public_html/cron/seed_pendidikan_islam_kssm.php
 */
declare(strict_types=1);

require_once __DIR__ . '/../inc/db.php';

function pendidikan_islam_kssm_catalog(): array
{
    return [
        // ============= TINGKATAN 4 =============
        ['form' => 4, 'name' => 'Al-Quran (Tingkatan 4)', 'subtopics' => [
            'Surah Al-An\'am: 162-163 (Ibadah ikhlas untuk Allah)',
            'Surah Al-Kahfi: 101-110 (Balasan amal soleh)',
            'Hukum Tajwid: Mad Silah, Mad Lazim dan Qalqalah',
            'Surah Al-Baqarah: 188 (Larangan Rasuah)',
            'Memahami Sunnatullah (Ali-Imran: 137-139)',
            'Larangan Mempersendakan Agama (Al-An\'am: 70)',
            'Dakwah Pemacu Kemajuan (Fussilat: 33)',
        ], 'skills' => [
            ['Membaca ayat dengan tajwid yang betul', 'medium'],
            ['Menerangkan kandungan ayat', 'medium'],
            ['Mengaplikasikan pengajaran ayat dalam kehidupan', 'medium'],
            ['Mengenal pasti hukum tajwid (mad, qalqalah)', 'medium'],
        ]],
        ['form' => 4, 'name' => 'Hadis (Tingkatan 4)', 'subtopics' => [
            'Hindari Dosa-dosa Besar',
            'Kemuliaan Berdikari (Hadis Riwayat Bukhari)',
        ], 'skills' => [
            ['Menghuraikan maksud hadis', 'medium'],
            ['Menganalisis pengajaran hadis', 'medium'],
            ['Mengaitkan hadis dengan situasi semasa', 'medium'],
        ]],
        ['form' => 4, 'name' => 'Akidah (Tingkatan 4)', 'subtopics' => [
            'Ilahiyat: Allah Maha Pembalas (Al-Muntaqim) dan Maha Perkasa (Al-Jabbar)',
            'Nubuwat: Beriman kepada Rasul',
            'Sami\'yat: Perkara Ghaib',
            'Perkara yang Membatalkan Iman',
            'Hindari Ajaran Sesat',
        ], 'skills' => [
            ['Menjelaskan sifat-sifat Allah SWT', 'medium'],
            ['Mengenal pasti perkara yang membatalkan iman', 'medium'],
            ['Membezakan ajaran benar dan ajaran sesat', 'hard'],
        ]],
        ['form' => 4, 'name' => 'Fiqah (Tingkatan 4)', 'subtopics' => [
            'Ibadat: Haji dan Umrah',
            'Ibadat: Sembelihan',
            'Ibadat: Korban dan Akikah',
            'Ibadat: Solat Sunat Dhuha dan Solat Sunat Gerhana',
            'Muamalat: Konsep Muamalat dalam Islam',
            'Muamalat: Jual Beli, Sewaan dan Pinjaman',
        ], 'skills' => [
            ['Menjelaskan rukun haji dan umrah', 'medium'],
            ['Menghuraikan syarat sembelihan', 'medium'],
            ['Membezakan korban dan akikah', 'medium'],
            ['Mengaplikasikan konsep muamalat Islam', 'medium'],
        ]],
        ['form' => 4, 'name' => 'Sirah dan Tamadun Islam (Tingkatan 4)', 'subtopics' => [
            'Kerajaan Bani Umaiyyah',
            'Kerajaan Abbasiyyah',
            'Tamadun Islam di Andalusia',
            'Sumbangan Tokoh: Imam-imam Mazhab',
            'Tamadun Islam di Asia Tenggara',
        ], 'skills' => [
            ['Menghuraikan perkembangan kerajaan Islam', 'medium'],
            ['Menilai sumbangan tokoh Islam', 'medium'],
            ['Menganalisis kesan Tamadun Islam terhadap dunia', 'medium'],
        ]],
        ['form' => 4, 'name' => 'Akhlak Islamiah (Tingkatan 4)', 'subtopics' => [
            'Adab Menuntut Ilmu',
            'Adab dalam Majlis',
            'Adab Berbahas dan Berdialog',
            'Adab Terhadap Pekerjaan',
            'Adab Terhadap Pemimpin',
        ], 'skills' => [
            ['Mengamalkan adab menuntut ilmu', 'easy'],
            ['Menghuraikan akhlak dalam pergaulan', 'medium'],
            ['Menilai kepentingan adab dalam Islam', 'medium'],
        ]],

        // ============= TINGKATAN 5 =============
        ['form' => 5, 'name' => 'Al-Quran (Tingkatan 5)', 'subtopics' => [
            'Surah At-Taubah: 128-129 (Sifat Rasulullah SAW)',
            'Surah Al-Hasyr: 21-24 (Asma\' Allah Al-Husna)',
            'Hukum Tajwid: Bacaan Ra (Waqaf dan Ibtida\')',
            'Ciri Mukmin Berjaya (Al-Mukminun: 1-11)',
            'Kecemerlangan Tindakan (At-Taubah: 19-20)',
            'Kepelbagaian Bangsa dalam Islam (Al-Hujurat: 13)',
        ], 'skills' => [
            ['Mentafsir ayat Al-Quran dengan tepat', 'hard'],
            ['Menjelaskan ciri orang mukmin', 'medium'],
            ['Mengaplikasikan nilai kepelbagaian dalam masyarakat', 'medium'],
            ['Mengenal pasti hukum bacaan Ra', 'medium'],
        ]],
        ['form' => 5, 'name' => 'Hadis (Tingkatan 5)', 'subtopics' => [
            'Setiap Orang adalah Pemimpin (Hadis Riwayat Bukhari & Muslim)',
            'Tujuh Golongan yang Dilindungi Allah pada Hari Kiamat',
        ], 'skills' => [
            ['Menjelaskan tanggungjawab kepimpinan', 'medium'],
            ['Mengenal pasti golongan yang dilindungi Allah', 'medium'],
            ['Mengaplikasikan pengajaran hadis dalam kepimpinan', 'medium'],
        ]],
        ['form' => 5, 'name' => 'Akidah (Tingkatan 5)', 'subtopics' => [
            'Ilahiyat: Ar-Raqib (Allah Maha Mengawasi) dan As-Syahid (Maha Menyaksikan)',
            'Akidah Ahlus Sunnah Wal Jamaah',
            'Penyimpangan Akidah',
            'Akidah dan Pembentukan Sahsiah',
        ], 'skills' => [
            ['Menghayati sifat Allah Ar-Raqib dan As-Syahid', 'medium'],
            ['Menjelaskan akidah Ahlus Sunnah Wal Jamaah', 'medium'],
            ['Membincangkan kesan penyimpangan akidah', 'hard'],
        ]],
        ['form' => 5, 'name' => 'Fiqah (Tingkatan 5)', 'subtopics' => [
            'Ibadat: Solat Sunat Istikharah dan Solat Sunat Tasbih',
            'Munakahat: Perkahwinan dalam Islam',
            'Munakahat: Tanggungjawab Suami Isteri',
            'Munakahat: Perceraian, Iddah dan Rujuk',
            'Pengurusan Harta Selepas Kematian (Faraid dan Wasiat)',
            'Jenayah dalam Islam (Hudud, Qisas, Takzir)',
        ], 'skills' => [
            ['Menjelaskan hukum dan rukun perkahwinan', 'medium'],
            ['Menghuraikan tanggungjawab dalam rumah tangga', 'medium'],
            ['Mengira pembahagian harta faraid asas', 'hard'],
            ['Membezakan jenis jenayah dalam Islam', 'medium'],
        ]],
        ['form' => 5, 'name' => 'Sirah dan Tamadun Islam (Tingkatan 5)', 'subtopics' => [
            'Tokoh Cendekiawan Islam (Al-Khawarizmi, Ibn Sina, Al-Biruni)',
            'Tamadun Islam Era Uthmaniyyah',
            'Perkembangan Islam di Nusantara',
            'Sumbangan Islam terhadap Sains dan Teknologi',
            'Cabaran Tamadun Islam Masa Kini',
        ], 'skills' => [
            ['Menilai sumbangan cendekiawan Islam', 'medium'],
            ['Menjelaskan perkembangan Islam di Nusantara', 'medium'],
            ['Menganalisis cabaran umat Islam moden', 'hard'],
        ]],
        ['form' => 5, 'name' => 'Akhlak Islamiah (Tingkatan 5)', 'subtopics' => [
            'Tawaduk (Rendah Hati)',
            'Istiqamah (Berterusan dalam Kebaikan)',
            'Layanan Terhadap Orang Bukan Islam',
            'Akhlak dalam Pergaulan Sosial',
            'Akhlak Profesional dalam Pekerjaan',
        ], 'skills' => [
            ['Mengamalkan sifat tawaduk dan istiqamah', 'medium'],
            ['Menjelaskan adab terhadap bukan Islam', 'medium'],
            ['Menilai kepentingan akhlak dalam pembinaan ummah', 'medium'],
        ]],
    ];
}

/** Starter ayat / hadis / doa bank. type = ayat | hadis | doa. */
function pendidikan_islam_kssm_ayat_hadis(): array
{
    return [
        // ---------- Ayat Al-Quran ----------
        ['Al-Quran (Tingkatan 4)', 'ayat', 'Surah Al-Baqarah: 188',
         'وَلَا تَأْكُلُوا أَمْوَالَكُمْ بَيْنَكُمْ بِالْبَاطِلِ',
         'Wa la ta\'kuluu amwaalakum bainakum bil-baatil',
         'Dan janganlah kamu makan harta sebahagian daripada kamu di antara kamu dengan jalan yang salah.',
         'rasuah', 'Larangan rasuah dan amalan harta haram dalam Islam.'],

        ['Al-Quran (Tingkatan 4)', 'ayat', 'Surah Al-An\'am: 162-163',
         'قُلْ إِنَّ صَلَاتِي وَنُسُكِي وَمَحْيَايَ وَمَمَاتِي لِلَّهِ رَبِّ الْعَالَمِينَ',
         'Qul inna solaatii wa nusukii wa mahyaaya wa mamaatii lillahi Rabbil-\'aalamiin',
         'Katakanlah: Sesungguhnya solatku, ibadahku, hidupku dan matiku, semuanya untuk Allah Tuhan sekalian alam.',
         'tauhid', 'Pengakuan penyerahan diri sepenuhnya kepada Allah dalam setiap aspek kehidupan.'],

        ['Al-Quran (Tingkatan 4)', 'ayat', 'Surah Al-Kahfi: 107-110',
         'إِنَّ الَّذِينَ آمَنُوا وَعَمِلُوا الصَّالِحَاتِ كَانَتْ لَهُمْ جَنَّاتُ الْفِرْدَوْسِ نُزُلًا',
         'Innal-laziina aamanuu wa \'amilus-soolihaati kaanat lahum jannaatul-firdausi nuzulaa',
         'Sesungguhnya orang yang beriman dan mengerjakan amal soleh, bagi mereka syurga Firdaus sebagai tempat tinggal.',
         'balasan amal', 'Janji Allah kepada orang beriman yang beramal soleh.'],

        ['Al-Quran (Tingkatan 4)', 'ayat', 'Surah Fussilat: 33',
         'وَمَنْ أَحْسَنُ قَوْلًا مِّمَّن دَعَا إِلَى اللَّهِ',
         'Wa man ahsanu qawlam mim man da\'aa ilallaah',
         'Dan tidak ada yang lebih baik perkataannya daripada orang yang menyeru kepada Allah.',
         'dakwah', 'Kemuliaan pendakwah yang menyeru manusia ke jalan Allah.'],

        ['Al-Quran (Tingkatan 4)', 'ayat', 'Surah Ali-Imran: 139',
         'وَلَا تَهِنُوا وَلَا تَحْزَنُوا وَأَنتُمُ الْأَعْلَوْنَ إِن كُنتُم مُّؤْمِنِينَ',
         'Wa laa tahinuu wa laa tahzanuu wa antumul-a\'launa in kuntum mu\'miniin',
         'Janganlah kamu bersikap lemah, dan janganlah kamu bersedih hati, padahal kamulah orang yang paling tinggi darjatnya, jika kamu orang yang beriman.',
         'kekuatan iman', 'Pesanan Allah supaya orang mukmin tidak putus asa.'],

        ['Al-Quran (Tingkatan 5)', 'ayat', 'Surah At-Taubah: 128-129',
         'لَقَدْ جَاءَكُمْ رَسُولٌ مِّنْ أَنفُسِكُمْ عَزِيزٌ عَلَيْهِ مَا عَنِتُّمْ',
         'Laqad jaa\'akum rasuulun min anfusikum \'aziizun \'alaihi maa \'anittum',
         'Sesungguhnya telah datang kepadamu seorang Rasul dari kaummu sendiri, berat terasa olehnya penderitaanmu, sangat menginginkan (keimanan dan keselamatan) bagimu.',
         'sifat rasul', 'Menjelaskan sifat-sifat mulia Rasulullah SAW terhadap umatnya.'],

        ['Al-Quran (Tingkatan 5)', 'ayat', 'Surah Al-Mukminun: 1-2',
         'قَدْ أَفْلَحَ الْمُؤْمِنُونَ ۝ الَّذِينَ هُمْ فِي صَلَاتِهِمْ خَاشِعُونَ',
         'Qad aflahal-mu\'minuun. Allaziina hum fii solaatihim khaasyi\'uun',
         'Sesungguhnya beruntunglah orang yang beriman, (iaitu) orang yang khusyuk dalam sembahyangnya.',
         'ciri mukmin', 'Permulaan ciri-ciri orang mukmin yang berjaya.'],

        ['Al-Quran (Tingkatan 5)', 'ayat', 'Surah Al-Hujurat: 13',
         'يَا أَيُّهَا النَّاسُ إِنَّا خَلَقْنَاكُم مِّن ذَكَرٍ وَأُنثَىٰ وَجَعَلْنَاكُمْ شُعُوبًا وَقَبَائِلَ لِتَعَارَفُوا',
         'Yaa ayyuhan-naasu innaa khalaqnaakum min zakarinw wa unsaa wa ja\'alnaakum syu\'uubaw wa qabaa\'ila lita\'aarafuu',
         'Wahai manusia! Sesungguhnya Kami menciptakan kamu daripada lelaki dan perempuan, dan menjadikan kamu berbangsa-bangsa dan bersuku-suku supaya kamu saling mengenali.',
         'kepelbagaian bangsa', 'Asas perpaduan dan saling mengenali dalam kepelbagaian.'],

        ['Al-Quran (Tingkatan 5)', 'ayat', 'Surah Al-Hasyr: 22',
         'هُوَ اللَّهُ الَّذِي لَا إِلَٰهَ إِلَّا هُوَ ۖ عَالِمُ الْغَيْبِ وَالشَّهَادَةِ',
         'Huwa Allaahul-lazii laa ilaaha illaa Huwa, \'aalimul-ghaibi wash-shahaadah',
         'Dialah Allah yang tiada Tuhan selain Dia, Yang Mengetahui yang ghaib dan yang nyata.',
         'asma Allah', 'Pengenalan kepada Asma\' Allah Al-Husna.'],

        ['Al-Quran (Tingkatan 5)', 'ayat', 'Surah At-Taubah: 19-20',
         'أَجَعَلْتُمْ سِقَايَةَ الْحَاجِّ وَعِمَارَةَ الْمَسْجِدِ الْحَرَامِ كَمَنْ آمَنَ بِاللَّهِ',
         'Aja\'altum siqaayatal-haajji wa \'imaaratal-masjidil-haraami kaman aamana billaah',
         'Apakah kamu menjadikan (orang yang) memberi minum kepada orang yang mengerjakan haji dan mengurus Masjidil Haram sama (darjatnya) dengan orang yang beriman kepada Allah?',
         'kecemerlangan', 'Membandingkan amalan zahir dengan iman dan jihad di jalan Allah.'],

        // ---------- Hadis ----------
        ['Hadis (Tingkatan 4)', 'hadis', 'Hadis Riwayat Bukhari (Kemuliaan Berdikari)',
         'مَا أَكَلَ أَحَدٌ طَعَامًا قَطُّ خَيْرًا مِنْ أَنْ يَأْكُلَ مِنْ عَمَلِ يَدِهِ',
         'Maa akala ahadun ta\'aaman qattu khairan min an ya\'kula min \'amali yadih',
         'Tidaklah seseorang itu makan suatu makanan yang lebih baik daripada makan hasil titik peluhnya sendiri.',
         'berdikari', 'Anjuran Islam supaya umatnya bekerja dan tidak mengharap belas kasihan.'],

        ['Hadis (Tingkatan 4)', 'hadis', 'Hadis Tujuh Dosa Besar (Bukhari)',
         'اجْتَنِبُوا السَّبْعَ الْمُوبِقَاتِ',
         'Ijtanibus-sab\'al-muubiqaat',
         'Jauhilah olehmu tujuh perkara yang membinasakan: syirik, sihir, membunuh jiwa yang diharamkan Allah, makan riba, makan harta anak yatim, lari dari medan perang, dan menuduh wanita mukmin.',
         'dosa besar', 'Senarai tujuh dosa besar yang wajib dijauhi.'],

        ['Hadis (Tingkatan 5)', 'hadis', 'Setiap Kamu adalah Pemimpin (Bukhari & Muslim)',
         'كُلُّكُمْ رَاعٍ وَكُلُّكُمْ مَسْؤُولٌ عَنْ رَعِيَّتِهِ',
         'Kullukum raa\'in wa kullukum mas\'uulun \'an ra\'iyyatih',
         'Setiap kamu adalah pemimpin dan setiap kamu akan ditanya tentang kepimpinannya.',
         'kepimpinan', 'Tanggungjawab kepimpinan ada pada setiap individu, dari rumah tangga hingga negara.'],

        ['Hadis (Tingkatan 5)', 'hadis', 'Tujuh Golongan dalam Naungan Allah (Bukhari & Muslim)',
         'سَبْعَةٌ يُظِلُّهُمُ اللَّهُ فِي ظِلِّهِ يَوْمَ لَا ظِلَّ إِلَّا ظِلُّهُ',
         'Sab\'atun yuzilluhumullaahu fii zillihi yawma laa zilla illaa zilluh',
         'Tujuh golongan yang akan dilindungi Allah dalam naungan-Nya pada hari tiada perlindungan selain perlindungan-Nya: pemimpin adil, pemuda yang membesar dalam ibadah, lelaki yang hatinya terpaut di masjid, dua orang yang berkasih kerana Allah, lelaki yang menolak ajakan zina, orang yang bersedekah secara senyap, dan orang yang berzikir dalam kesunyian sehingga menangis.',
         'kebaikan', 'Senarai amalan yang menjamin perlindungan Allah di akhirat.'],

        ['Akhlak Islamiah (Tingkatan 4)', 'hadis', 'Hadis Riwayat Tirmizi (Akhlak Baik)',
         'أَكْمَلُ الْمُؤْمِنِينَ إِيمَانًا أَحْسَنُهُمْ خُلُقًا',
         'Akmalul-mu\'miniina iimaanan ahsanuhum khuluqaa',
         'Mukmin yang paling sempurna imannya ialah yang paling baik akhlaknya.',
         'akhlak', 'Akhlak adalah ukuran kesempurnaan iman seorang mukmin.'],

        // ---------- Doa ----------
        ['Akhlak Islamiah (Tingkatan 4)', 'doa', 'Doa Menuntut Ilmu',
         'رَبِّ زِدْنِي عِلْمًا',
         'Rabbi zidnii \'ilma',
         'Ya Tuhanku, tambahkanlah aku ilmu.',
         'menuntut ilmu', 'Doa pendek dari Surah Ta-Ha ayat 114, sering diamalkan pelajar.'],

        ['Akhlak Islamiah (Tingkatan 4)', 'doa', 'Doa Mohon Kebaikan Dunia dan Akhirat',
         'رَبَّنَا آتِنَا فِي الدُّنْيَا حَسَنَةً وَفِي الْآخِرَةِ حَسَنَةً وَقِنَا عَذَابَ النَّارِ',
         'Rabbanaa aatinaa fid-dunyaa hasanataw wa fil-aakhirati hasanataw wa qinaa \'azaaban-naar',
         'Wahai Tuhan kami, berilah kami kebaikan di dunia dan kebaikan di akhirat, dan peliharalah kami dari azab neraka.',
         'doa harian', 'Doa sapu jagat dari Surah Al-Baqarah ayat 201, ringkas dan menyeluruh.'],

        ['Fiqah (Tingkatan 4)', 'doa', 'Doa Selepas Wuduk',
         'أَشْهَدُ أَنْ لَا إِلَهَ إِلَّا اللَّهُ وَحْدَهُ لَا شَرِيكَ لَهُ وَأَشْهَدُ أَنَّ مُحَمَّدًا عَبْدُهُ وَرَسُولُهُ',
         'Asyhadu an laa ilaaha illallaahu wahdahu laa syariika lah, wa asyhadu anna Muhammadan \'abduhu wa rasuuluh',
         'Aku bersaksi tiada Tuhan selain Allah Yang Esa, tiada sekutu bagi-Nya, dan aku bersaksi bahawa Muhammad adalah hamba dan rasul-Nya.',
         'wuduk', 'Lafaz syahadah dibaca selepas selesai berwuduk.'],

        ['Akhlak Islamiah (Tingkatan 5)', 'doa', 'Doa Mohon Hati Istiqamah',
         'يَا مُقَلِّبَ الْقُلُوبِ ثَبِّتْ قَلْبِي عَلَى دِينِكَ',
         'Yaa muqallibal-quluub, sabbit qalbii \'alaa diinik',
         'Wahai Yang Membolak-balikkan hati, tetapkanlah hatiku atas agama-Mu.',
         'istiqamah', 'Doa Rasulullah SAW agar hati tetap teguh dalam Islam.'],

        ['Fiqah (Tingkatan 5)', 'doa', 'Doa Sebelum Solat Istikharah',
         'اللَّهُمَّ إِنِّي أَسْتَخِيرُكَ بِعِلْمِكَ وَأَسْتَقْدِرُكَ بِقُدْرَتِكَ',
         'Allaahumma innii astakhiiruka bi\'ilmika wa astaqdiruka biqudratik',
         'Ya Allah, sesungguhnya aku memohon pilihan-Mu dengan ilmu-Mu dan memohon kemampuan-Mu dengan kekuasaan-Mu.',
         'istikharah', 'Doa khusus untuk memohon petunjuk dalam membuat keputusan penting.'],
    ];
}

function pendidikan_islam_kssm_run(): array
{
    $subject = db_one("SELECT id FROM subjects WHERE slug = 'pendidikan-islam' LIMIT 1");
    if (!$subject) {
        return ['status' => 'no_subject', 'message' => 'Pendidikan Islam subject not found. Run install / seed_subjects first.'];
    }
    $sid = (int) $subject['id'];

    $ayatTable = (bool) db_one("SELECT 1 AS x FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'islam_ayat_hadis'");

    $catalog = pendidikan_islam_kssm_catalog();
    $topicsCreated = $topicsKept = $topicsAdopted = 0;
    $subCreated = $subKept = 0;
    $skillsCreated = $skillsKept = 0;
    $ayatCreated = $ayatKept = 0;

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

    if ($ayatTable) {
        foreach (pendidikan_islam_kssm_ayat_hadis() as $idx => [$topicName, $type, $ref, $arabic, $translit, $translation, $theme, $explanation]) {
            $tid = $topicIds[$topicName] ?? null;
            $existing = db_one('SELECT id FROM islam_ayat_hadis WHERE reference = ? LIMIT 1', [$ref]);
            if ($existing) {
                $ayatKept++;
            } else {
                db_exec(
                    'INSERT INTO islam_ayat_hadis (topic_id, topic_label, type, reference, arabic_text, transliteration, translation, theme, explanation, sort_order)
                     VALUES (?,?,?,?,?,?,?,?,?,?)',
                    [$tid, $topicName, $type, $ref, $arabic, $translit, $translation, $theme, $explanation, $idx + 1]
                );
                $ayatCreated++;
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
        'ayat_created'       => $ayatCreated,
        'ayat_kept'          => $ayatKept,
        'catalog_topics'     => count($catalog),
        'ayat_table'         => $ayatTable,
    ];
}

if (PHP_SAPI === 'cli') {
    $r = pendidikan_islam_kssm_run();
    if (($r['status'] ?? '') !== 'ok') {
        fwrite(STDERR, ($r['message'] ?? 'unknown error') . "\n");
        exit(1);
    }
    echo "KSSM Pendidikan Islam seeded —\n";
    echo "  Bidang:        created {$r['topics_created']}, adopted {$r['topics_adopted']}, kept {$r['topics_kept']} (catalog {$r['catalog_topics']})\n";
    echo "  Pelajaran:     created {$r['subtopics_created']}, kept {$r['subtopics_kept']}\n";
    echo "  Kemahiran:     created {$r['skills_created']}, kept {$r['skills_kept']}\n";
    if ($r['ayat_table']) {
        echo "  Ayat/Hadis/Doa: created {$r['ayat_created']}, kept {$r['ayat_kept']}\n";
    } else {
        echo "  Ayat/Hadis/Doa: table missing — run database migrations to enable.\n";
    }
}
