<?php
/**
 * KSSM SPM Bahasa Tamil (தமிழ்மொழி) syllabus seeder.
 * Tingkatan 4 (7 topik) + Tingkatan 5 (7 topik) — 14 topik total
 * organised by macro skills mirip seed_bm_kssm.php dan seed_bahasa_
 * cina_kssm.php (bukannya unit textbook). Catalog dwi-bahasa Tamil
 * + BM untuk membantu pengguna pelbagai latar.
 *
 * Plus starter bank rujukan Tamil: திருக்குறள் (Thirukkural couplets),
 * பழமொழி (proverbs), tokoh sasterawan klasik dan moden (Valluvar,
 * Bharathiyar, Bharathidasan), karya klasik (Silappathikaram).
 *
 * Idempotent: topik matched by (subject_id, name, form_level); legacy
 * same-named NULL-form topik are adopted.
 *
 *   php public_html/cron/seed_bahasa_tamil_kssm.php
 */
declare(strict_types=1);

require_once __DIR__ . '/../inc/db.php';

function bahasa_tamil_kssm_catalog(): array
{
    return [
        // ============= TINGKATAN 4 =============
        ['form' => 4, 'name' => 'கேட்டல் பேசுதல் (Kemahiran Mendengar dan Bertutur)', 'subtopics' => [
            'செய்தி கேட்டல் (Mendengar Maklumat)',
            'பதில் அளித்தல் (Memberi Respons)',
            'கலந்துரையாடல் (Perbincangan)',
            'விளக்கக்காட்சி (Pembentangan)',
            'அதிகாரப்பூர்வ தொடர்பாடல் (Komunikasi Formal)',
        ], 'skills' => [
            ['முக்கியக் கருத்துக்களைப் புரிந்து கொள்ளுதல் (Mendengar maklumat penting)', 'easy'],
            ['கருத்துகளை வெளிப்படுத்துதல் (Menyampaikan pendapat)', 'medium'],
            ['சரியான தொடர்பாடல் (Berkomunikasi secara berkesan)', 'medium'],
        ]],
        ['form' => 4, 'name' => 'புரிதல் (Pemahaman)', 'subtopics' => [
            'பொது பத்தி (Petikan Umum)',
            'இலக்கியப் பத்தி (Petikan Sastera)',
            'KBAT வினாக்கள் (Soalan KBAT)',
            'முதன்மைக் கருத்து கண்டறிதல் (Mencari Idea Pokok)',
            'விளக்க வினாக்கள் (Soalan Inferens)',
        ], 'skills' => [
            ['முக்கியக் கருத்து கண்டறிதல் (Mencari isi penting)', 'medium'],
            ['உட்கருத்து புரிதல் (Membuat inferens)', 'medium'],
            ['KBAT வினாக்களுக்குப் பதில் (Menjawab soalan KBAT)', 'hard'],
        ]],
        ['form' => 4, 'name' => 'அடிப்படை இலக்கணம் (Tatabahasa Asas)', 'subtopics' => [
            'பெயர்ச்சொல் (Kata Nama)',
            'வினைச்சொல் (Kata Kerja)',
            'உரிச்சொல் (Kata Adjektif)',
            'இடைச்சொல் (Kata Tugas)',
            'வாக்கிய அமைப்பு (Struktur Ayat)',
            'வாக்கியப் பிழைகள் (Kesalahan Ayat)',
        ], 'skills' => [
            ['சொல் வகைகளைக் கண்டறிதல் (Mengenal pasti golongan kata)', 'easy'],
            ['சரியான வாக்கியங்கள் உருவாக்குதல் (Membina ayat gramatis)', 'medium'],
            ['வாக்கியப் பிழைகளைத் திருத்துதல் (Membetulkan kesalahan bahasa)', 'medium'],
        ]],
        ['form' => 4, 'name' => 'சிறு கட்டுரை (Karangan Pendek)', 'subtopics' => [
            'குறுகிய பதில் கட்டுரை (Karangan Respons Terhad)',
            'விளக்க கட்டுரை (Karangan Penerangan)',
            'கருத்துக் கட்டுரை (Karangan Pendapat)',
            'கடிதம் (Surat)',
        ], 'skills' => [
            ['கருத்துகளை ஒழுங்காகச் சேர்த்தல் (Menulis isi tersusun)', 'medium'],
            ['இணைப்புச் சொற்களைப் பயன்படுத்துதல் (Menggunakan penanda wacana)', 'medium'],
        ]],
        ['form' => 4, 'name' => 'வழிகாட்டப்பட்ட கட்டுரை (Karangan Berpandu)', 'subtopics' => [
            'விளக்கமான கட்டுரை (Karangan Deskriptif)',
            'நிகழ்ச்சி விளக்கம் (Karangan Naratif)',
            'வாதிடும் கட்டுரை (Karangan Hujahan)',
            'அறிக்கை (Karangan Laporan)',
            'பேச்சுரை (Karangan Ucapan)',
        ], 'skills' => [
            ['கட்டுரையின் கட்டமைப்பு திட்டமிடுதல் (Merancang isi)', 'medium'],
            ['கருத்துகளை விரிவாக்குதல் (Mengembangkan idea)', 'medium'],
            ['சரியான வடிவத்தைப் பின்பற்றுதல் (Menulis dalam format yang sesuai)', 'medium'],
        ]],
        ['form' => 4, 'name' => 'இலக்கியம் — தொடக்கம் (Kesusasteraan Tamil — Tingkatan 4)', 'subtopics' => [
            'திருக்குறள் தேர்வுகள் (Petikan Thirukkural)',
            'நாட்டுப்புறப் பாடல்கள் (Lagu Rakyat / Folk Songs)',
            'புதுக்கவிதை (Puisi Moden / New Poetry)',
            'சிறுகதை (Cerpen / Short Story)',
            'நாடகம் (Drama)',
        ], 'skills' => [
            ['இலக்கியப் படைப்புகளை அலசுதல் (Menganalisis karya)', 'medium'],
            ['இலக்கியக் கேள்விகளுக்குப் பதில் (Menjawab soalan KOMSAS)', 'medium'],
        ]],
        ['form' => 4, 'name' => 'பழமொழி & மரபுத்தொடர் (Peribahasa dan Frasa Tradisional)', 'subtopics' => [
            'பழமொழிகள் (Peribahasa)',
            'மரபுத்தொடர் (Frasa Idiomatik)',
            'அர்த்தம் & பயன்பாடு (Makna dan Penggunaan)',
            'வாக்கியத்தில் பயன்படுத்துதல் (Mengaplikasikan dalam Ayat)',
        ], 'skills' => [
            ['பழமொழிகளை விளக்குதல் (Menjelaskan makna peribahasa)', 'medium'],
            ['வாக்கியங்களில் பயன்படுத்துதல் (Mengaplikasikan dalam ayat)', 'medium'],
        ]],

        // ============= TINGKATAN 5 =============
        ['form' => 5, 'name' => 'KBAT புரிதல் (Pemahaman KBAT)', 'subtopics' => [
            'பகுப்பாய்வு (Analisis)',
            'மதிப்பீடு (Penilaian)',
            'பிரச்சினை தீர்த்தல் (Penyelesaian Masalah)',
            'விமர்சனப் படிப்பு (Pembacaan Kritis)',
        ], 'skills' => [
            ['அதிகச் சிந்தனை வினாக்கள் (Menjawab soalan aras tinggi)', 'hard'],
            ['நியாயம் வழங்குதல் (Membuat justifikasi)', 'medium'],
        ]],
        ['form' => 5, 'name' => 'மேம்பட்ட இலக்கணம் (Tatabahasa Lanjutan)', 'subtopics' => [
            'கூட்டுச் சொற்கள் (Kata Majmuk)',
            'வேற்றுமை உருபுகள் (Kata Imbuhan)',
            'வழக்கு மொழி (Bahasa Lisan vs Formal)',
            'மரபுத்தொடர் & பழமொழி (Idiom dan Peribahasa)',
            'பிழை திருத்தம் (Pembetulan Kesalahan)',
        ], 'skills' => [
            ['சரியான பழமொழியைத் தேர்வு (Memilih peribahasa sesuai)', 'medium'],
            ['வாக்கியங்களில் பயன்படுத்துதல் (Menggunakan dalam ayat)', 'medium'],
            ['பிழைகளைத் திருத்துதல் (Membetulkan kesalahan tatabahasa)', 'medium'],
        ]],
        ['form' => 5, 'name' => 'வாதிடும் கட்டுரை (Karangan Hujahan)', 'subtopics' => [
            'வாதம் வைத்தல் (Menyatakan Tesis)',
            'ஆதாரம் வழங்குதல் (Memberi Bukti)',
            'எதிர்ப்புக் கருத்து (Bantahan)',
            'முடிவுரை (Penutup)',
        ], 'skills' => [
            ['தெளிவான வாதங்கள் வைத்தல் (Menyatakan hujah yang jelas)', 'hard'],
            ['ஆதாரத்துடன் வாதம் (Menyokong dengan bukti)', 'hard'],
            ['முழுமையான கட்டுரை (Menulis karangan lengkap)', 'hard'],
        ]],
        ['form' => 5, 'name' => 'நிகழ்ச்சி கட்டுரை & உணர்வுக் கட்டுரை (Karangan Naratif dan Emosi)', 'subtopics' => [
            'கதைப் பிரிவு (Plot Cerita)',
            'பாத்திரப் படைப்பு (Pembinaan Watak)',
            'காட்சி வருணனை (Penggambaran Latar)',
            'உணர்ச்சி வெளிப்பாடு (Penyataan Emosi)',
            'திருக்குறள் மேற்கோள் (Petikan Thirukkural)',
        ], 'skills' => [
            ['முழுமையான கதை அமைத்தல் (Membina plot yang utuh)', 'medium'],
            ['உயிரோட்டமான வருணனை (Menggunakan penggambaran hidup)', 'hard'],
            ['உண்மையான உணர்வுகளை வெளிப்படுத்துதல் (Menyampaikan emosi tulus)', 'medium'],
        ]],
        ['form' => 5, 'name' => 'சுருக்க எழுத்து (Ringkasan)', 'subtopics' => [
            'முக்கியக் கருத்துக்களைப் பெறுதல் (Mengenal pasti idea utama)',
            'மாற்றியெழுதுதல் (Paraphrasing)',
            'சொற்களின் எண்ணிக்கை (Word Limit)',
            'கருத்துக்களை இணைத்தல் (Menghubung Idea)',
        ], 'skills' => [
            ['சுருக்கமாக எழுதுதல் (Menulis ringkasan padat)', 'hard'],
            ['சரியாக மாற்றியெழுதுதல் (Paraphrase dengan tepat)', 'hard'],
            ['சொல் எண்ணிக்கையைப் பேணுதல் (Mematuhi had perkataan)', 'medium'],
        ]],
        ['form' => 5, 'name' => 'திருக்குறள் & பாரம்பரிய இலக்கியம் (Thirukkural dan Sastera Klasik)', 'subtopics' => [
            'திருக்குறள் அமைப்பு (Struktur Thirukkural — 1330 kural)',
            'அறம் (Aram / Virtue)',
            'பொருள் (Porul / Wealth & Statecraft)',
            'இன்பம் (Inbam / Love)',
            'சங்க இலக்கியம் (Sastera Sangam)',
            'சிலப்பதிகாரம் (Silappathikaram)',
        ], 'skills' => [
            ['திருக்குறளின் கருத்துக்களை விளக்குதல் (Menjelaskan makna Thirukkural)', 'hard'],
            ['பாரம்பரிய இலக்கியம் அலசுதல் (Menganalisis sastera klasik)', 'hard'],
            ['நெறிமுறைப் பாடம் பெறுதல் (Mengekstrak pengajaran moral)', 'medium'],
        ]],
        ['form' => 5, 'name' => 'நவீன இலக்கியம் (Sastera Tamil Moden)', 'subtopics' => [
            'பாரதியார் கவிதைகள் (Puisi Bharathiyar)',
            'பாரதிதாசன் (Bharathidasan)',
            'நவீன சிறுகதை (Cerpen Moden)',
            'புதுக்கவிதை (Puisi Moden)',
            'நாடகம் & உரைநடை (Drama dan Prosa)',
        ], 'skills' => [
            ['நவீன படைப்புகளை அலசுதல் (Menganalisis karya moden)', 'medium'],
            ['எழுத்தாளரின் கருத்தைப் புரிதல் (Memahami pandangan penulis)', 'medium'],
            ['இலக்கியக் கேள்விகளுக்குப் பதிலளித்தல் (Menjawab soalan KOMSAS)', 'medium'],
        ]],
    ];
}

