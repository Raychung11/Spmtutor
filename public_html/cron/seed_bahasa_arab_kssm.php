<?php
/**
 * KSSM SPM Bahasa Arab (اللغة العربية) syllabus seeder.
 * Tingkatan 4 (7 topik) + Tingkatan 5 (7 topik) — 14 topik total,
 * organised by macro skills mirip seed_bm_kssm.php /
 * seed_bahasa_cina_kssm.php / seed_bahasa_tamil_kssm.php
 * (Istima'+Kalam, Qira'ah, Kitabah, Nahu, Mufradat, Insya', Adab).
 *
 * Plus starter bank rujukan Arab: أمثال amthal (proverbs), nahu terms
 * (mubtada/khabar/fi'l mudhari'/madhi), mufradat tematik, tokoh
 * (Al-Mutanabbi, Mahmoud Darwish, Naguib Mahfouz) dan karya.
 *
 * Idempotent: topik matched by (subject_id, name, form_level); legacy
 * same-named NULL-form topik are adopted.
 *
 *   php public_html/cron/seed_bahasa_arab_kssm.php
 */
declare(strict_types=1);

require_once __DIR__ . '/../inc/db.php';

function bahasa_arab_kssm_catalog(): array
{
    return [
        // ============= TINGKATAN 4 =============
        ['form' => 4, 'name' => 'الاستماع والكلام (Kemahiran Mendengar dan Bertutur)', 'subtopics' => [
            'الاستماع للمعلومات (Mendengar Maklumat)',
            'الإجابة والاستجابة (Memberi Respons)',
            'الحوار (Perbincangan / Dialog)',
            'العرض الشفوي (Pembentangan Lisan)',
            'الاتصال الرسمي (Komunikasi Formal)',
        ], 'skills' => [
            ['فهم المعلومات الرئيسية (Memahami maklumat penting)', 'easy'],
            ['التعبير عن الآراء (Menyampaikan pendapat)', 'medium'],
            ['التواصل بفعالية (Berkomunikasi secara berkesan)', 'medium'],
        ]],
        ['form' => 4, 'name' => 'القراءة والفهم (Pemahaman / Kemahiran Membaca)', 'subtopics' => [
            'النص العام (Petikan Umum)',
            'النص الأدبي (Petikan Sastera)',
            'الأسئلة العليا (Soalan KBAT)',
            'استخراج الفكرة الرئيسية (Idea Pokok)',
            'الاستنتاج (Inferens)',
        ], 'skills' => [
            ['استخراج الأفكار الرئيسية (Mencari isi penting)', 'medium'],
            ['الاستنتاج من النص (Membuat inferens)', 'medium'],
            ['الإجابة عن أسئلة عليا (Menjawab soalan KBAT)', 'hard'],
        ]],
        ['form' => 4, 'name' => 'النحو الأساسي (Tatabahasa / Nahu Asas)', 'subtopics' => [
            'الاسم (Kata Nama — Isim)',
            'الفعل: الماضي والمضارع والأمر (Kata Kerja — Madhi, Mudhari\', Amr)',
            'الحرف (Kata Tugas — Harf)',
            'المبتدأ والخبر (Jumlah Ismiyyah)',
            'الفعل والفاعل والمفعول به (Jumlah Fi\'liyyah)',
            'تصحيح الأخطاء (Pembetulan Kesalahan)',
        ], 'skills' => [
            ['تمييز الاسم والفعل والحرف (Mengenal pasti isim, fi\'l, harf)', 'easy'],
            ['تركيب الجمل النحوية الصحيحة (Membina ayat gramatis)', 'medium'],
            ['إعراب الكلمات (Mengi\'rab kalimah)', 'hard'],
        ]],
        ['form' => 4, 'name' => 'المفردات (Kosa Kata / Mufradat)', 'subtopics' => [
            'الأسرة والبيت (Keluarga dan Rumah)',
            'المدرسة والتعليم (Sekolah dan Pendidikan)',
            'المجتمع والمناسبات (Masyarakat dan Majlis)',
            'الصحة والرياضة (Kesihatan dan Sukan)',
            'العمل والمهن (Pekerjaan dan Kerjaya)',
        ], 'skills' => [
            ['توسيع المفردات (Memperluas kosa kata)', 'medium'],
            ['استخدام المفردات في الجمل (Menggunakan kosa kata dalam ayat)', 'medium'],
            ['تمييز المرادفات والأضداد (Mengenal pasti sinonim dan antonim)', 'medium'],
        ]],
        ['form' => 4, 'name' => 'الإنشاء القصير (Karangan Pendek)', 'subtopics' => [
            'الرسالة الشخصية (Surat Peribadi)',
            'البريد الإلكتروني (E-mel)',
            'الإعلانات والإخطارات (Iklan dan Notis)',
            'الفقرة الوصفية (Perenggan Deskriptif)',
            'تقرير قصير (Laporan Pendek)',
        ], 'skills' => [
            ['كتابة رسائل قصيرة (Menulis surat pendek)', 'medium'],
            ['اتباع الشكل المناسب (Mematuhi format)', 'easy'],
            ['التعبير بوضوح (Menyampaikan idea dengan jelas)', 'medium'],
        ]],
        ['form' => 4, 'name' => 'الإنشاء الموجه (Karangan Berpandu)', 'subtopics' => [
            'الإنشاء الوصفي (Karangan Deskriptif)',
            'الإنشاء السردي (Karangan Naratif)',
            'الإنشاء التفسيري (Karangan Penerangan)',
            'الخطاب (Karangan Ucapan)',
            'تنظيم الفقرات (Organisasi Perenggan)',
        ], 'skills' => [
            ['تخطيط الإنشاء (Merangka karangan)', 'medium'],
            ['تطوير الفقرات (Mengembangkan perenggan)', 'medium'],
            ['استخدام الروابط (Menggunakan kata penghubung)', 'medium'],
        ]],
        ['form' => 4, 'name' => 'الثقافة العربية والإسلامية (Budaya Arab dan Islam)', 'subtopics' => [
            'الأمثال العربية (Peribahasa Arab)',
            'القصص من القرآن (Kisah dari Al-Quran)',
            'سيرة الأنبياء (Sirah Para Nabi)',
            'العلماء المسلمون (Tokoh Cendekiawan Islam)',
            'المعالم الإسلامية (Mercu Tanda Islam)',
        ], 'skills' => [
            ['فهم الأمثال العربية (Memahami peribahasa Arab)', 'medium'],
            ['ربط الثقافة باللغة (Menghubungkan budaya dengan bahasa)', 'medium'],
            ['تقدير التراث الإسلامي (Menghargai warisan Islam)', 'medium'],
        ]],

        // ============= TINGKATAN 5 =============
        ['form' => 5, 'name' => 'القراءة المتقدمة (Pemahaman KBAT)', 'subtopics' => [
            'النصوص الطويلة (Petikan Panjang)',
            'التحليل والنقد (Analisis dan Kritik)',
            'وجهة نظر الكاتب (Pandangan Penulis)',
            'البلاغة (Bahasa Retorik / Balaghah)',
            'القراءة النقدية (Pembacaan Kritis)',
        ], 'skills' => [
            ['القراءة النقدية (Membaca secara kritis)', 'hard'],
            ['تحليل النصوص الأدبية (Menganalisis teks sastera)', 'hard'],
            ['الإجابة عن أسئلة KBAT (Menjawab soalan KBAT)', 'hard'],
        ]],
        ['form' => 5, 'name' => 'النحو المتقدم (Tatabahasa Lanjutan)', 'subtopics' => [
            'الجملة الاسمية والفعلية (Jumlah Ismiyyah dan Fi\'liyyah)',
            'المرفوعات (Marfu\'at — kasus nominatif)',
            'المنصوبات (Mansubat — kasus akusatif)',
            'المجرورات (Majrurat — kasus genitif)',
            'كان وأخواتها وإنّ وأخواتها (Kana wa Akhwatuha wa Inna wa Akhwatuha)',
            'الإعراب التطبيقي (I\'rab Praktikal)',
        ], 'skills' => [
            ['تطبيق قواعد الإعراب (Mengaplikasikan kaedah i\'rab)', 'hard'],
            ['تمييز المرفوعات والمنصوبات (Mengenal pasti marfu\'at dan mansubat)', 'hard'],
            ['تصحيح الأخطاء النحوية (Membetulkan kesalahan nahu)', 'hard'],
        ]],
        ['form' => 5, 'name' => 'الإنشاء الحجاجي (Karangan Hujahan)', 'subtopics' => [
            'تحديد الأطروحة (Pernyataan Tesis)',
            'تقديم الحجج والأدلة (Memberi Hujah dan Bukti)',
            'الرد على الاعتراضات (Membalas Bantahan)',
            'الخاتمة المقنعة (Penutup yang Meyakinkan)',
            'استخدام الأمثال (Menggunakan Peribahasa)',
        ], 'skills' => [
            ['طرح الحجج بوضوح (Menyatakan hujah jelas)', 'hard'],
            ['دعم الأطروحة بأدلة (Menyokong tesis dengan bukti)', 'hard'],
            ['كتابة مقال حجاجي كامل (Menulis karangan hujahan lengkap)', 'hard'],
        ]],
        ['form' => 5, 'name' => 'الإنشاء السردي والوصفي (Karangan Naratif dan Deskriptif)', 'subtopics' => [
            'عناصر القصة (Elemen Cerita)',
            'بناء الشخصيات (Penggambaran Watak)',
            'وصف المشاهد (Penggambaran Latar)',
            'الحوار في القصة (Dialog dalam Cerita)',
            'استخدام الصور البلاغية (Penggunaan Bahasa Kiasan)',
        ], 'skills' => [
            ['بناء حبكة متكاملة (Membina plot lengkap)', 'medium'],
            ['الوصف الحي (Menggunakan penggambaran hidup)', 'hard'],
            ['التعبير عن المشاعر (Menyampaikan emosi)', 'medium'],
        ]],
        ['form' => 5, 'name' => 'التلخيص (Ringkasan)', 'subtopics' => [
            'استخراج الأفكار الرئيسية (Mengenal Idea Utama)',
            'إعادة الصياغة (Paraphrasing)',
            'حدود الكلمات (Had Perkataan)',
            'الترابط بين الأفكار (Penghubungan Idea)',
            'البنية الواضحة (Struktur Jelas)',
        ], 'skills' => [
            ['كتابة ملخص متماسك (Menulis ringkasan padat)', 'hard'],
            ['إعادة الصياغة بدقة (Paraphrase dengan tepat)', 'hard'],
            ['التقيد بحد الكلمات (Mematuhi had perkataan)', 'medium'],
        ]],
        ['form' => 5, 'name' => 'الأدب العربي (Kesusasteraan Arab)', 'subtopics' => [
            'الشعر العربي الكلاسيكي (Syair Arab Klasik)',
            'الشعر العربي الحديث (Syair Arab Moden)',
            'القصة القصيرة (Cerpen Arab)',
            'الرواية العربية (Novel Arab)',
            'البلاغة والمحسنات (Balaghah dan Hiasan Sastera)',
        ], 'skills' => [
            ['تحليل القصائد العربية (Menganalisis syair Arab)', 'hard'],
            ['فهم البلاغة (Memahami balaghah)', 'hard'],
            ['تقدير الأدب العربي (Mengapresiasi sastera Arab)', 'medium'],
        ]],
        ['form' => 5, 'name' => 'الحضارة الإسلامية (Tamadun Islam dalam Bahasa Arab)', 'subtopics' => [
            'العصور الإسلامية (Era Islam — Umaiyyah, Abbasiyyah, Andalusia)',
            'العلماء والمكتشفين (Cendekiawan dan Penemu)',
            'العمارة الإسلامية (Seni Bina Islam)',
            'الاكتشافات العلمية (Penemuan Sains)',
            'القيم الإسلامية في اللغة (Nilai Islam dalam Bahasa)',
        ], 'skills' => [
            ['وصف الحضارة الإسلامية (Menerangkan Tamadun Islam)', 'medium'],
            ['تقدير إنجازات المسلمين (Menghargai pencapaian umat Islam)', 'medium'],
            ['ربط التاريخ باللغة (Menghubungkan sejarah dengan bahasa)', 'medium'],
        ]],
    ];
}

