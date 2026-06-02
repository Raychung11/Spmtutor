<?php
/**
 * Pioneer Track: Asas Kepintaran Buatan (AI Foundations) syllabus seeder.
 *
 * Not yet in the official KSSM DSKP — this is LulusAI's proposed
 * curriculum positioning AI literacy as the next-generation elective.
 * Tingkatan 4 (10 bab — AI fundamentals, ML, NLP, CV, Gen AI, prompt
 * engineering) + Tingkatan 5 (10 bab — ethics, privacy, society,
 * applications, hands-on capstone) — 20 bab total.
 *
 * Plus ~40 ai_concepts (istilah + code snippets + prompt patterns) and
 * ~30 ai_timeline entries (1956 Dartmouth → 2025 reasoning models).
 *
 * Idempotent: bab matched by (subject_id, name, form_level); legacy
 * same-named NULL-form bab are adopted.
 *
 *   php public_html/cron/seed_kepintaran_buatan_kssm.php
 */
declare(strict_types=1);

require_once __DIR__ . '/../inc/db.php';

function kepintaran_buatan_kssm_catalog(): array
{
    return [
        // ============= TINGKATAN 4 — Asas AI & Pemikiran Komputasi =============
        ['form' => 4, 'name' => 'Pengenalan kepada AI', 'subtopics' => [
            'Definisi AI dan Jenis (Narrow, General, Super)',
            'Sejarah Ringkas AI (1956 Dartmouth → Hari Ini)',
            'AI Predictive vs Generative',
            'AI dalam Kehidupan Harian',
            'Kerjaya dalam AI',
        ], 'skills' => [
            ['Mentakrifkan AI dan jenisnya', 'easy'],
            ['Menghuraikan sejarah perkembangan AI', 'medium'],
            ['Membezakan AI predictive dan generative', 'medium'],
        ]],
        ['form' => 4, 'name' => 'Data Sebagai Bahan Asas AI', 'subtopics' => [
            'Jenis Data (Structured, Unstructured, Semi-structured)',
            'Kualiti Data (Accuracy, Completeness, Consistency)',
            'Pengumpulan dan Pelabelan Data',
            'Bias dalam Data',
            'Privasi Data Asas',
        ], 'skills' => [
            ['Mengenal pasti jenis data', 'easy'],
            ['Menilai kualiti dataset', 'medium'],
            ['Menjelaskan kesan bias data terhadap AI', 'medium'],
        ]],
        ['form' => 4, 'name' => 'Pemikiran Komputasi dan Algoritma', 'subtopics' => [
            'Decomposition (Pemecahan Masalah)',
            'Pattern Recognition (Pengenalan Corak)',
            'Abstraction (Pengabstrakan)',
            'Pseudokod Algoritma AI',
            'Carta Aliran Logik AI',
        ], 'skills' => [
            ['Mengaplikasikan empat tunjang pemikiran komputasi', 'medium'],
            ['Menulis pseudokod algoritma mudah', 'medium'],
            ['Melukis carta aliran proses AI', 'medium'],
        ]],
        ['form' => 4, 'name' => 'Pembelajaran Mesin (Machine Learning) Asas', 'subtopics' => [
            'Konsep Pembelajaran Mesin',
            'Supervised Learning (Pembelajaran Bimbingan)',
            'Unsupervised Learning (Pembelajaran Tanpa Bimbingan)',
            'Reinforcement Learning (Pembelajaran Pengukuhan)',
            'Pembahagian Data: Train / Validation / Test',
            'Accuracy, Precision, Recall',
        ], 'skills' => [
            ['Membezakan paradigma pembelajaran mesin', 'medium'],
            ['Menjelaskan kitar latihan model ML', 'medium'],
            ['Mentafsir metrik prestasi (accuracy, precision, recall)', 'hard'],
        ]],
        ['form' => 4, 'name' => 'Algoritma ML Mudah', 'subtopics' => [
            'Decision Trees (Pokok Keputusan)',
            'k-Nearest Neighbours (k-NN)',
            'Linear Regression',
            'k-Means Clustering',
            'Overfitting dan Underfitting',
        ], 'skills' => [
            ['Membina pokok keputusan secara manual', 'hard'],
            ['Menjelaskan algoritma k-NN', 'medium'],
            ['Mengenal pasti overfitting dan underfitting', 'medium'],
        ]],
        ['form' => 4, 'name' => 'Neural Networks 101', 'subtopics' => [
            'Model Neuron Tunggal (Perceptron)',
            'Lapisan Input, Hidden, Output',
            'Activation Functions (ReLU, Sigmoid, Softmax)',
            'Forward Pass dan Backpropagation',
            'Deep Learning: Mengapa "Deep"?',
        ], 'skills' => [
            ['Menerangkan struktur neural network', 'medium'],
            ['Mengaitkan neural network dengan otak manusia', 'easy'],
            ['Membezakan ML klasik dan deep learning', 'medium'],
        ]],
        ['form' => 4, 'name' => 'Natural Language Processing (NLP)', 'subtopics' => [
            'Tokenization dan Embeddings',
            'Sentiment Analysis',
            'Machine Translation',
            'Chatbot Architecture',
            'Large Language Models (LLM) Asas',
        ], 'skills' => [
            ['Menjelaskan tokenization dan embeddings', 'medium'],
            ['Mengaplikasikan sentiment analysis', 'medium'],
            ['Menerangkan cara LLM berfungsi pada tahap intuitif', 'hard'],
        ]],
        ['form' => 4, 'name' => 'Computer Vision', 'subtopics' => [
            'Pixel, Feature, Object Recognition',
            'Convolutional Neural Network (CNN) Intuisi',
            'Image Classification',
            'Object Detection vs Segmentation',
            'OCR, Face Recognition, Deepfake Awareness',
        ], 'skills' => [
            ['Menjelaskan bagaimana komputer "melihat" imej', 'medium'],
            ['Membezakan classification, detection dan segmentation', 'medium'],
            ['Menilai aplikasi computer vision dalam kehidupan', 'medium'],
        ]],
        ['form' => 4, 'name' => 'Generative AI', 'subtopics' => [
            'Apa itu Generative AI?',
            'Large Language Models (Claude, GPT, Gemini)',
            'Diffusion Models (DALL-E, Stable Diffusion, Midjourney)',
            'Audio dan Video Generation',
            'Hallucination dan Had AI Generatif',
        ], 'skills' => [
            ['Menjelaskan konsep generative AI', 'medium'],
            ['Membezakan LLM dan diffusion models', 'medium'],
            ['Mengenal pasti hallucination dan had AI', 'hard'],
        ]],
        ['form' => 4, 'name' => 'Prompt Engineering Asas', 'subtopics' => [
            'Anatomi Prompt yang Baik (Role, Task, Constraints, Format)',
            'Few-shot vs Zero-shot Prompting',
            'Chain-of-Thought (CoT) Prompting',
            'Output Format Specification',
            'Iterative Refinement',
        ], 'skills' => [
            ['Menulis prompt yang berstruktur', 'medium'],
            ['Mengaplikasikan teknik chain-of-thought', 'hard'],
            ['Memperbaiki prompt secara iteratif', 'medium'],
        ]],

        // ============= TINGKATAN 5 — Aplikasi, Etika dan Reka Cipta =============
        ['form' => 5, 'name' => 'Etika AI', 'subtopics' => [
            'Bias dalam Algoritma',
            'Fairness dan Equity',
            'Accountability dan Liability',
            'Explainable AI (XAI)',
            'Kes Kajian: Algoritma Pekerjaan dan Pinjaman',
        ], 'skills' => [
            ['Mengenal pasti bias dalam sistem AI', 'medium'],
            ['Menilai isu kebertanggungjawaban AI', 'hard'],
            ['Membincangkan keperluan explainable AI', 'hard'],
        ]],
        ['form' => 5, 'name' => 'Privasi Data dan Hak Digital', 'subtopics' => [
            'Akta Perlindungan Data Peribadi (PDPA) Malaysia 2010',
            'Pindaan PDPA 2024',
            'Persetujuan (Consent) dan Hak Pengguna',
            'Hak untuk Dilupakan (Right to be Forgotten)',
            'AI dan Surveillance',
        ], 'skills' => [
            ['Menjelaskan peruntukan utama PDPA Malaysia', 'medium'],
            ['Menerangkan hak digital pengguna', 'medium'],
            ['Menilai implikasi AI terhadap privasi', 'hard'],
        ]],
        ['form' => 5, 'name' => 'Misinformation dan Deepfakes', 'subtopics' => [
            'Apa itu Deepfake?',
            'Generative AI dan Maklumat Palsu',
            'Watermarking dan Provenance',
            'Fact-checking Tools dan Sumber Boleh Dipercayai',
            'Tanggungjawab Pencipta dan Pengguna Kandungan',
        ], 'skills' => [
            ['Mengenal pasti tanda deepfake', 'medium'],
            ['Mengaplikasikan kaedah fact-checking', 'medium'],
            ['Menilai sumber maklumat secara kritis', 'medium'],
        ]],
        ['form' => 5, 'name' => 'AI dan Pekerjaan Masa Depan', 'subtopics' => [
            'Automasi vs Augmentasi',
            'Pekerjaan Berisiko Tinggi vs Rendah',
            'Kemahiran Tidak Boleh Diautomasi (Kreativiti, Empati, Strategi)',
            'Lifelong Learning dan Reskilling',
            'Tinjauan Kerjaya AI di Malaysia',
        ], 'skills' => [
            ['Membezakan automasi dan augmentasi', 'medium'],
            ['Mengenal pasti kemahiran abad ke-21', 'easy'],
            ['Merancang pembangunan kerjaya dalam era AI', 'medium'],
        ]],
        ['form' => 5, 'name' => 'AI dalam Ekonomi Malaysia', 'subtopics' => [
            'Industri 4.0 dan Malaysia Madani',
            'MDEC, MCMC dan Inisiatif AI Negara',
            'AI Startup Ecosystem (Aerodyne, Ailegant, MyAI)',
            'Pelaburan Asing dalam Pusat Data Malaysia',
            'Polisi Etika AI Negara',
        ], 'skills' => [
            ['Menerangkan inisiatif AI Malaysia', 'medium'],
            ['Menilai potensi ekonomi AI', 'medium'],
            ['Menganalisis polisi AI Malaysia', 'hard'],
        ]],
        ['form' => 5, 'name' => 'AI dalam Sektor Industri', 'subtopics' => [
            'AI dalam Kesihatan (Diagnosis, Drug Discovery)',
            'AI dalam Pertanian (Presisi, Drone, Prediksi Cuaca)',
            'AI dalam Pendidikan (Tutoring, Adaptive Learning)',
            'AI dalam Kewangan (Fraud Detection, Algorithmic Trading)',
            'AI dalam Pengangkutan (Self-driving, Logistik)',
        ], 'skills' => [
            ['Menghuraikan aplikasi AI dalam pelbagai sektor', 'medium'],
            ['Menilai impak AI dalam sesuatu sektor', 'medium'],
            ['Mencadangkan aplikasi AI baharu untuk masalah tempatan', 'hard'],
        ]],
        ['form' => 5, 'name' => 'Bina Aplikasi AI No-Code', 'subtopics' => [
            'Google Teachable Machine',
            'Hugging Face Spaces',
            'Make.com / Zapier untuk Workflow AI',
            'ChatGPT Custom GPTs',
            'Bina Chatbot Mudah',
        ], 'skills' => [
            ['Melatih classifier dengan Teachable Machine', 'medium'],
            ['Membina workflow AI no-code', 'medium'],
            ['Mereka custom GPT untuk masalah tempatan', 'hard'],
        ]],
        ['form' => 5, 'name' => 'Coding dengan AI Assistant', 'subtopics' => [
            'Python Asas untuk AI',
            'Pair Programming dengan Claude / GitHub Copilot',
            'API Calls (OpenAI, Anthropic, Hugging Face)',
            'Membaca dan Memahami Output AI',
            'Debugging dengan Bantuan AI',
        ], 'skills' => [
            ['Memanggil AI API menggunakan Python', 'hard'],
            ['Mengaplikasikan pair programming dengan AI', 'medium'],
            ['Mendebug kod menggunakan AI', 'medium'],
        ]],
        ['form' => 5, 'name' => 'Sustainability dan AI', 'subtopics' => [
            'Kos Karbon Model Besar',
            'Edge AI dan Pengiraan Tempatan',
            'Green Compute dan Pusat Data Hijau',
            'AI untuk Pelestarian Alam',
            'Etika Penggunaan AI Mampan',
        ], 'skills' => [
            ['Mengira kos karbon penggunaan AI', 'medium'],
            ['Membezakan edge AI dan cloud AI', 'medium'],
            ['Mencadangkan amalan AI mampan', 'medium'],
        ]],
        ['form' => 5, 'name' => 'Projek AI Akhir (Capstone)', 'subtopics' => [
            'Pengenalpastian Masalah Tempatan',
            'Reka Bentuk Penyelesaian AI',
            'Pelaksanaan Prototaip (No-Code atau Python)',
            'Pengujian dan Penilaian',
            'Pembentangan dan Dokumentasi',
        ], 'skills' => [
            ['Mengenal pasti masalah yang boleh diselesaikan dengan AI', 'medium'],
            ['Membina prototaip AI', 'hard'],
            ['Membentangkan projek dengan berkesan', 'medium'],
        ]],
    ];
}