/**
 * Starter Bahasa Tamil references — Thirukkural couplets, Tamil
 * proverbs, classical and modern authors.
 */
function bahasa_tamil_kssm_references(): array
{
    return [
        // topic, type, expression, transliteration, meaning_bm, meaning_ta, example, origin, category

        // ===== திருக்குறள் / Thirukkural couplets =====
        ['திருக்குறள் & பாரம்பரிய இலக்கியம் (Thirukkural dan Sastera Klasik)', 'kural',
         'அகர முதல எழுத்தெல்லாம் ஆதி பகவன் முதற்றே உலகு',
         'Agara mudhala ezhuthellaam aathi bagavan mudhatre ulagu',
         'Semua huruf bermula dengan "A" (akaram); dunia bermula dengan Tuhan yang abadi.',
         'எல்லா எழுத்துக்களின் தொடக்கம் "அ", எல்லா உலகத்திற்கும் ஆதியான கடவுள் முதல்.',
         'Kural 1 — pembuka Thirukkural. Sering dipetik untuk pengenalan.',
         'திருவள்ளுவர் / Thiruvalluvar', 'aram'],

        ['திருக்குறள் & பாரம்பரிய இலக்கியம் (Thirukkural dan Sastera Klasik)', 'kural',
         'கற்க கசடறக் கற்பவை கற்றபின் நிற்க அதற்குத் தக',
         'Karka kasadara karpavai karrapin nirka adharkuth thaga',
         'Pelajari sesuatu dengan teliti tanpa kekeliruan; setelah belajar, hidup setia kepada apa yang dipelajari.',
         'குற்றமற்றுக் கற்க வேண்டியதைக் கற்று, கற்றதன்படி நடக்க வேண்டும்.',
         'Kural 391 — Bab tentang ilmu (கல்வி).',
         'திருவள்ளுவர் / Thiruvalluvar', 'aram'],

        ['திருக்குறள் & பாரம்பரிய இலக்கியம் (Thirukkural dan Sastera Klasik)', 'kural',
         'பிறப்பொக்கும் எல்லா உயிர்க்கும் சிறப்பொவ்வா செய்தொழில் வேற்றுமையான்',
         'Pirappokkum ellaa uyirkkum sirappovvaa seythozhil verrumaiyaan',
         'Semua nyawa lahir sama; perbezaan kemuliaan datang daripada pekerjaan yang dilakukan.',
         'எல்லா உயிர்களும் பிறப்பால் சமம்; சிறப்பு தொழிலின் வேறுபாட்டால் வரும்.',
         'Kural 972 — sering disebut untuk kesaksamaan sosial.',
         'திருவள்ளுவர் / Thiruvalluvar', 'aram'],

        ['திருக்குறள் & பாரம்பரிய இலக்கியம் (Thirukkural dan Sastera Klasik)', 'kural',
         'கல்லாதவனாகி உய்ய நினைப்பான் அரிய அறங்களை ஆற்றமாட்டான்',
         'Kallaadhavanaagi uyya ninaippaan ariya arangalai aarramaattaan',
         'Orang yang tidak berilmu lalu mahu mencapai kebebasan tidak akan mampu melakukan kebajikan yang sukar.',
         'கல்வி இல்லாமல் வாழ்வை மேம்படுத்த நினைப்பவன் கடினமான நற்செயல்களைச் செய்ய முடியாது.',
         'Kural 134 — pentingnya pendidikan.',
         'திருவள்ளுவர் / Thiruvalluvar', 'aram'],

        ['திருக்குறள் & பாரம்பரிய இலக்கியம் (Thirukkural dan Sastera Klasik)', 'kural',
         'அறன்வரையான் அல்ல செயினும் பிறன்வரையாள் பெண்மை நயவாமை நன்று',
         'Aranvaraiyaan alla seyinum piranvaraiyaal penmai nayavaamai nandru',
         'Walaupun seseorang melakukan perkara yang tidak adil, lebih baik dia tidak mengingini isteri orang lain.',
         'அறம் தவறிய செயல் செய்தாலும் பிறர் மனைவியை விரும்பாமை சிறந்தது.',
         'Kural 148 — bab tentang kesetiaan.',
         'திருவள்ளுவர் / Thiruvalluvar', 'aram'],

        ['திருக்குறள் & பாரம்பரிய இலக்கியம் (Thirukkural dan Sastera Klasik)', 'kural',
         'அன்பின் வழியது உயிர்நிலை அஃதிலார்க்கு என்புதோல் போர்த்த உடம்பு',
         'Anbin vazhiyadhu uyirnilai ahdhilaarkku enbuthol porththa udambu',
         'Hidup yang bermakna adalah hidup atas dasar kasih sayang; tanpa kasih sayang, badan hanyalah tulang yang terbalut kulit.',
         'அன்பின் வழியில் உள்ளதே உயிர் வாழ்வு; அன்பு இல்லாதவர்க்கு உடல் எலும்பும் தோலும் மட்டுமே.',
         'Kural 80 — bab tentang kasih sayang (அன்பு).',
         'திருவள்ளுவர் / Thiruvalluvar', 'aram'],

        ['திருக்குறள் & பாரம்பரிய இலக்கியம் (Thirukkural dan Sastera Klasik)', 'kural',
         'செய்க பொருளைச் செறுநர் செருக்கறுக்கும் எஃகதனிற் கூரிய தில்',
         'Seyga porulai serunar serukkarukkum ehkadhanir kooriya thil',
         'Kumpulkanlah kekayaan; tiada senjata yang lebih tajam untuk mematahkan kesombongan musuh.',
         'பொருளை சேமி; எதிரியின் கர்வத்தை அழிக்க இதைவிட கூர்மையான ஆயுதம் இல்லை.',
         'Kural 759 — bab tentang kekayaan (பொருள் / porul).',
         'திருவள்ளுவர் / Thiruvalluvar', 'porul'],

        ['திருக்குறள் & பாரம்பரிய இலக்கியம் (Thirukkural dan Sastera Klasik)', 'kural',
         'வையத்துள் வாழ்வாங்கு வாழ்பவன் வானுறையும் தெய்வத்துள் வைக்கப்படும்',
         'Vaiyathul vaazhvaangu vaazhpavan vaanuraiyum dheivathul vaikkappadum',
         'Orang yang hidup dengan baik di dunia ini akan dikira sebagai dewa di kayangan.',
         'இவ்வுலகில் சிறப்பாக வாழ்பவன் சொர்க்க தெய்வங்களுக்கிடையே வைக்கப்படுவான்.',
         'Kural 50 — hidup yang baik di dunia disamakan dengan dewa-dewi.',
         'திருவள்ளுவர் / Thiruvalluvar', 'aram'],

        // ===== பழமொழி / Tamil proverbs =====
        ['பழமொழி & மரபுத்தொடர் (Peribahasa dan Frasa Tradisional)', 'pazhamozhi',
         'அரசன் அன்று கொல்லும் தெய்வம் நின்று கொல்லும்',
         'Arasan andru kollum dheivam nindru kollum',
         'Raja boleh menghukum mati hari ini; Tuhan menghukum dengan perlahan-lahan.',
         'அரசன் உடனே தண்டிப்பான்; ஆனால் தெய்வம் நிதானமாக தண்டிக்கும்.',
         'Pengajaran: keadilan ilahi datang lambat tapi pasti.',
         'பழமொழி', 'pengajaran'],

        ['பழமொழி & மரபுத்தொடர் (Peribahasa dan Frasa Tradisional)', 'pazhamozhi',
         'அளவுக்கு மீறினால் அமிர்தமும் நஞ்சு',
         'Alavukku meerinaal amirthamum nanju',
         'Walaupun nektar para dewa, jika berlebihan ia menjadi racun.',
         'அளவு மீறிப் பயன்படுத்தினால் அமிர்தமும் விஷமாகும்.',
         'Pengajaran: kesederhanaan dalam semua perkara.',
         'பழமொழி', 'kesederhanaan'],

        ['பழமொழி & மரபுத்தொடர் (Peribahasa dan Frasa Tradisional)', 'pazhamozhi',
         'அகத்தின் அழகு முகத்தில் தெரியும்',
         'Agathin azhagu mugaththil theriyum',
         'Keindahan hati terpancar pada wajah.',
         'மனதின் அழகு முகத்தில் தெரியும்.',
         'Pengajaran: sifat batin terpancar pada lahir.',
         'பழமொழி', 'akhlak'],

        ['பழமொழி & மரபுத்தொடர் (Peribahasa dan Frasa Tradisional)', 'pazhamozhi',
         'காலம் தாழ்த்தினால் கடினம் ஆகும்',
         'Kaalam thaazhththinaal kadinam aagum',
         'Jika ditunda-tunda, hal yang mudah menjadi sukar.',
         'நேரத்தைத் தாமதப்படுத்தினால் வேலை கடினமாகும்.',
         'Pengajaran: bertindak segera, jangan tangguh.',
         'பழமொழி', 'masa'],

        ['பழமொழி & மரபுத்தொடர் (Peribahasa dan Frasa Tradisional)', 'pazhamozhi',
         'மீண்டும் வந்த துணியில் எலியும் சேரும்',
         'Meendum vandha thuniyil eliyum serum',
         'Walau pakaian usang yang dikembalikan, tikus juga akan menumpang.',
         'மீண்டும் வந்த ஆடையில் தேவையற்றவர்களும் சேர்வர்.',
         'Pengajaran: barang/orang yang dianggap remeh kerap diabaikan.',
         'பழமொழி', 'sosial'],

        ['பழமொழி & மரபுத்தொடர் (Peribahasa dan Frasa Tradisional)', 'pazhamozhi',
         'கற்றது கைமண் அளவு, கல்லாதது உலகளவு',
         'Katrathu kaimann alavu kallaadhadhu ulagalavu',
         'Apa yang telah dipelajari hanyalah segenggam pasir; apa yang belum dipelajari adalah sebesar dunia.',
         'கற்றது ஒரு கை மண்ணளவு; கல்லாதது இந்த உலகளவு பெரிதாகும்.',
         'Pengajaran: rendah diri tentang ilmu kita; pembelajaran sepanjang hayat.',
         'ஒளவையார் / Avvaiyar', 'pembelajaran'],

        ['பழமொழி & மரபுத்தொடர் (Peribahasa dan Frasa Tradisional)', 'pazhamozhi',
         'பெரியோர் சொல் பெரும் தலைவலி',
         'Periyor sol perum thalaivali',
         'Nasihat orang tua kadang-kadang dirasakan sebagai sakit kepala.',
         'பெரியவர்களின் அறிவுரை சிலருக்கு தலைவலி போல் தோன்றும்.',
         'Pengajaran: nasihat orang tua mungkin tidak menyenangkan tetapi berharga.',
         'பழமொழி', 'pengajaran'],

        ['பழமொழி & மரபுத்தொடர் (Peribahasa dan Frasa Tradisional)', 'pazhamozhi',
         'ஓடி உழைக்காதவன் ஊர்வசியை விரும்பினான்',
         'Odi uzhaikkaadhavan oorvasiyai virumbinaan',
         'Orang yang enggan berlari dan bekerja keras inginkan Urvasi (bidadari kahyangan).',
         'வேலை செய்யத் தயங்குபவன் பெரிய பரிசை எதிர்பார்க்கிறான்.',
         'Pengajaran: tiada hasil tanpa usaha.',
         'பழமொழி', 'usaha'],

        ['பழமொழி & மரபுத்தொடர் (Peribahasa dan Frasa Tradisional)', 'pazhamozhi',
         'காசு போனாலும் ஆசை போகாது',
         'Kaasu ponaalum aasai pogaadhu',
         'Walaupun wang habis, hasrat tidak akan habis.',
         'பணம் தீர்ந்தாலும் ஆசை தீராது.',
         'Pengajaran: keinginan manusia tiada batas.',
         'பழமொழி', 'akhlak'],

        // ===== Tokoh klasik =====
        ['திருக்குறள் & பாரம்பரிய இலக்கியம் (Thirukkural dan Sastera Klasik)', 'author',
         'திருவள்ளுவர் / Thiruvalluvar',
         'Thiruvalluvar',
         'Penyair-falsafah agung Tamil; mengarang Thirukkural (~5 SM – 1 M). Karyanya mengandungi 1,330 kural dalam tiga bab utama: அறம் (kebajikan), பொருள் (kekayaan/pemerintahan), இன்பம் (cinta).',
         'தமிழின் தலைசிறந்த தத்துவ கவிஞர், திருக்குறளை இயற்றியவர்.',
         'Thirukkural diterjemahkan ke lebih 80 bahasa dunia.',
         'Era Sangam', 'penyair_klasik'],

        ['திருக்குறள் & பாரம்பரிய இலக்கியம் (Thirukkural dan Sastera Klasik)', 'author',
         'ஒளவையார் / Avvaiyar',
         'Avvaiyar',
         'Penyair wanita Tamil yang ulung — terkenal dengan karya pendidikan moral untuk kanak-kanak (ஆத்திசூடி, கொன்றை வேந்தன், மூதுரை, நல்வழி).',
         'தமிழ் இலக்கியத்தின் மிகச்சிறந்த பெண் கவிஞர்.',
         '"ஆலயம் தொழுவது சாலவும் நன்று" — "menyembah di kuil adalah amat baik".',
         'Era Sangam', 'penyair_klasik'],

        ['திருக்குறள் & பாரம்பரிய இலக்கியம் (Thirukkural dan Sastera Klasik)', 'classical_work',
         'சிலப்பதிகாரம் / Silappathikaram',
         'Silappathikaram',
         'Salah satu daripada Lima Wira Tamil — epik puitis oleh Ilango Adigal yang mengisahkan Kannagi dan Kovalan dengan tema keadilan dan kesetiaan.',
         'தமிழின் ஐம்பெருங்காப்பியங்களில் ஒன்று; இளங்கோ அடிகள் இயற்றியது.',
         'Bahagian: Pugar Kandam, Madurai Kandam, Vanchi Kandam.',
         'Era Sangam Terakhir', 'karya_klasik'],

        ['திருக்குறள் & பாரம்பரிய இலக்கியம் (Thirukkural dan Sastera Klasik)', 'classical_work',
         'மணிமேகலை / Manimekalai',
         'Manimekalai',
         'Salah satu dari Lima Wira Tamil; karangan Seethalai Saathanar. Sambungan Silappathikaram, dengan tema Buddhisme dan kemanusiaan.',
         'ஐம்பெருங்காப்பியங்களில் ஒன்று; சீத்தலைச் சாத்தனார் இயற்றியது.',
         'Mengisahkan kehidupan Manimekalai, anak perempuan Madhavi dan Kovalan.',
         'Era Sangam Terakhir', 'karya_klasik'],

        // ===== Tokoh moden =====
        ['நவீன இலக்கியம் (Sastera Tamil Moden)', 'author',
         'பாரதியார் / Bharathiyar',
         'Subramania Bharati',
         'Penyair revolusioner Tamil (1882-1921); digelar மகாகவி (Maha Kavi) — Penyair Agung. Karyanya menyerang sistem kasta, menyokong pembebasan India dan kesaksamaan wanita.',
         'நவீன தமிழ் இலக்கியத்தின் முகவராக கருதப்படும் மகாகவி.',
         '"காக்கை குருவி எங்கள் ஜாதி" — "Gagak dan burung-pipit adalah kaum kami".',
         'Era pra-kemerdekaan India', 'pengarang_moden'],

        ['நவீன இலக்கியம் (Sastera Tamil Moden)', 'author',
         'பாரதிதாசன் / Bharathidasan',
         'Bharathidasan',
         'Penyair Tamil moden (1891-1964) — murid Bharathiyar. Karyanya menekankan reformasi sosial, hak-hak wanita dan keadilan kasta.',
         'பாரதியின் சீடர், சமூக சீர்திருத்தத்திற்காக எழுதியவர்.',
         '"வேதம் அனைத்தும் தமிழ்செய்தேன்" — "Kuubahkan semua Veda kepada Tamil".',
         'Era moden India', 'pengarang_moden'],

        ['நவீன இலக்கியம் (Sastera Tamil Moden)', 'author',
         'புதுமைப்பித்தன் / Pudhumaipithan',
         'Pudhumaipithan',
         'Pengarang cerpen Tamil moden (1906-1948); dianggap sebagai pengasas cerpen Tamil moden. Karyanya kerap mengupas ketidakadilan sosial dengan gaya satir.',
         'நவீன தமிழ் சிறுகதையின் தந்தை.',
         '"காஞ்சனை", "சாபவிமோசனம்", "துன்பக்கேணி".',
         'Era moden Tamil Nadu', 'pengarang_moden'],

        ['நவீன இலக்கியம் (Sastera Tamil Moden)', 'author',
         'கல்கி / Kalki Krishnamurthy',
         'Kalki Krishnamurthy',
         'Novelis Tamil (1899-1954) — terkenal dengan novel-novel sejarah seperti Ponniyin Selvan, Sivagamiyin Sapatham, Parthiban Kanavu.',
         'வரலாற்று நாவல்களின் முடிசூடா மன்னன்.',
         '"பொன்னியின் செல்வன்" — Ponniyin Selvan, novel sejarah agung Chola.',
         'Era moden India', 'pengarang_moden'],
    ];
}