/**
 * Starter Bahasa Arab references — amthal (proverbs), nahu terms,
 * mufradat tematik, dan tokoh sasterawan.
 */
function bahasa_arab_kssm_references(): array
{
    return [
        // topic, type, expression, transliteration, meaning_bm, meaning_ar, example, origin, category

        // ===== أمثال / Arabic proverbs =====
        ['الثقافة العربية والإسلامية (Budaya Arab dan Islam)', 'mathal',
         'مَنْ جَدَّ وَجَدَ', 'Man jadda wajada',
         'Siapa yang bersungguh-sungguh akan berjaya.',
         'الذي يجتهد سوف ينجح.',
         'مثل عربي مشهور يُحفِّز الطلاب على الاجتهاد.',
         'مثل عربي', 'usaha'],

        ['الثقافة العربية والإسلامية (Budaya Arab dan Islam)', 'mathal',
         'العِلْمُ فِي الصِّغَرِ كَالنَّقْشِ عَلَى الحَجَرِ', 'Al-\'ilmu fis-sighari kan-naqshi \'alal-hajar',
         'Ilmu di waktu kecil seperti ukiran pada batu — kekal lama dalam ingatan.',
         'العلم في الصغر يبقى في الذاكرة طويلاً مثل النقش على الحجر.',
         'مثل تربوي يحث على التعلم في الصغر.',
         'مثل عربي', 'pembelajaran'],

        ['الثقافة العربية والإسلامية (Budaya Arab dan Islam)', 'mathal',
         'الصَّبْرُ مِفْتَاحُ الفَرَجِ', 'As-sabru miftahul-faraj',
         'Kesabaran adalah kunci kelapangan / kelegaan.',
         'الصبر يفتح أبواب الفرج والراحة.',
         'مثل شائع يحث على الصبر عند الشدائد.',
         'مثل عربي', 'akhlak'],

        ['الثقافة العربية والإسلامية (Budaya Arab dan Islam)', 'mathal',
         'الوَقْتُ كَالسَّيْفِ إِنْ لَمْ تَقْطَعْهُ قَطَعَكَ', 'Al-waqtu kas-saif in lam taqta\'hu qata\'ak',
         'Waktu seperti pedang — jika engkau tidak memotongnya, ia akan memotongmu.',
         'الوقت كالسيف؛ إن لم تستفد منه فسوف يضرك.',
         'مثل يحث على إدارة الوقت.',
         'مثل عربي', 'masa'],

        ['الثقافة العربية والإسلامية (Budaya Arab dan Islam)', 'mathal',
         'العَقْلُ السَّلِيمُ فِي الجِسْمِ السَّلِيمِ', 'Al-\'aqlus-salim fil-jismis-salim',
         'Akal yang sihat ada dalam tubuh yang sihat.',
         'العقل القوي يعتمد على الجسم القوي.',
         'مثل يحث على ممارسة الرياضة وصحة البدن.',
         'مثل عربي', 'kesihatan'],

        ['الثقافة العربية والإسلامية (Budaya Arab dan Islam)', 'mathal',
         'إِنَّ مَعَ العُسْرِ يُسْرًا', 'Inna ma\'al-\'usri yusra',
         'Sesungguhnya bersama kesulitan ada kemudahan.',
         'بعد كل صعوبة يأتي اليُسر والسهولة.',
         'من سورة الشرح آية ٦.',
         'القرآن الكريم — سورة الشرح', 'akhlak'],

        ['الثقافة العربية والإسلامية (Budaya Arab dan Islam)', 'mathal',
         'خَيْرُ الكَلاَمِ مَا قَلَّ وَدَلَّ', 'Khairul-kalami ma qalla wa dalla',
         'Sebaik-baik percakapan adalah yang ringkas tetapi padat maknanya.',
         'الكلام الجيد هو القليل المفيد.',
         'مثل عربي عن البلاغة.',
         'مثل عربي', 'bahasa'],

        ['الثقافة العربية والإسلامية (Budaya Arab dan Islam)', 'mathal',
         'لاَ يُلْدَغُ المُؤْمِنُ مِنْ جُحْرٍ وَاحِدٍ مَرَّتَيْنِ', 'La yulda\'ul-mu\'minu min juhrin wahidin marratain',
         'Seorang mukmin tidak akan disengat dari lubang yang sama dua kali.',
         'الإنسان الذكي لا يخطئ في نفس المكان مرتين.',
         'حديث نبوي شريف — رواه البخاري ومسلم.',
         'حديث شريف', 'pengajaran'],

        ['الثقافة العربية والإسلامية (Budaya Arab dan Islam)', 'mathal',
         'مَنْ سَارَ عَلَى الدَّرْبِ وَصَلَ', 'Man sara \'alad-darbi wasal',
         'Siapa yang mengikuti jalan akan sampai ke destinasi — ketekunan membuahkan hasil.',
         'الذي يستمر في طريقه يصل إلى هدفه.',
         'مثل يحث على المثابرة.',
         'مثل عربي', 'usaha'],

        ['الثقافة العربية والإسلامية (Budaya Arab dan Islam)', 'mathal',
         'مَا حَكَّ جِلْدَكَ مِثْلُ ظُفْرِكَ', 'Ma hakka jildaka mithlu zhufrik',
         'Tiada yang dapat menggaruk kulitmu seperti kukumu sendiri — bergantung pada diri sendiri.',
         'لا أحد يهتم بأمرك مثلك.',
         'مثل عن الاعتماد على النفس.',
         'مثل عربي', 'kemandirian'],

        // ===== Nahu / Grammar terms =====
        ['النحو الأساسي (Tatabahasa / Nahu Asas)', 'nahu',
         'الاسم (Isim)', 'al-Ism',
         'Kata Nama — kata yang menunjukkan benda, orang, tempat atau keadaan.',
         'كلمة تدل على معنى في نفسها غير مقترنة بزمن.',
         'مثال: مُحَمَّدٌ، كِتَابٌ، بَيْتٌ، جَمِيلٌ.',
         'علم النحو', 'kata_dasar'],

        ['النحو الأساسي (Tatabahasa / Nahu Asas)', 'nahu',
         'الفعل (Fi\'l)', 'al-Fi\'l',
         'Kata Kerja — kata yang menunjukkan perbuatan dan terikat dengan masa.',
         'كلمة تدل على معنى مقترن بزمن.',
         'الماضي: كَتَبَ — المضارع: يَكْتُبُ — الأمر: اُكْتُبْ.',
         'علم النحو', 'kata_dasar'],

        ['النحو الأساسي (Tatabahasa / Nahu Asas)', 'nahu',
         'الحرف (Harf)', 'al-Harf',
         'Kata Tugas — kata yang tidak mempunyai makna tersendiri kecuali bersambung dengan kata lain.',
         'كلمة لا تدل على معنى إلا مع غيرها.',
         'مثال: فِي، عَلَى، إِلَى، مِنْ، عَنْ.',
         'علم النحو', 'kata_dasar'],

        ['النحو الأساسي (Tatabahasa / Nahu Asas)', 'nahu',
         'الجملة الاسمية (Jumlah Ismiyyah)', 'al-Jumlah al-Ismiyyah',
         'Ayat Namaan — ayat yang dimulakan dengan isim, terdiri daripada mubtada\' + khabar.',
         'الجملة التي تبدأ بالاسم، مكونة من المبتدأ والخبر.',
         'مثال: الطَّالِبُ مُجْتَهِدٌ — Pelajar itu rajin.',
         'علم النحو', 'jumlah'],

        ['النحو الأساسي (Tatabahasa / Nahu Asas)', 'nahu',
         'الجملة الفعلية (Jumlah Fi\'liyyah)', 'al-Jumlah al-Fi\'liyyah',
         'Ayat Kerjaan — ayat yang dimulakan dengan fi\'l, terdiri daripada fi\'l + fa\'il (+ maf\'ul).',
         'الجملة التي تبدأ بالفعل، مكونة من الفعل والفاعل والمفعول به.',
         'مثال: قَرَأَ الطَّالِبُ الكِتَابَ — Pelajar membaca buku.',
         'علم النحو', 'jumlah'],

        ['النحو الأساسي (Tatabahasa / Nahu Asas)', 'nahu',
         'المبتدأ والخبر (Mubtada\' wal-Khabar)', 'al-Mubtada wal-Khabar',
         'Subjek dan Predikat dalam ayat namaan — mubtada\' adalah perkara yang diceritakan, khabar menerangkannya.',
         'المبتدأ هو الاسم المرفوع في أول الجملة الاسمية، والخبر هو ما يكمل معناه.',
         'مثال: العِلْمُ نُورٌ — Ilmu (mubtada) cahaya (khabar).',
         'علم النحو', 'jumlah_ismiyyah'],

        ['النحو المتقدم (Tatabahasa Lanjutan)', 'nahu',
         'كَانَ وَأَخَوَاتُهَا (Kana wa Akhwatuha)', 'Kana wa Akhwatuha',
         'Kana dan saudaranya — kata kerja tidak sempurna yang menaikkan mubtada\' (kepada nama) dan mensaiisnya menjadi khabar dengan kasus akusatif.',
         'أفعال ناقصة ترفع المبتدأ ويسمى اسمها وتنصب الخبر ويسمى خبرها.',
         'مثال: كَانَ الطَّالِبُ مُجْتَهِدًا. (كان + اسمها (الطالب) + خبرها (مجتهدًا)).',
         'علم النحو', 'kana'],

        ['النحو المتقدم (Tatabahasa Lanjutan)', 'nahu',
         'إِنَّ وَأَخَوَاتُهَا (Inna wa Akhwatuha)', 'Inna wa Akhwatuha',
         'Inna dan saudaranya — huruf yang menasibkan mubtada\' (kasus akusatif) dan merafa\'kan khabar (kasus nominatif).',
         'حروف تنصب المبتدأ ويسمى اسمها وترفع الخبر ويسمى خبرها.',
         'مثال: إِنَّ الطَّالِبَ مُجْتَهِدٌ. (إن + اسمها (الطالبَ) + خبرها (مجتهدٌ)).',
         'علم النحو', 'inna'],

        ['النحو المتقدم (Tatabahasa Lanjutan)', 'nahu',
         'المرفوعات (Marfu\'at)', 'al-Marfu\'at',
         'Kasus nominatif — kata-kata yang merafa\'/dirafa\'kan: subjek (fa\'il), namaan kana, ayat mubtada/khabar.',
         'الكلمات المرفوعة في الإعراب.',
         'الفاعل، نائب الفاعل، المبتدأ، الخبر، اسم كان، خبر إنّ.',
         'علم النحو', 'irab'],

        ['النحو المتقدم (Tatabahasa Lanjutan)', 'nahu',
         'المنصوبات (Mansubat)', 'al-Mansubat',
         'Kasus akusatif — kata-kata yang menjadi mansub: objek, khabar kana, namaan inna.',
         'الكلمات المنصوبة في الإعراب.',
         'المفعول به، خبر كان، اسم إنّ، الحال، التمييز.',
         'علم النحو', 'irab'],

        ['النحو المتقدم (Tatabahasa Lanjutan)', 'nahu',
         'المجرورات (Majrurat)', 'al-Majrurat',
         'Kasus genitif — kata-kata yang menjadi majrur: selepas harf jar atau muodaf ilaih.',
         'الكلمات المجرورة في الإعراب.',
         'الاسم بعد حرف الجر (في البيت)، المضاف إليه (كتاب الطالب).',
         'علم النحو', 'irab'],

        // ===== Mufradat / Vocabulary =====
        ['المفردات (Kosa Kata / Mufradat)', 'mufradat',
         'مَدْرَسَةٌ (Madrasah)', 'madrasah',
         'Sekolah — tempat pembelajaran formal.',
         'المؤسسة التعليمية التي يدرس فيها الطلاب.',
         'أَذْهَبُ إِلَى المَدْرَسَةِ كُلَّ يَوْمٍ — Saya pergi ke sekolah setiap hari.',
         null, 'pendidikan'],

        ['المفردات (Kosa Kata / Mufradat)', 'mufradat',
         'مُعَلِّمٌ (Mu\'allim)', 'mu\'allim',
         'Guru lelaki — orang yang mengajar.',
         'الشخص الذي يقوم بتعليم الطلاب.',
         'المُعَلِّمُ يَشْرَحُ الدَّرْسَ — Guru menerangkan pelajaran.',
         null, 'pendidikan'],

        ['المفردات (Kosa Kata / Mufradat)', 'mufradat',
         'كِتَابٌ (Kitab)', 'kitab',
         'Buku — kumpulan helaian bertulis atau bercetak yang dijilid.',
         'مجموعة من الأوراق المطبوعة أو المكتوبة المجلدة معاً.',
         'هَذَا كِتَابٌ مُفِيدٌ — Ini buku yang bermanfaat.',
         null, 'pendidikan'],

        ['المفردات (Kosa Kata / Mufradat)', 'mufradat',
         'أُسْرَةٌ (Usrah)', 'usrah',
         'Keluarga — unit kekeluargaan terkecil yang terdiri daripada ibu, ayah dan anak-anak.',
         'الأب والأم والأولاد.',
         'أُسْرَتِي صَغِيرَةٌ — Keluarga saya kecil.',
         null, 'keluarga'],

        ['المفردات (Kosa Kata / Mufradat)', 'mufradat',
         'مَسْجِدٌ (Masjid)', 'masjid',
         'Masjid — tempat ibadah umat Islam.',
         'مكان عبادة المسلمين.',
         'يَذْهَبُ المُسْلِمُونَ إِلَى المَسْجِدِ — Orang Islam pergi ke masjid.',
         null, 'agama'],

        // ===== Tokoh sasterawan =====
        ['الأدب العربي (Kesusasteraan Arab)', 'author',
         'المُتَنَبِّي (Al-Mutanabbi)', 'Al-Mutanabbi',
         'Abu al-Tayyib al-Mutanabbi (915-965 M) — penyair Arab agung Era Abbasiyyah, terkenal dengan kebijaksanaan dan kehebatan bahasanya.',
         'أبو الطيب المتنبي — من أعظم الشعراء العرب في العصر العباسي.',
         '"الخَيْلُ وَاللَّيْلُ وَالبَيْدَاءُ تَعْرِفُنِي وَالسَّيْفُ وَالرُّمْحُ وَالقِرْطَاسُ وَالقَلَمُ" — bait masyhur.',
         'العصر العباسي', 'penyair_klasik'],

        ['الأدب العربي (Kesusasteraan Arab)', 'author',
         'أَحْمَد شَوْقِي (Ahmad Shawqi)', 'Ahmad Shawqi',
         'Ahmad Shawqi (1868-1932) — Penyair Diraja (أمير الشعراء); pelopor kebangkitan syair Arab moden di Mesir.',
         'أمير الشعراء، رائد النهضة الشعرية في العصر الحديث.',
         '"الشَّرْقُ عَتَى وَالمَجْدُ مِنْ سَلِيلٍ" — daripada syair revival.',
         'مصر / العصر الحديث', 'penyair_moden'],

        ['الأدب العربي (Kesusasteraan Arab)', 'author',
         'محمود درويش (Mahmoud Darwish)', 'Mahmoud Darwish',
         'Mahmoud Darwish (1941-2008) — penyair Palestin agung; suara kemerdekaan dan identiti Palestin di pentas sastera dunia.',
         'الشاعر الفلسطيني العظيم، صوت القضية الفلسطينية.',
         '"سَجِّلْ أَنَا عَرَبِيٌّ" — "Tulis: aku ialah Arab".',
         'فلسطين / العصر الحديث', 'penyair_moden'],

        ['الأدب العربي (Kesusasteraan Arab)', 'author',
         'نَجِيب مَحْفُوظ (Naguib Mahfouz)', 'Naguib Mahfouz',
         'Naguib Mahfouz (1911-2006) — novelis Mesir; pemenang Hadiah Nobel Kesusasteraan 1988 (Arab pertama).',
         'الروائي المصري الحائز على جائزة نوبل في الأدب عام ١٩٨٨.',
         'ثلاثية القاهرة: "بين القصرين"، "قصر الشوق"، "السكرية".',
         'مصر / العصر الحديث', 'pengarang_moden'],

        // ===== Tamadun Islam =====
        ['الحضارة الإسلامية (Tamadun Islam dalam Bahasa Arab)', 'tamadun',
         'الخوارزمي (Al-Khawarizmi)', 'Al-Khawarizmi',
         'Muhammad ibn Musa al-Khawarizmi (~780-850 M) — bapa algebra dan algoritma; karyanya "Kitab al-Jabr" menjadi asas matematik moden.',
         'مؤسس علم الجبر؛ كتابه "الجبر والمقابلة" أصل لعلم الجبر الحديث.',
         'كلمتا "algebra" و"algorithm" مشتقتان من اسمه وعمله.',
         'العصر العباسي / بغداد', 'cendekiawan'],

        ['الحضارة الإسلامية (Tamadun Islam dalam Bahasa Arab)', 'tamadun',
         'ابن سينا (Ibn Sina)', 'Ibn Sina',
         'Avicenna (980-1037 M) — doktor dan ahli falsafah Parsi/Arab agung; karyanya "Al-Qanun fi al-Tibb" digunakan sebagai teks utama perubatan di universiti Eropah selama 600 tahun.',
         'الطبيب والفيلسوف المسلم؛ كتابه "القانون في الطب" مرجع طبي عالمي لقرون.',
         'كتاب الشفاء — موسوعة في الفلسفة والعلوم.',
         'العصر العباسي / بخارى', 'cendekiawan'],

        ['الحضارة الإسلامية (Tamadun Islam dalam Bahasa Arab)', 'tamadun',
         'ابن خلدون (Ibn Khaldun)', 'Ibn Khaldun',
         'Ibn Khaldun (1332-1406 M) — sejarawan dan ahli sosiologi Arab pertama; karyanya "Muqaddimah" meletakkan asas kepada ilmu sosiologi moden.',
         'مؤسس علم الاجتماع، صاحب "المقدمة" — أساس علم الاجتماع الحديث.',
         '"المقدمة" — تحليل لنشوء وانحلال الحضارات.',
         'العصر الإسلامي / المغرب', 'cendekiawan'],

        ['الحضارة الإسلامية (Tamadun Islam dalam Bahasa Arab)', 'tamadun',
         'بَيْتُ الحِكْمَةِ (Bayt al-Hikmah)', 'Bayt al-Hikmah',
         'Rumah Kebijaksanaan — institusi keilmuan agung di Baghdad pada era Abbasiyyah; pusat terjemahan karya Yunani, Parsi dan India ke bahasa Arab.',
         'مؤسسة علمية كبرى في بغداد العباسية، مركز ترجمة وعلوم.',
         'ترجم فيها أعمال أرسطو وأفلاطون وبطليموس إلى العربية.',
         'بغداد / العصر العباسي', 'institusi'],
    ];
}