/** Starter ai_concepts bank — istilah + code snippets + prompt patterns. */
function kepintaran_buatan_kssm_concepts(): array
{
    return [
        // topic, type, name, definition, code_snippet, language, example, category, year_invented

        // ----- Foundational concepts -----
        ['Pengenalan kepada AI', 'istilah', 'Artificial Intelligence (AI)',
         'Bidang sains komputer yang membangunkan mesin yang dapat melaksanakan tugas yang biasanya memerlukan kepintaran manusia — seperti belajar, menalar, memahami bahasa, dan membuat keputusan.',
         null, null, 'Siri-Apple, ChatGPT, kereta self-driving Tesla.', 'foundational', '1956'],

        ['Pengenalan kepada AI', 'istilah', 'Narrow AI (Weak AI)',
         'AI yang direka untuk melaksanakan satu tugas tertentu sahaja. Semua AI yang wujud hari ini adalah Narrow AI.',
         null, null, 'AlphaGo (main Go), Siri (asisten suara), ChatGPT (bahasa).', 'foundational', null],

        ['Pengenalan kepada AI', 'istilah', 'General AI (AGI)',
         'AI hipotetikal yang mempunyai kepintaran setara dengan manusia merentas semua bidang. Belum wujud.',
         null, null, 'Konsep — masih menjadi matlamat penyelidikan jangka panjang OpenAI, Anthropic, DeepMind.', 'foundational', null],

        ['Pengenalan kepada AI', 'istilah', 'Generative AI',
         'Kategori AI yang menghasilkan kandungan baharu (teks, imej, audio, video) berdasarkan corak data latihan.',
         null, null, 'Claude untuk teks, DALL-E untuk imej, ElevenLabs untuk suara.', 'foundational', '2014'],

        // ----- Data -----
        ['Data Sebagai Bahan Asas AI', 'istilah', 'Structured Data',
         'Data yang tersusun dalam baris dan lajur (jadual) dengan skema yang jelas.',
         null, null, 'Spreadsheet Excel, jadual MySQL, fail CSV.', 'data', null],

        ['Data Sebagai Bahan Asas AI', 'istilah', 'Unstructured Data',
         'Data tanpa struktur jadual yang tetap — teks bebas, imej, audio, video.',
         null, null, 'Email, foto, lagu, video YouTube, tweet.', 'data', null],

        ['Data Sebagai Bahan Asas AI', 'istilah', 'Data Bias',
         'Berlaku apabila data latihan tidak mewakili populasi sebenar, menyebabkan model membuat keputusan yang tidak adil.',
         null, null, 'Sistem pengecaman muka yang kurang tepat untuk wajah kulit gelap kerana data latihan kebanyakan kulit cerah.', 'ethics', null],

        // ----- Machine Learning -----
        ['Pembelajaran Mesin (Machine Learning) Asas', 'istilah', 'Machine Learning (ML)',
         'Subset AI di mana sistem belajar daripada data tanpa diprogram secara eksplisit untuk setiap tugas.',
         null, null, 'Filter spam email yang belajar daripada email yang ditandakan sebagai spam.', 'ml_basics', '1959'],

        ['Pembelajaran Mesin (Machine Learning) Asas', 'istilah', 'Supervised Learning',
         'Paradigma ML di mana model dilatih dengan data berlabel (input + jawapan betul).',
         null, null, 'Mengklasifikasi email sebagai spam/bukan-spam dengan data email berlabel.', 'ml_basics', null],

        ['Pembelajaran Mesin (Machine Learning) Asas', 'istilah', 'Unsupervised Learning',
         'Paradigma ML di mana model mencari corak dalam data tanpa label.',
         null, null, 'Mengelompokkan pelanggan berdasarkan tabiat membeli (clustering) tanpa label kategori.', 'ml_basics', null],

        ['Pembelajaran Mesin (Machine Learning) Asas', 'syntax', 'Python — Train/Test Split',
         'Memisahkan dataset kepada bahagian latihan dan ujian untuk menilai prestasi model dengan adil.',
         "from sklearn.model_selection import train_test_split\n\nX_train, X_test, y_train, y_test = train_test_split(\n    X, y, test_size=0.2, random_state=42\n)\n\nprint(f'Train: {len(X_train)}, Test: {len(X_test)}')",
         'python', '80% latihan, 20% ujian — standard amalan.', 'ml_basics', null],

        // ----- Algorithms -----
        ['Algoritma ML Mudah', 'istilah', 'Decision Tree',
         'Algoritma ML yang membuat keputusan dengan mengikuti siri soalan ya/tidak — seperti carta aliran.',
         null, null, 'Klasifikasi sama ada hari ini sesuai bermain golf berdasarkan cuaca, kelembapan, angin.', 'algorithms', '1959'],

        ['Algoritma ML Mudah', 'istilah', 'k-Nearest Neighbours (k-NN)',
         'Algoritma yang mengklasifikasi data baharu berdasarkan k jirannya yang paling dekat dalam ruang ciri.',
         null, null, 'Mengesyorkan filem berdasarkan tabiat menonton pengguna serupa.', 'algorithms', '1951'],

        ['Algoritma ML Mudah', 'syntax', 'Python — Decision Tree Classifier',
         'Melatih pokok keputusan menggunakan scikit-learn.',
         "from sklearn.tree import DecisionTreeClassifier\nfrom sklearn.metrics import accuracy_score\n\nmodel = DecisionTreeClassifier(max_depth=5)\nmodel.fit(X_train, y_train)\n\npred = model.predict(X_test)\nprint(f'Accuracy: {accuracy_score(y_test, pred):.2%}')",
         'python', 'Output: Accuracy: 87.50%', 'algorithms', null],

        ['Algoritma ML Mudah', 'istilah', 'Overfitting',
         'Model belajar terlalu dekat dengan data latihan termasuk hingar — prestasi tinggi pada train tapi rendah pada test.',
         null, null, 'Pokok keputusan dengan kedalaman 100 yang menghafal setiap titik latihan.', 'algorithms', null],

        // ----- Neural Networks -----
        ['Neural Networks 101', 'istilah', 'Neural Network',
         'Model ML yang diinspirasi oleh otak — terdiri daripada lapisan neuron yang menerima input, mengaplikasikan pemberat, dan menghasilkan output.',
         null, null, 'Pengecaman tulisan tangan dalam aplikasi bank.', 'neural_nets', '1958'],

        ['Neural Networks 101', 'istilah', 'Backpropagation',
         'Algoritma yang mengemaskini pemberat neural network dengan menyebarkan ralat dari lapisan output ke input.',
         null, null, 'Asas latihan semua deep learning hari ini sejak kertas Rumelhart 1986.', 'neural_nets', '1986'],

        ['Neural Networks 101', 'istilah', 'Activation Function',
         'Fungsi tak linear yang dikenakan pada output neuron untuk membolehkan rangkaian mempelajari corak kompleks.',
         null, null, 'ReLU (Rectified Linear Unit), Sigmoid, Softmax.', 'neural_nets', null],

        ['Neural Networks 101', 'istilah', 'Deep Learning',
         'Subset ML menggunakan neural network dengan banyak lapisan (deep) untuk mempelajari representasi data yang kompleks.',
         null, null, 'GPT, BERT, Stable Diffusion, AlphaGo — semua adalah deep learning.', 'neural_nets', '2012'],

        // ----- NLP -----
        ['Natural Language Processing (NLP)', 'istilah', 'Tokenization',
         'Proses memecahkan teks kepada unit yang lebih kecil (tokens) — boleh kata, sub-kata, atau aksara.',
         null, null, '"Saya makan nasi" → ["Saya", "makan", "nasi"] atau ["Sa", "ya", " ma", "kan", ...]', 'nlp', null],

        ['Natural Language Processing (NLP)', 'istilah', 'Embeddings',
         'Representasi vektor (nombor) bagi perkataan atau frasa yang menangkap makna semantik.',
         null, null, 'Vector("Raja") - Vector("Lelaki") + Vector("Perempuan") ≈ Vector("Ratu")', 'nlp', '2013'],

        ['Natural Language Processing (NLP)', 'istilah', 'Large Language Model (LLM)',
         'Neural network yang dilatih atas jumlah teks yang sangat besar untuk meramal token seterusnya.',
         null, null, 'Claude, GPT-4, Gemini, Llama, Mistral.', 'nlp', '2018'],

        ['Natural Language Processing (NLP)', 'istilah', 'Transformer Architecture',
         'Seni bina neural network yang menggunakan mekanisme "attention" untuk memproses jujukan — asas semua LLM moden.',
         null, null, 'Kertas "Attention Is All You Need" oleh Google (2017).', 'nlp', '2017'],

        ['Natural Language Processing (NLP)', 'istilah', 'RAG (Retrieval-Augmented Generation)',
         'Teknik yang menggabungkan LLM dengan pangkalan data tersuai supaya LLM boleh menjawab soalan tentang dokumen spesifik.',
         null, null, 'Chatbot syarikat yang menjawab daripada buku panduan dalaman.', 'nlp', '2020'],

        ['Natural Language Processing (NLP)', 'istilah', 'Hallucination',
         'Apabila LLM menghasilkan maklumat yang kelihatan munasabah tetapi sebenarnya palsu atau dicipta.',
         null, null, 'Chatbot yang memberikan petikan kes mahkamah yang tidak wujud.', 'nlp', null],

        // ----- Computer Vision -----
        ['Computer Vision', 'istilah', 'Convolutional Neural Network (CNN)',
         'Jenis neural network khas untuk imej yang menggunakan operasi convolution untuk mengesan ciri seperti tepi dan tekstur.',
         null, null, 'AlexNet (2012) — model CNN yang mencetuskan revolusi deep learning.', 'computer_vision', '1989'],

        ['Computer Vision', 'istilah', 'Image Classification',
         'Tugas memberi label kepada keseluruhan imej daripada satu set kategori.',
         null, null, 'Imej ini → "kucing" atau "anjing"?', 'computer_vision', null],

        ['Computer Vision', 'istilah', 'Object Detection',
         'Mengenal pasti dan melokasikan beberapa objek dalam satu imej.',
         null, null, 'YOLO mengesan dan melukis kotak sekeliling kereta, orang, lampu trafik dalam foto jalan raya.', 'computer_vision', '2015'],

        // ----- Generative AI -----
        ['Generative AI', 'istilah', 'Diffusion Model',
         'Jenis model generatif yang belajar menjana imej dengan secara beransur-ansur mengeluarkan "noise" dari noise rawak.',
         null, null, 'DALL-E 3, Stable Diffusion, Midjourney — semua adalah diffusion models.', 'gen_ai', '2020'],

        ['Generative AI', 'syntax', 'Python — Call Claude API',
         'Menghantar prompt ke Claude API dan menerima respons.',
         "import anthropic\n\nclient = anthropic.Anthropic(api_key='sk-ant-...')\n\nmessage = client.messages.create(\n    model='claude-sonnet-4-6',\n    max_tokens=1024,\n    messages=[\n        {'role': 'user', 'content': 'Terangkan AI dalam Bahasa Melayu, ringkas.'}\n    ]\n)\nprint(message.content[0].text)",
         'python', 'Output: AI ialah keupayaan mesin untuk meniru kepintaran manusia...', 'gen_ai', null],

        // ----- Prompt Engineering -----
        ['Prompt Engineering Asas', 'istilah', 'Prompt Engineering',
         'Seni dan sains mereka prompt yang menghasilkan output AI berkualiti tinggi melalui kejelasan, konteks, dan kekangan yang tepat.',
         null, null, '"Anda ialah tutor SPM. Terangkan teori sel dalam 3 perenggan untuk pelajar Form 4."', 'prompt_engineering', '2020'],

        ['Prompt Engineering Asas', 'syntax', 'Anatomi Prompt yang Baik',
         'Struktur lima-bahagian: Role + Task + Context + Constraints + Output Format.',
         "[ROLE]   Anda ialah tutor SPM Bahasa Melayu berpengalaman.\n[TASK]   Periksa karangan murid berikut.\n[CONTEXT] Karangan: \"...\"  Tajuk: Amalan gaya hidup sihat.\n[CONSTRAINTS] Berikan markah ikut rubrik SPM 100m. Fokus pada Isi, Bahasa, Pengolahan, Gaya.\n[OUTPUT FORMAT] JSON: {score, rubric, strengths[], weaknesses[], suggestions[]}",
         'prompt', 'Output: {\"score\": 78, \"rubric\": {...}, ...}', 'prompt_engineering', null],

        ['Prompt Engineering Asas', 'syntax', 'Chain-of-Thought Prompting',
         'Teknik menambah "Fikir langkah demi langkah" supaya LLM menunjukkan penalaran intermediate, meningkatkan ketepatan tugas penalaran.',
         "Soalan: Kelas mempunyai 24 pelajar. 3 daripada 8 adalah perempuan. Berapakah\n          jumlah perempuan dalam kelas?\n\nMari kita fikir langkah demi langkah:\n1. Pecahan perempuan = 3/8\n2. Jumlah pelajar = 24\n3. Bilangan perempuan = 24 × 3/8 = 9\n\nJawapan: 9 perempuan.",
         'prompt', 'Tanpa CoT: ~30% accuracy. Dengan CoT: ~80% accuracy pada GSM8K benchmark.', 'prompt_engineering', '2022'],

        ['Prompt Engineering Asas', 'istilah', 'Few-shot Prompting',
         'Memberi 2-5 contoh dalam prompt sebagai panduan untuk tugas — LLM belajar corak dari contoh.',
         null, null, 'Tunjukkan 3 contoh terjemahan BM→EN, kemudian minta LLM terjemah ayat baharu.', 'prompt_engineering', null],

        // ----- Ethics & Society -----
        ['Etika AI', 'istilah', 'Algorithmic Bias',
         'Berlaku apabila sistem AI menghasilkan keputusan yang tidak adil terhadap kumpulan tertentu — biasanya disebabkan data latihan yang berat sebelah.',
         null, null, 'Algoritma pengambilan pekerja Amazon (2018) didapati berat sebelah terhadap calon perempuan.', 'ethics', null],

        ['Etika AI', 'istilah', 'Explainable AI (XAI)',
         'Pendekatan AI di mana keputusan model boleh difahami dan dijelaskan oleh manusia — penting dalam sektor seperti perubatan dan kewangan.',
         null, null, 'LIME dan SHAP — perpustakaan untuk menjelaskan ramalan model.', 'ethics', '2016'],

        ['Privasi Data dan Hak Digital', 'istilah', 'PDPA Malaysia',
         'Akta Perlindungan Data Peribadi 2010 — undang-undang utama Malaysia mengawal pemprosesan data peribadi dalam transaksi komersial. Dipinda 2024 untuk peruntukan baharu termasuk DPO mandatori.',
         null, null, 'Mengenakan denda sehingga RM1 juta atas pelanggaran PDPA.', 'ethics', '2010'],

        ['Misinformation dan Deepfakes', 'istilah', 'Deepfake',
         'Media sintetik (imej, video, audio) yang dihasilkan oleh AI untuk meniru rupa atau suara seseorang secara meyakinkan.',
         null, null, 'Video deepfake Tom Cruise viral 2021 yang dijana menggunakan model deep learning.', 'ethics', '2017'],

        ['Misinformation dan Deepfakes', 'istilah', 'Watermarking',
         'Teknik menambah tanda halus (visible atau invisible) pada output AI supaya boleh dikesan kemudian.',
         null, null, 'SynthID Google untuk mengesan imej yang dijana AI.', 'ethics', '2023'],

        // ----- Local context -----
        ['AI dalam Ekonomi Malaysia', 'istilah', 'Polisi AI Negara (NAIRR)',
         'Polisi AI Kebangsaan Malaysia yang menggariskan strategi pembangunan ekosistem AI 2026-2030.',
         null, null, 'Dilancarkan oleh MOSTI dan MDEC pada 2024.', 'malaysia', '2024'],

        ['AI dalam Ekonomi Malaysia', 'istilah', 'MyAI Centre of Excellence',
         'Pusat kecemerlangan AI Malaysia yang ditubuhkan untuk membangunkan bakat dan penyelesaian AI tempatan.',
         null, null, 'Kolaborasi antara MDEC dan universiti tempatan.', 'malaysia', '2024'],

        // ----- Sustainability -----
        ['Sustainability dan AI', 'istilah', 'Edge AI',
         'Menjalankan model AI pada peranti (telefon, sensor) berbanding dalam awan — kurang latency, lebih privasi, kurang penggunaan tenaga.',
         null, null, 'Apple Neural Engine pada iPhone untuk pengecaman muka Face ID setempat.', 'sustainability', '2017'],

        ['Sustainability dan AI', 'istilah', 'Carbon Cost of AI Training',
         'Jumlah pelepasan karbon yang dihasilkan semasa melatih model AI besar — boleh sangat signifikan.',
         null, null, 'Melatih GPT-3 dianggarkan menghasilkan ~552 tonne CO2 — setara dengan 120 kereta sepanjang setahun.', 'sustainability', null],

        // ----- Reinforcement Learning -----
        ['Pembelajaran Mesin (Machine Learning) Asas', 'istilah', 'RLHF (Reinforcement Learning from Human Feedback)',
         'Teknik latihan di mana model belajar daripada maklum balas manusia — asas penjajaran ChatGPT, Claude, Gemini.',
         null, null, 'Manusia melabel mana respons yang lebih baik → model dilatih untuk menghasilkan respons jenis itu.', 'ml_basics', '2017'],
    ];
}

