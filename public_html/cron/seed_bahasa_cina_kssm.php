<?php
/**
 * KSSM SPM Bahasa Cina (华文) syllabus seeder.
 * Tingkatan 4 (7 topik — pemahaman moden, wenyan, tatabahasa, karangan
 * pendek, karangan berpandu, karangan berformat, KOMSAS moden) +
 * Tingkatan 5 (7 topik — pemahaman lanjutan, terjemahan wenyan,
 * ringkasan, karangan hujahan, karangan naratif, puisi klasik,
 * karya klasik) — 14 topik total, dengan starter bank 成语 chengyu
 * dan wenyan virtual words yang digunakan oleh AI Bahasa Cina
 * Trainer dan flashcard hafazan.
 *
 * Catalog organised mirip seed_bm_kssm.php (macro skills) bukannya
 * 10 unit textbook supaya AI question generation dapat fokus pada
 * kemahiran ujian SPM (Kertas 1 karangan, Kertas 2 pemahaman/wenyan/
 * terjemahan, Kertas 3 lisan).
 *
 * Idempotent: topik matched by (subject_id, name, form_level); legacy
 * same-named NULL-form topik are adopted.
 *
 *   php public_html/cron/seed_bahasa_cina_kssm.php
 */
declare(strict_types=1);

require_once __DIR__ . '/../inc/db.php';