function bahasa_arab_kssm_run(): array
{
    $subject = db_one("SELECT id FROM subjects WHERE slug = 'bahasa-arab' LIMIT 1");
    if (!$subject) {
        return ['status' => 'no_subject', 'message' => 'Bahasa Arab subject not found. Run install / seed_subjects first.'];
    }
    $sid = (int) $subject['id'];

    $refsTable = (bool) db_one("SELECT 1 AS x FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ba_references'");

    $catalog = bahasa_arab_kssm_catalog();
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
        foreach (bahasa_arab_kssm_references() as $idx => [$topicName, $type, $expr, $translit, $meaningBm, $meaningAr, $example, $origin, $category]) {
            $tid = $topicIds[$topicName] ?? null;
            $existing = db_one('SELECT id FROM ba_references WHERE expression = ? LIMIT 1', [$expr]);
            if ($existing) {
                $refsKept++;
            } else {
                db_exec(
                    'INSERT INTO ba_references (topic_id, topic_label, type, expression, transliteration, meaning_bm, meaning_ar, example, origin, category, sort_order)
                     VALUES (?,?,?,?,?,?,?,?,?,?,?)',
                    [$tid, $topicName, $type, $expr, $translit, $meaningBm, $meaningAr, $example, $origin, $category, $idx + 1]
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
    $r = bahasa_arab_kssm_run();
    if (($r['status'] ?? '') !== 'ok') {
        fwrite(STDERR, ($r['message'] ?? 'unknown error') . "\n");
        exit(1);
    }
    echo "KSSM Bahasa Arab seeded —\n";
    echo "  Topik:     created {$r['topics_created']}, adopted {$r['topics_adopted']}, kept {$r['topics_kept']} (catalog {$r['catalog_topics']})\n";
    echo "  Subtopik:  created {$r['subtopics_created']}, kept {$r['subtopics_kept']}\n";
    echo "  Kemahiran: created {$r['skills_created']}, kept {$r['skills_kept']}\n";
    if ($r['refs_table']) {
        echo "  Rujukan (Amthal + Nahu + Mufradat + Tokoh): created {$r['refs_created']}, kept {$r['refs_kept']}\n";
    } else {
        echo "  Rujukan: table missing — run database migrations to enable.\n";
    }
}