/** Starter AI timeline — 30 peristiwa penting dari 1956 hingga 2025. */
function kepintaran_buatan_kssm_timeline(): array
{
    return [
        // -------- Foundational era --------
        ['Pengenalan kepada AI', 'foundational', '1950', NULL,                 'Turing Test diperkenalkan', 'Alan Turing menerbitkan kertas "Computing Machinery and Intelligence" yang memperkenalkan Imitation Game (Turing Test) sebagai ukuran kepintaran mesin.', 'high'],
        ['Pengenalan kepada AI', 'foundational', '1956', 'Julai',              'Bengkel Dartmouth — kelahiran AI', 'John McCarthy mengumpul saintis di Dartmouth College untuk bengkel musim panas tentang "kepintaran buatan" — gelaran AI dicipta di sini.', 'high'],
        ['Algoritma ML Mudah',   'foundational', '1957', NULL,                 'Frank Rosenblatt mencipta Perceptron', 'Algoritma neural network terawal — Mark I Perceptron mampu mengecam imej mudah.', 'medium'],
        ['Pengenalan kepada AI', 'foundational', '1959', NULL,                 'Istilah "Machine Learning" diperkenalkan', 'Arthur Samuel mendefinisikan ML sebagai "bidang yang memberi komputer keupayaan untuk belajar tanpa diprogram secara eksplisit".', 'high'],

        // -------- AI Winter --------
        ['Pengenalan kepada AI', 'ai_winter',    '1969', NULL,                 'Buku "Perceptrons" — bermulanya AI Winter', 'Minsky dan Papert menerbitkan buku yang menunjukkan had perceptron tunggal, mengurangkan pendanaan neural networks selama dekad.', 'medium'],
        ['Pengenalan kepada AI', 'ai_winter',    '1974',  NULL,                'Lighthill Report — pengurangan dana AI', 'Laporan kerajaan British mengkritik penyelidikan AI, mencetuskan AI Winter pertama yang berterusan ke 1980an.', 'medium'],

        // -------- Renaissance --------
        ['Neural Networks 101', 'renaissance',  '1986', NULL,                  'Backpropagation dipopularkan', 'Rumelhart, Hinton dan Williams menerbitkan kertas yang memperkenalkan backpropagation kepada komuniti AI yang lebih luas.', 'high'],
        ['Algoritma ML Mudah',   'renaissance',  '1997', '11 Mei',             'Deep Blue mengalahkan Garry Kasparov', 'Komputer catur IBM Deep Blue mengalahkan juara dunia Garry Kasparov — milestone AI dalam permainan strategik.', 'high'],

        // -------- Deep Learning Revolution --------
        ['Computer Vision',      'deep_learning','2012', 'September',           'AlexNet memenangi ImageNet', 'Krizhevsky, Sutskever dan Hinton dengan CNN AlexNet mengurangkan ralat ImageNet daripada 25% kepada 16% — mencetuskan revolusi deep learning.', 'high'],
        ['Natural Language Processing (NLP)', 'deep_learning', '2013', NULL,    'Word2Vec diperkenalkan', 'Mikolov et al. dari Google menerbitkan Word2Vec — algoritma embeddings yang menjadikan "Raja - Lelaki + Perempuan = Ratu" mungkin.', 'medium'],
        ['Generative AI',        'deep_learning','2014', 'Jun',                 'Ian Goodfellow mencipta GAN', 'Generative Adversarial Networks — dua neural network bersaing untuk menghasilkan imej sintetik berkualiti tinggi.', 'high'],
        ['Algoritma ML Mudah',   'deep_learning','2016', 'Mac',                 'AlphaGo mengalahkan Lee Sedol', 'DeepMind\'s AlphaGo mengalahkan juara Go dunia 4-1 — dahulunya dianggap masalah tidak boleh diselesaikan oleh AI.', 'high'],

        // -------- Transformer Era --------
        ['Natural Language Processing (NLP)', 'transformer', '2017', 'Jun',     'Kertas "Attention Is All You Need"', 'Pasukan Google memperkenalkan seni bina Transformer — asas semua LLM moden termasuk GPT, BERT, Claude.', 'high'],
        ['Natural Language Processing (NLP)', 'transformer', '2018', 'Oktober', 'BERT dikeluarkan oleh Google', 'Bidirectional Encoder Representations from Transformers — meningkatkan SOTA pada banyak tugas NLP.', 'medium'],
        ['Natural Language Processing (NLP)', 'transformer', '2019', 'Februari','GPT-2 dikeluarkan oleh OpenAI', 'OpenAI pada mulanya menahan model penuh kerana kebimbangan tentang penyalahgunaan — model bahasa pertama yang menghasilkan teks meyakinkan.', 'medium'],
        ['Natural Language Processing (NLP)', 'transformer', '2020', 'Mei',     'GPT-3 dengan 175 bilion parameter', 'OpenAI mengeluarkan GPT-3 — lompatan kuantum dalam keupayaan few-shot learning bahasa.', 'high'],

        // -------- Generative AI boom --------
        ['Generative AI', 'gen_ai_boom', '2021', 'Januari',                     'DALL-E mengejutkan dunia', 'OpenAI menunjukkan model teks-ke-imej pertama yang luas — "kerusi alpukat" menjadi viral.', 'medium'],
        ['Generative AI', 'gen_ai_boom', '2022', 'Julai',                       'Stable Diffusion adalah open source', 'Stability AI mengeluarkan model diffusion open source — menjadikan generasi imej AI boleh diakses semua orang.', 'medium'],
        ['Generative AI', 'gen_ai_boom', '2022', '30 November',                 'ChatGPT dilancarkan', 'OpenAI mengeluarkan ChatGPT — mencapai 100 juta pengguna dalam 2 bulan, momen "iPhone" untuk AI.', 'high'],
        ['Generative AI', 'gen_ai_boom', '2023', 'Februari',                    'Microsoft melabur $10 bilion dalam OpenAI', 'Perkongsian strategik Microsoft-OpenAI mempercepatkan integrasi AI ke dalam Bing, Office, Windows.', 'medium'],
        ['Generative AI', 'gen_ai_boom', '2023', 'Mac',                         'Anthropic melancarkan Claude', 'Anthropic — diasaskan oleh bekas penyelidik OpenAI — mengeluarkan Claude sebagai pesaing ChatGPT dengan fokus keselamatan.', 'high'],
        ['Generative AI', 'gen_ai_boom', '2023', 'Mac',                         'GPT-4 dikeluarkan oleh OpenAI', 'GPT-4 menunjukkan keupayaan penalaran yang signifikan lebih baik dan input pelbagai modal.', 'high'],
        ['Generative AI', 'gen_ai_boom', '2023', 'Disember',                    'Google mengeluarkan Gemini', 'Pesaing utama GPT-4 daripada DeepMind/Google, dengan keupayaan multimodal asli.', 'medium'],

        // -------- Regulation & society --------
        ['Etika AI',     'regulation', '2023', 'November',                      'Sidang Kemuncak AI Safety di Bletchley Park', 'UK menganjurkan sidang kemuncak antarabangsa pertama tentang keselamatan AI dengan 28 negara menandatangani Deklarasi Bletchley.', 'high'],
        ['Etika AI',     'regulation', '2024', 'Mei',                          'EU AI Act diluluskan', 'Parlimen Eropah meluluskan akta AI komprehensif pertama dunia — mengklasifikasi sistem AI mengikut risiko.', 'high'],
        ['AI dalam Ekonomi Malaysia', 'regulation', '2024', NULL,             'Polisi AI Negara Malaysia (NAIRR)', 'MOSTI dan MDEC melancarkan rangka kerja kebangsaan untuk pembangunan AI 2026-2030.', 'medium'],
        ['Privasi Data dan Hak Digital', 'regulation', '2024', NULL,           'Pindaan PDPA Malaysia', 'Akta Perlindungan Data Peribadi dipinda untuk memperkenalkan DPO mandatori dan denda lebih berat.', 'medium'],

        // -------- Reasoning era --------
        ['Generative AI', 'reasoning', '2024', 'September',                     'OpenAI o1 — model reasoning pertama', 'OpenAI mengeluarkan o1 — model dengan "thinking" yang lebih panjang sebelum menjawab, meningkatkan ketepatan pada masalah matematik dan sains.', 'high'],
        ['Generative AI', 'reasoning', '2025', 'Februari',                      'Claude 3.7 Sonnet dengan extended thinking', 'Anthropic mengeluarkan Claude dengan keupayaan reasoning yang boleh dilanjutkan — menggabungkan kelajuan dan ketepatan.', 'high'],
        ['Generative AI', 'reasoning', '2025', NULL,                            'DeepSeek R1 — reasoning model open source', 'Makmal AI Cina DeepSeek mengeluarkan R1 — model reasoning open source yang setanding dengan o1 dengan kos pengiraan jauh lebih rendah.', 'high'],
    ];
}