function bahasa_cina_kssm_catalog(): array
{
    return [
        // ============= TINGKATAN 4 =============
        ['form' => 4, 'name' => '现代汉语阅读理解 (Pemahaman Bahasa Cina Moden)', 'subtopics' => [
            '说明文阅读 (Pemahaman Petikan Penerangan)',
            '记叙文阅读 (Pemahaman Petikan Naratif)',
            '议论文阅读 (Pemahaman Petikan Hujahan)',
            '词汇运用 (Penggunaan Kosa Kata)',
            '主旨与中心思想 (Idea Pokok dan Tema)',
        ], 'skills' => [
            ['辨识文章主旨 (Mengenal pasti idea pokok petikan)', 'medium'],
            ['推断隐含意义 (Membuat inferens)', 'medium'],
            ['运用词汇于句子 (Mengaplikasikan kosa kata)', 'medium'],
        ]],
        ['form' => 4, 'name' => '文言文阅读 (Pembacaan Wenyan / Bahasa Klasik)', 'subtopics' => [
            '文言虚词 (Kata Tugas Klasik — 之, 其, 而, 以, 也)',
            '文言实词 (Kata Penuh Klasik)',
            '一词多义 (Makna Berbilang Satu Kata)',
            '古今异义 (Perbezaan Makna Lama dan Baharu)',
            '短篇文言阅读 (Pemahaman Petikan Wenyan Pendek)',
        ], 'skills' => [
            ['识别文言虚词的用法 (Mengenal pasti penggunaan kata tugas klasik)', 'hard'],
            ['翻译文言句子 (Menterjemah ayat wenyan)', 'hard'],
            ['理解古文大意 (Memahami maksud umum karya klasik)', 'medium'],
        ]],
        ['form' => 4, 'name' => '语法基础 (Tatabahasa Asas)', 'subtopics' => [
            '词类 (Jenis Kata — 名词, 动词, 形容词, 副词)',
            '句子成分 (Komponen Ayat)',
            '常见句型 (Jenis Ayat Lazim)',
            '关联词的使用 (Penggunaan Kata Hubung)',
            '改正病句 (Pembetulan Ayat Salah)',
        ], 'skills' => [
            ['辨别词类与句子成分 (Mengenal pasti jenis kata dan komponen ayat)', 'medium'],
            ['运用关联词造句 (Membina ayat dengan kata hubung)', 'medium'],
            ['改正病句 (Membetulkan ayat salah)', 'medium'],
        ]],
        ['form' => 4, 'name' => '短文写作 (Karangan Pendek)', 'subtopics' => [
            '日记写作 (Penulisan Diari)',
            '便条与通知 (Nota dan Notis)',
            '简短书信 (Surat Pendek)',
            '段落写作 (Penulisan Perenggan)',
            '描写性段落 (Perenggan Deskriptif)',
        ], 'skills' => [
            ['撰写不同类型的短文 (Menulis pelbagai jenis karangan pendek)', 'medium'],
            ['运用恰当的格式 (Mengaplikasikan format yang sesuai)', 'easy'],
            ['表达清晰的观点 (Menyampaikan idea dengan jelas)', 'medium'],
        ]],
        ['form' => 4, 'name' => '命题作文 (Karangan Berpandu)', 'subtopics' => [
            '记叙文 (Karangan Naratif)',
            '说明文 (Karangan Penerangan)',
            '议论文入门 (Karangan Hujahan Pengenalan)',
            '抒情文 (Karangan Penyampaian Perasaan)',
            '段落组织与过渡 (Organisasi Perenggan dan Peralihan)',
        ], 'skills' => [
            ['构思写作大纲 (Merangka karangan)', 'medium'],
            ['展开主题段落 (Mengembangkan perenggan)', 'medium'],
            ['运用过渡句衔接段落 (Menggunakan ayat penghubung)', 'medium'],
        ]],
        ['form' => 4, 'name' => '应用文 (Karangan Berformat)', 'subtopics' => [
            '通告与启事 (Notis dan Pengumuman)',
            '演讲稿 (Teks Ucapan)',
            '广播稿 (Skrip Siaran)',
            '报告 (Laporan)',
            '会议记录 (Minit Mesyuarat)',
        ], 'skills' => [
            ['遵循应用文格式 (Mematuhi format karangan berformat)', 'medium'],
            ['使用恰当的语体 (Menggunakan gaya bahasa yang sesuai)', 'medium'],
            ['撰写实用性应用文 (Menulis karangan berformat praktikal)', 'medium'],
        ]],
        ['form' => 4, 'name' => '现代华文学 (KOMSAS Moden — Tingkatan 4)', 'subtopics' => [
            '现代散文 (Esei Moden)',
            '现代小说 (Novel Moden)',
            '现代诗歌 (Puisi Moden)',
            '人物分析 (Analisis Watak)',
            '主题思想分析 (Analisis Tema)',
        ], 'skills' => [
            ['分析作品的主题与人物 (Menganalisis tema dan watak)', 'medium'],
            ['赏析现代文学作品 (Mengapresiasi karya sastera moden)', 'medium'],
            ['作答KOMSAS问题 (Menjawab soalan KOMSAS)', 'medium'],
        ]],

        // ============= TINGKATAN 5 =============
        ['form' => 5, 'name' => '进阶阅读理解 (Pemahaman Lanjutan)', 'subtopics' => [
            '长篇阅读 (Pemahaman Petikan Panjang)',
            '推理与分析 (Inferens dan Analisis)',
            '作者观点 (Pandangan Penulis)',
            '修辞手法 (Bahasa Retorik / Bahasa Kiasan)',
            '高思维阅读 (Pemahaman KBAT)',
        ], 'skills' => [
            ['进行批判性阅读 (Membaca secara kritis)', 'hard'],
            ['辨识修辞手法 (Mengenal pasti bahasa kiasan)', 'medium'],
            ['作答高思维问题 (Menjawab soalan KBAT)', 'hard'],
        ]],
        ['form' => 5, 'name' => '文言翻译与理解 (Terjemahan dan Pemahaman Wenyan)', 'subtopics' => [
            '文言文翻译技巧 (Teknik Terjemahan Wenyan)',
            '文言句式 (Struktur Ayat Klasik)',
            '常见文言名句 (Ayat-ayat Wenyan Lazim)',
            '《论语》选读 (Petikan dari Analek Confucius)',
            '《孟子》选读 (Petikan dari Mengzi)',
        ], 'skills' => [
            ['准确翻译文言句子 (Menterjemah ayat wenyan dengan tepat)', 'hard'],
            ['理解经典文言著作 (Memahami karya klasik)', 'hard'],
            ['作答文言文综合题 (Menjawab soalan wenyan)', 'hard'],
        ]],
        ['form' => 5, 'name' => '摘要写作 (Ringkasan)', 'subtopics' => [
            '提炼要点 (Menyaring Idea Utama)',
            '改写技巧 (Teknik Mengubah Suai)',
            '字数控制 (Mengawal Bilangan Perkataan)',
            '语言简洁 (Bahasa Ringkas)',
            '结构清晰 (Struktur Jelas)',
        ], 'skills' => [
            ['提炼文章要点 (Mengenal pasti idea utama)', 'medium'],
            ['撰写简洁的摘要 (Menulis ringkasan padat)', 'hard'],
            ['控制字数限制 (Mematuhi had perkataan)', 'medium'],
        ]],
        ['form' => 5, 'name' => '议论文写作 (Karangan Hujahan)', 'subtopics' => [
            '论点确立 (Pernyataan Tesis)',
            '论据组织 (Penyusunan Bukti)',
            '论证方法 (Kaedah Hujah)',
            '反驳与回应 (Bantahan dan Respons)',
            '结论提升 (Penutup yang Meyakinkan)',
        ], 'skills' => [
            ['提出明确论点 (Menyatakan tesis jelas)', 'medium'],
            ['运用论据支持论点 (Menyokong tesis dengan bukti)', 'hard'],
            ['撰写完整议论文 (Menulis karangan hujahan lengkap)', 'hard'],
        ]],
        ['form' => 5, 'name' => '记叙文与抒情文写作 (Karangan Naratif dan Emosi)', 'subtopics' => [
            '记叙的六要素 (Enam Unsur Naratif)',
            '人物刻画 (Penggambaran Watak)',
            '场景描写 (Penggambaran Latar)',
            '抒情技巧 (Teknik Menyampaikan Emosi)',
            '运用成语与修辞 (Penggunaan Chengyu dan Bahasa Kiasan)',
        ], 'skills' => [
            ['构建完整的故事情节 (Membina plot lengkap)', 'medium'],
            ['运用生动的描写 (Menggunakan penggambaran hidup)', 'hard'],
            ['表达真挚情感 (Menyampaikan emosi tulus)', 'medium'],
        ]],
        ['form' => 5, 'name' => '古典诗词 (Puisi Klasik)', 'subtopics' => [
            '唐诗赏析 (Apresiasi Puisi Dinasti Tang)',
            '宋词赏析 (Apresiasi Ci Dinasti Song)',
            '诗词格律 (Peraturan Puisi Klasik)',
            '名家作品 (Karya Penyair Terkenal — 李白, 杜甫, 王维, 苏轼)',
            '主题与意境 (Tema dan Suasana)',
        ], 'skills' => [
            ['理解古典诗词的意境 (Memahami suasana puisi klasik)', 'hard'],
            ['分析诗词的主题 (Menganalisis tema puisi)', 'medium'],
            ['辨识修辞手法 (Mengenal pasti gaya bahasa)', 'medium'],
        ]],
        ['form' => 5, 'name' => '古典文学经典 (Karya Klasik Cina)', 'subtopics' => [
            '《论语》核心思想 (Inti Falsafah Analek)',
            '《孟子》仁政思想 (Falsafah Pemerintahan Mengzi)',
            '古代散文名篇 (Esei Klasik Terkenal)',
            '寓言故事 (Cerita Iktibar / Dongeng)',
            '中国传统价值观 (Nilai Tradisional Cina)',
        ], 'skills' => [
            ['理解古典文学的思想 (Memahami falsafah karya klasik)', 'hard'],
            ['分析人物与情节 (Menganalisis watak dan plot)', 'medium'],
            ['提取道德教训 (Mengekstrak pengajaran)', 'medium'],
        ]],
    ];
}