function bahasa_tamil_kssm_run(): array
{
    $subject = db_one("SELECT id FROM subjects WHERE slug = 'bahasa-tamil' LIMIT 1");
    if (!$subject) {
        return ['status' => 'no_subject', 'message' => 'Bahasa Tamil subject not found. Run install / seed_subjects first.'];
    }
    $sid = (int) $subject['id'];

    $refsTable = (bool) db_one("SELECT 1 AS x FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'bt_references'");

    $catalog = bahasa_tamil_kssm_catalog();
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
        foreach (bahasa_tamil_kssm_references() as $idx => [$topicName, $type, $expr, $translit, $meaningBm, $meaningTa, $example, $origin, $category]) {
            $tid = $topicIds[$topicName] ?? null;
            $existing = db_one('SELECT id FROM bt_references WHERE expression = ? LIMIT 1', [$expr]);
            if ($existing) {
                $refsKept++;
            } else {
                db_exec(
                    'INSERT INTO bt_references (topic_id, topic_label, type, expression, transliteration, meaning_bm, meaning_ta, example, origin, category, sort_order)
                     VALUES (?,?,?,?,?,?,?,?,?,?,?)',
                    [$tid, $topicName, $type, $expr, $translit, $meaningBm, $meaningTa, $example, $origin, $category, $idx + 1]
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
    $r = bahasa_tamil_kssm_run();
    if (($r['status'] ?? '') !== 'ok') {
        fwrite(STDERR, ($r['message'] ?? 'unknown error') . "\n");
        exit(1);
    }
    echo "KSSM Bahasa Tamil seeded —\n";
    echo "  Topik:     created {$r['topics_created']}, adopted {$r['topics_adopted']}, kept {$r['topics_kept']} (catalog {$r['catalog_topics']})\n";
    echo "  Subtopik:  created {$r['subtopics_created']}, kept {$r['subtopics_kept']}\n";
    echo "  Kemahiran: created {$r['skills_created']}, kept {$r['skills_kept']}\n";
    if ($r['refs_table']) {
        echo "  Rujukan (Thirukkural + Pazhamozhi + Tokoh): created {$r['refs_created']}, kept {$r['refs_kept']}\n";
    } else {
        echo "  Rujukan: table missing — run database migrations to enable.\n";
    }
}