function kepintaran_buatan_kssm_run(): array
{
    $subject = db_one("SELECT id FROM subjects WHERE slug = 'kepintaran-buatan' LIMIT 1");
    if (!$subject) {
        return ['status' => 'no_subject', 'message' => 'Kepintaran Buatan subject not found. Run install / seed_subjects first.'];
    }
    $sid = (int) $subject['id'];

    $conceptsTable = (bool) db_one("SELECT 1 AS x FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ai_concepts'");
    $timelineTable = (bool) db_one("SELECT 1 AS x FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ai_timeline'");

    $catalog = kepintaran_buatan_kssm_catalog();
    $topicsCreated = $topicsKept = $topicsAdopted = 0;
    $subCreated = $subKept = 0;
    $skillsCreated = $skillsKept = 0;
    $conceptsCreated = $conceptsKept = 0;
    $timelineCreated = $timelineKept = 0;

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
        foreach (kepintaran_buatan_kssm_concepts() as $idx => [$topicName, $type, $name, $def, $code, $lang, $example, $category, $year]) {
            $tid = $topicIds[$topicName] ?? null;
            $existing = db_one('SELECT id FROM ai_concepts WHERE name = ? LIMIT 1', [$name]);
            if ($existing) {
                $conceptsKept++;
            } else {
                db_exec(
                    'INSERT INTO ai_concepts (topic_id, topic_label, type, name, definition, code_snippet, language, example, category, year_invented, sort_order)
                     VALUES (?,?,?,?,?,?,?,?,?,?,?)',
                    [$tid, $topicName, $type, $name, $def, $code, $lang, $example, $category, $year, $idx + 1]
                );
                $conceptsCreated++;
            }
        }
    }

    if ($timelineTable) {
        foreach (kepintaran_buatan_kssm_timeline() as $idx => [$topicName, $era, $year, $date, $title, $desc, $importance]) {
            $tid = $topicIds[$topicName] ?? null;
            $existing = db_one('SELECT id FROM ai_timeline WHERE title = ? LIMIT 1', [$title]);
            if ($existing) {
                $timelineKept++;
            } else {
                db_exec(
                    'INSERT INTO ai_timeline (topic_id, topic_label, era, year_label, event_date, title, description, importance, sort_order)
                     VALUES (?,?,?,?,?,?,?,?,?)',
                    [$tid, $topicName, $era, $year, $date, $title, $desc, $importance, $idx + 1]
                );
                $timelineCreated++;
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
        'timeline_created'   => $timelineCreated,
        'timeline_kept'      => $timelineKept,
        'catalog_topics'     => count($catalog),
        'concepts_table'     => $conceptsTable,
        'timeline_table'     => $timelineTable,
    ];
}

if (PHP_SAPI === 'cli') {
    $r = kepintaran_buatan_kssm_run();
    if (($r['status'] ?? '') !== 'ok') {
        fwrite(STDERR, ($r['message'] ?? 'unknown error') . "\n");
        exit(1);
    }
    echo "Asas Kepintaran Buatan seeded —\n";
    echo "  Bab:        created {$r['topics_created']}, adopted {$r['topics_adopted']}, kept {$r['topics_kept']} (catalog {$r['catalog_topics']})\n";
    echo "  Subtopik:   created {$r['subtopics_created']}, kept {$r['subtopics_kept']}\n";
    echo "  Kemahiran:  created {$r['skills_created']}, kept {$r['skills_kept']}\n";
    if ($r['concepts_table']) {
        echo "  Konsep:     created {$r['concepts_created']}, kept {$r['concepts_kept']}\n";
    } else {
        echo "  Konsep:     table missing — run database migrations to enable.\n";
    }
    if ($r['timeline_table']) {
        echo "  Timeline:   created {$r['timeline_created']}, kept {$r['timeline_kept']}\n";
    } else {
        echo "  Timeline:   table missing — run database migrations to enable.\n";
    }
}