/**
 * Starter Bahasa Cina references bank — 成语 chengyu, simpulan bahasa,
 * dan kata tugas klasik (wenyan virtual words).
 */
function bahasa_cina_kssm_references(): array
{
    return [
        // topic, type, expression, pinyin, meaning_bm, meaning_zh, example, origin, category

        // ===== 成语 / Chengyu (4-character idioms) =====
        ['语法基础 (Tatabahasa Asas)', 'chengyu',
         '一举两得', 'yī jǔ liǎng dé',
         'Satu tindakan menghasilkan dua faedah; sekali gus dua manfaat.',
         '一个行动得到两个好处。',
         '骑脚踏车上学既省钱又锻炼身体，真是一举两得。',
         '《晋书》', 'kebaikan'],

        ['语法基础 (Tatabahasa Asas)', 'chengyu',
         '画蛇添足', 'huà shé tiān zú',
         'Melukis ular dan menambah kaki — membuat sesuatu yang tidak perlu, akhirnya merosakkan.',
         '比喻做多余的事，反而把事情弄糟。',
         '他的文章已经很好了，再加这些就是画蛇添足。',
         '《战国策》', 'kebijaksanaan'],

        ['语法基础 (Tatabahasa Asas)', 'chengyu',
         '守株待兔', 'shǒu zhū dài tù',
         'Mengawasi pokok menanti arnab — mengharapkan rezeki tanpa berusaha.',
         '比喻死守狭隘经验，不知变通。',
         '只想着中彩票而不努力工作，简直是守株待兔。',
         '《韩非子》', 'pengajaran'],

        ['现代汉语阅读理解 (Pemahaman Bahasa Cina Moden)', 'chengyu',
         '亡羊补牢', 'wáng yáng bǔ láo',
         'Membaiki kandang selepas kambing hilang — masih boleh diperbaiki walaupun terlambat sedikit.',
         '出了问题以后想办法补救，可以防止继续受损失。',
         '虽然考试失败，但亡羊补牢，现在努力还来得及。',
         '《战国策》', 'kebijaksanaan'],

        ['现代汉语阅读理解 (Pemahaman Bahasa Cina Moden)', 'chengyu',
         '愚公移山', 'yú gōng yí shān',
         'Yu Gong memindahkan gunung — ketekunan boleh mencapai apa-apa yang sukar.',
         '比喻不怕困难，有毅力。',
         '他每天坚持学习，这种愚公移山的精神令人敬佩。',
         '《列子》', 'ketekunan'],

        ['短文写作 (Karangan Pendek)', 'chengyu',
         '滴水穿石', 'dī shuǐ chuān shí',
         'Titisan air menebuk batu — usaha berterusan menghasilkan kejayaan besar.',
         '比喻力量虽小，只要持之以恒，也能成就大事。',
         '滴水穿石，只要不放弃，梦想终会实现。',
         '《汉书》', 'ketekunan'],

        ['短文写作 (Karangan Pendek)', 'chengyu',
         '熟能生巧', 'shú néng shēng qiǎo',
         'Lazim menghasilkan kemahiran — banyak latihan menjadi pakar.',
         '熟练了就能产生巧妙的办法。',
         '熟能生巧，只要多练习，你一定能学好华文。',
         '欧阳修', 'pembelajaran'],

        ['命题作文 (Karangan Berpandu)', 'chengyu',
         '锦上添花', 'jǐn shàng tiān huā',
         'Menambah bunga pada brokat — menambah baik sesuatu yang sudah baik.',
         '比喻好上加好，美上添美。',
         '他获得奖学金后又考上名校，真是锦上添花。',
         '黄庭坚', 'kebaikan'],

        ['命题作文 (Karangan Berpandu)', 'chengyu',
         '雪中送炭', 'xuě zhōng sòng tàn',
         'Menghantar arang dalam salji — memberi bantuan tepat pada waktu memerlukan.',
         '在别人最需要的时候给予帮助。',
         '在他困难时借钱给他，真是雪中送炭。',
         '范成大', 'kebaikan'],

        ['议论文写作 (Karangan Hujahan)', 'chengyu',
         '舍本逐末', 'shě běn zhú mò',
         'Mengabaikan asas dan mengejar perkara kecil — keutamaan terbalik.',
         '抛弃根本的、主要的部分而追求枝节、次要的部分。',
         '只重视外表而忽视品德，是舍本逐末的做法。',
         '《吕氏春秋》', 'kebijaksanaan'],

        ['议论文写作 (Karangan Hujahan)', 'chengyu',
         '一举一动', 'yī jǔ yī dòng',
         'Setiap pergerakan dan tindakan; segala perbuatan.',
         '指人的每一个动作或行为。',
         '老师的一举一动都被学生们模仿。',
         null, 'tingkah_laku'],

        ['议论文写作 (Karangan Hujahan)', 'chengyu',
         '杞人忧天', 'qǐ rén yōu tiān',
         'Orang Qi bimbangkan langit — risau perkara yang mustahil berlaku.',
         '比喻不必要的或缺乏根据的忧虑和担心。',
         '担心明天会不会下陨石，纯粹是杞人忧天。',
         '《列子》', 'kebimbangan'],

        ['记叙文与抒情文写作 (Karangan Naratif dan Emosi)', 'chengyu',
         '青出于蓝', 'qīng chū yú lán',
         'Biru daripada nila — pelajar mengatasi gurunya.',
         '比喻学生胜过老师，后人胜过前人。',
         '他的成绩比父亲还好，真是青出于蓝。',
         '《荀子》', 'pencapaian'],

        ['记叙文与抒情文写作 (Karangan Naratif dan Emosi)', 'chengyu',
         '风雨同舟', 'fēng yǔ tóng zhōu',
         'Bersama dalam perahu hujan dan angin — sehidup semati dalam kesusahan.',
         '在艰难险阻的环境中同心协力，共同奋斗。',
         '我们风雨同舟，一起渡过这次难关。',
         '《孙子兵法》', 'persahabatan'],

        ['记叙文与抒情文写作 (Karangan Naratif dan Emosi)', 'chengyu',
         '心心相印', 'xīn xīn xiāng yìn',
         'Hati ke hati saling mencerminkan — sefahaman erat.',
         '心意相通，彼此了解对方的心意。',
         '他们是心心相印的好朋友。',
         null, 'persahabatan'],

        ['现代汉语阅读理解 (Pemahaman Bahasa Cina Moden)', 'chengyu',
         '三思而行', 'sān sī ér xíng',
         'Berfikir tiga kali sebelum bertindak — fikir masak-masak sebelum bertindak.',
         '反复考虑然后再做。',
         '做重要决定前应三思而行。',
         '《论语》', 'kebijaksanaan'],

        ['现代汉语阅读理解 (Pemahaman Bahasa Cina Moden)', 'chengyu',
         '半途而废', 'bàn tú ér fèi',
         'Berhenti separuh jalan — meninggalkan sesuatu sebelum selesai.',
         '事情没做完就停止，不能坚持到底。',
         '学习华文不能半途而废，否则永远学不好。',
         '《礼记》', 'ketekunan'],

        // ===== Wenyan virtual / function words =====
        ['文言文阅读 (Pembacaan Wenyan / Bahasa Klasik)', 'classical_word',
         '之', 'zhī',
         'Kata tugas wenyan paling lazim — boleh bermaksud "yang", "nya", "kepada", atau pengganti kata nama.',
         '文言虚词，可作助词、代词或动词。',
         '"学而时习之"（《论语》）— "belajar dan mengulanginya".',
         '《论语》', 'wenyan'],

        ['文言文阅读 (Pembacaan Wenyan / Bahasa Klasik)', 'classical_word',
         '而', 'ér',
         'Kata hubung wenyan — boleh menunjukkan hubungan tambahan, tentangan atau peralihan masa.',
         '连词，表并列、转折、承接、修饰等关系。',
         '"学而不思则罔" — "belajar tetapi tidak berfikir akan keliru".',
         '《论语》', 'wenyan'],

        ['文言文阅读 (Pembacaan Wenyan / Bahasa Klasik)', 'classical_word',
         '以', 'yǐ',
         'Kata sendi wenyan — bermaksud "dengan", "untuk", "kerana".',
         '介词，表手段、原因、目的。',
         '"以德报怨" — "membalas keburukan dengan kebaikan".',
         '《老子》', 'wenyan'],

        ['文言文阅读 (Pembacaan Wenyan / Bahasa Klasik)', 'classical_word',
         '其', 'qí',
         'Kata ganti / kata tanya wenyan — bermaksud "nya", "mereka", atau soalan retorik.',
         '代词，第三人称，"他、它、他们的"。',
         '"其乐无穷" — "kegembiraannya tiada hadnya".',
         '《孟子》', 'wenyan'],

        ['文言文阅读 (Pembacaan Wenyan / Bahasa Klasik)', 'classical_word',
         '也', 'yě',
         'Kata akhir wenyan — menanda hujung ayat penegasan atau soalan.',
         '语气词，常表判断、陈述。',
         '"知之为知之，不知为不知，是知也" — "tahu adalah tahu, tidak tahu adalah tidak tahu, itulah pengetahuan".',
         '《论语》', 'wenyan'],

        // ===== Authors / Tokoh =====
        ['古典诗词 (Puisi Klasik)', 'author',
         '李白', 'Lǐ Bái',
         'Penyair besar Dinasti Tang (701-762 M) — digelar "Penyair Dewata" (诗仙) kerana gaya bebas dan romantik.',
         '唐代浪漫主义诗人，"诗仙"。',
         '《静夜思》《将进酒》《望庐山瀑布》。',
         'Dinasti Tang', 'penyair'],

        ['古典诗词 (Puisi Klasik)', 'author',
         '杜甫', 'Dù Fǔ',
         'Penyair besar Dinasti Tang (712-770 M) — digelar "Penyair Suci" (诗圣) kerana puisinya yang mencerminkan kesengsaraan rakyat.',
         '唐代现实主义诗人，"诗圣"。',
         '《春望》《登高》《茅屋为秋风所破歌》。',
         'Dinasti Tang', 'penyair'],

        ['古典诗词 (Puisi Klasik)', 'author',
         '王维', 'Wáng Wéi',
         'Penyair dan pelukis Dinasti Tang (701-761 M) — terkenal dengan puisi yang menggambarkan alam dan ketenangan Buddha.',
         '唐代田园诗人，诗中有画。',
         '《山居秋暝》《送元二使安西》。',
         'Dinasti Tang', 'penyair'],

        ['古典诗词 (Puisi Klasik)', 'author',
         '苏轼', 'Sū Shì',
         'Penyair, penulis dan pegawai Dinasti Song (1037-1101 M) — digelar Su Dongpo, terkenal dengan puisi ci dan esei.',
         '宋代文豪，号"东坡居士"。',
         '《水调歌头·明月几时有》《赤壁赋》。',
         'Dinasti Song', 'penyair'],

        ['古典文学经典 (Karya Klasik Cina)', 'author',
         '孔子', 'Kǒng Zǐ',
         'Confucius (551-479 SM) — ahli falsafah agung China; pengasas Konfusianisme yang menekankan kemanusiaan (仁), etika dan pemerintahan moral.',
         '春秋时期思想家、教育家，儒家创始人。',
         '《论语》记载其言行；"仁"、"礼"为核心思想。',
         'Dinasti Zhou', 'ahli_falsafah'],

        ['古典文学经典 (Karya Klasik Cina)', 'author',
         '孟子', 'Mèng Zǐ',
         'Mengzi (372-289 SM) — pengikut Confucius; menggalakkan pemerintahan kebajikan (仁政) dan kebaikan asas manusia (性善论).',
         '战国时期思想家，主张性善与仁政。',
         '《孟子》七篇为儒家经典。',
         'Dinasti Zhou', 'ahli_falsafah'],

        // ===== Karya Klasik =====
        ['古典文学经典 (Karya Klasik Cina)', 'classical_work',
         '《论语》', 'Lún Yǔ',
         'Analek Confucius — rekod kata-kata dan ajaran Confucius oleh murid-muridnya. Salah satu Empat Buku Klasik (四书).',
         '记录孔子及其弟子言行的儒家经典。',
         '"学而时习之，不亦说乎？"',
         'Dinasti Zhou', 'karya_klasik'],

        ['古典文学经典 (Karya Klasik Cina)', 'classical_work',
         '《孟子》', 'Mèng Zǐ',
         'Buku Mengzi — koleksi perbualan, syarahan dan teori Mengzi. Salah satu Empat Buku Klasik.',
         '孟子及其学生的言行记录，儒家经典。',
         '"老吾老以及人之老，幼吾幼以及人之幼。"',
         'Dinasti Zhou', 'karya_klasik'],

        ['古典文学经典 (Karya Klasik Cina)', 'classical_work',
         '《大学》', 'Dà Xué',
         'Pembelajaran Agung — esei pendek dalam Buku Adat yang menggariskan jalan kebijaksanaan Konfusianisme. Salah satu Empat Buku.',
         '儒家"四书"之一，论修身齐家治国平天下。',
         '"大学之道，在明明德，在亲民，在止于至善。"',
         'Dinasti Han', 'karya_klasik'],

        ['古典文学经典 (Karya Klasik Cina)', 'classical_work',
         '《中庸》', 'Zhōng Yōng',
         'Doktrin Pertengahan — esei tentang keseimbangan dan harmoni; salah satu Empat Buku.',
         '儒家"四书"之一，强调中庸之道。',
         '"喜怒哀乐之未发，谓之中。"',
         'Dinasti Han', 'karya_klasik'],

        // ===== Puisi Tang terkenal =====
        ['古典诗词 (Puisi Klasik)', 'poem',
         '《静夜思》', 'Jìng Yè Sī',
         'Puisi terkenal Li Bai — "Renungan Malam yang Sunyi". Menggambarkan kerinduan kepada kampung halaman.',
         '李白名诗，思念故乡之作。',
         '"床前明月光，疑是地上霜。举头望明月，低头思故乡。"',
         '李白 / Dinasti Tang', 'puisi_tang'],

        ['古典诗词 (Puisi Klasik)', 'poem',
         '《春望》', 'Chūn Wàng',
         'Puisi Du Fu — "Pemandangan Musim Bunga". Mencerminkan kesedihan akibat perang An Lushan.',
         '杜甫名诗，写国破家亡的悲痛。',
         '"国破山河在，城春草木深。感时花溅泪，恨别鸟惊心。"',
         '杜甫 / Dinasti Tang', 'puisi_tang'],

        ['古典诗词 (Puisi Klasik)', 'poem',
         '《登鹳雀楼》', 'Dēng Guàn Què Lóu',
         'Puisi Wang Zhihuan — "Mendaki Menara Burung Belibis". Falsafah: untuk melihat lebih jauh, perlu naik lebih tinggi.',
         '王之涣名诗，喻进取精神。',
         '"白日依山尽，黄河入海流。欲穷千里目，更上一层楼。"',
         '王之涣 / Dinasti Tang', 'puisi_tang'],

        // ===== Modern Chinese authors =====
        ['现代华文学 (KOMSAS Moden — Tingkatan 4)', 'author',
         '鲁迅', 'Lǔ Xùn',
         'Lu Xun (1881-1936) — bapa kesusasteraan moden China. Karyanya mengkritik masyarakat tradisional dan menyeru pembaharuan.',
         '中国现代文学之父，批判封建社会。',
         '《狂人日记》《阿Q正传》《故乡》。',
         'Era Republik', 'pengarang_moden'],

        ['现代华文学 (KOMSAS Moden — Tingkatan 4)', 'author',
         '冰心', 'Bīng Xīn',
         'Bing Xin (1900-1999) — penulis wanita moden China; terkenal dengan esei lembut dan puisi pendek tentang kanak-kanak dan ibu.',
         '现代著名作家，作品温柔，关怀儿童与母爱。',
         '《繁星》《春水》《寄小读者》。',
         'Era Republik', 'pengarang_moden'],

        ['现代华文学 (KOMSAS Moden — Tingkatan 4)', 'author',
         '老舍', 'Lǎo Shě',
         'Lao She (1899-1966) — novelis dan dramawan; karyanya menggambarkan kehidupan rakyat Beijing dan kepedihan zaman pergolakan.',
         '现代著名小说家、剧作家。',
         '《骆驼祥子》《茶馆》《四世同堂》。',
         'Era Republik', 'pengarang_moden'],
    ];
}

function bahasa_cina_kssm_run(): array
{
    $subject = db_one("SELECT id FROM subjects WHERE slug = 'bahasa-cina' LIMIT 1");
    if (!$subject) {
        return ['status' => 'no_subject', 'message' => 'Bahasa Cina subject not found. Run install / seed_subjects first.'];
    }
    $sid = (int) $subject['id'];

    $refsTable = (bool) db_one("SELECT 1 AS x FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'bc_references'");

    $catalog = bahasa_cina_kssm_catalog();
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
        foreach (bahasa_cina_kssm_references() as $idx => [$topicName, $type, $expr, $pinyin, $meaningBm, $meaningZh, $example, $origin, $category]) {
            $tid = $topicIds[$topicName] ?? null;
            $existing = db_one('SELECT id FROM bc_references WHERE expression = ? LIMIT 1', [$expr]);
            if ($existing) {
                $refsKept++;
            } else {
                db_exec(
                    'INSERT INTO bc_references (topic_id, topic_label, type, expression, pinyin, meaning_bm, meaning_zh, example, origin, category, sort_order)
                     VALUES (?,?,?,?,?,?,?,?,?,?,?)',
                    [$tid, $topicName, $type, $expr, $pinyin, $meaningBm, $meaningZh, $example, $origin, $category, $idx + 1]
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
    $r = bahasa_cina_kssm_run();
    if (($r['status'] ?? '') !== 'ok') {
        fwrite(STDERR, ($r['message'] ?? 'unknown error') . "\n");
        exit(1);
    }
    echo "KSSM Bahasa Cina seeded —\n";
    echo "  Topik:     created {$r['topics_created']}, adopted {$r['topics_adopted']}, kept {$r['topics_kept']} (catalog {$r['catalog_topics']})\n";
    echo "  Subtopik:  created {$r['subtopics_created']}, kept {$r['subtopics_kept']}\n";
    echo "  Kemahiran: created {$r['skills_created']}, kept {$r['skills_kept']}\n";
    if ($r['refs_table']) {
        echo "  Rujukan (成语 + wenyan + pengarang): created {$r['refs_created']}, kept {$r['refs_kept']}\n";
    } else {
        echo "  Rujukan: table missing — run database migrations to enable.\n";
    }
}
