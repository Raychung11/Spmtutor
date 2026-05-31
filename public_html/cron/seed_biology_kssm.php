<?php
/**
 * KSSM SPM Biology syllabus seeder. Form 4 (11 chapters) + Form 5
 * (10 chapters) with subtopics + starter skills, plus a starter diagram
 * bank and flashcard bank.
 *
 * Idempotent: topics matched by (subject_id, name, form_level); legacy
 * same-named topics with NULL form_level are adopted.
 *
 *   php public_html/cron/seed_biology_kssm.php
 */
declare(strict_types=1);

require_once __DIR__ . '/../inc/db.php';

function biology_kssm_catalog(): array
{
    return [
        // ============= FORM 4 =============
        ['form' => 4, 'name' => 'Introduction to Biology and Laboratory Rules', 'subtopics' => [
            'Biology and Career', 'Scientific Investigation', 'Laboratory Safety', 'Scientific Method',
        ], 'skills' => [
            ['Conduct investigations', 'medium'],
            ['Apply scientific methods', 'easy'],
            ['Follow lab procedures', 'easy'],
        ]],
        ['form' => 4, 'name' => 'Cell Biology and Organisation', 'subtopics' => [
            'Cell Structure', 'Animal Cell', 'Plant Cell', 'Cell Organelles', 'Cell Organisation',
        ], 'skills' => [
            ['Label cell structures', 'easy'],
            ['Compare plant and animal cells', 'medium'],
            ['Explain organelle functions', 'medium'],
        ]],
        ['form' => 4, 'name' => 'Movement of Substances Across Plasma Membrane', 'subtopics' => [
            'Diffusion', 'Osmosis', 'Active Transport', 'Passive Transport',
        ], 'skills' => [
            ['Explain membrane transport', 'medium'],
            ['Compare transport mechanisms', 'medium'],
            ['Apply osmosis concepts', 'medium'],
        ]],
        ['form' => 4, 'name' => 'Chemical Composition in Cells', 'subtopics' => [
            'Water', 'Carbohydrates', 'Proteins', 'Lipids', 'Nucleic Acids',
        ], 'skills' => [
            ['Identify biomolecules', 'easy'],
            ['Explain functions', 'medium'],
            ['Analyze food content', 'medium'],
        ]],
        ['form' => 4, 'name' => 'Metabolism and Enzymes', 'subtopics' => [
            'Metabolism', 'Catabolism', 'Anabolism', 'Enzymes',
        ], 'skills' => [
            ['Explain enzyme action', 'medium'],
            ['Analyze enzyme factors', 'medium'],
            ['Interpret experiments', 'hard'],
        ]],
        ['form' => 4, 'name' => 'Cell Division', 'subtopics' => [
            'Mitosis', 'Meiosis', 'Chromosomes',
        ], 'skills' => [
            ['Compare mitosis and meiosis', 'medium'],
            ['Explain cell division', 'medium'],
            ['Analyze genetic outcomes', 'hard'],
        ]],
        ['form' => 4, 'name' => 'Respiration', 'subtopics' => [
            'Aerobic Respiration', 'Anaerobic Respiration', 'Energy Production',
        ], 'skills' => [
            ['Compare respiration types', 'medium'],
            ['Explain ATP production', 'medium'],
        ]],
        ['form' => 4, 'name' => 'Nutrition', 'subtopics' => [
            'Digestive System', 'Balanced Diet', 'Digestion', 'Absorption',
        ], 'skills' => [
            ['Analyze nutrition', 'medium'],
            ['Explain digestion', 'medium'],
            ['Calculate caloric needs', 'medium'],
        ]],
        ['form' => 4, 'name' => 'Human Digestive System', 'subtopics' => [
            'Digestive Organs', 'Enzymes', 'Absorption Process',
        ], 'skills' => [
            ['Label digestive organs', 'easy'],
            ['Explain digestion process', 'medium'],
        ]],
        ['form' => 4, 'name' => 'Transport in Humans and Animals', 'subtopics' => [
            'Blood Components', 'Circulatory System', 'Heart', 'Blood Vessels',
        ], 'skills' => [
            ['Explain blood circulation', 'medium'],
            ['Analyze cardiovascular functions', 'medium'],
        ]],
        ['form' => 4, 'name' => 'Immunity', 'subtopics' => [
            'Body Defense', 'Antigens', 'Antibodies', 'Vaccination',
        ], 'skills' => [
            ['Explain immune responses', 'medium'],
            ['Differentiate immunity types', 'medium'],
        ]],

        // ============= FORM 5 =============
        ['form' => 5, 'name' => 'Transport in Plants', 'subtopics' => [
            'Xylem', 'Phloem', 'Transpiration',
        ], 'skills' => [
            ['Explain transport mechanisms', 'medium'],
            ['Analyze water movement', 'medium'],
        ]],
        ['form' => 5, 'name' => 'Ecosystem', 'subtopics' => [
            'Food Chains', 'Food Webs', 'Energy Flow', 'Ecological Pyramid',
        ], 'skills' => [
            ['Construct food chains', 'easy'],
            ['Explain ecological relationships', 'medium'],
        ]],
        ['form' => 5, 'name' => 'Sustainability of Environment', 'subtopics' => [
            'Pollution', 'Conservation', 'Sustainability',
        ], 'skills' => [
            ['Evaluate environmental issues', 'medium'],
            ['Propose solutions', 'medium'],
        ]],
        ['form' => 5, 'name' => 'Reproduction and Growth', 'subtopics' => [
            'Human Reproduction', 'Menstrual Cycle', 'Fertilisation', 'Growth',
        ], 'skills' => [
            ['Explain reproductive processes', 'medium'],
            ['Analyze growth patterns', 'medium'],
        ]],
        ['form' => 5, 'name' => 'Variation', 'subtopics' => [
            'Continuous Variation', 'Discontinuous Variation', 'Genetic Factors',
        ], 'skills' => [
            ['Compare variation types', 'medium'],
            ['Analyze inheritance patterns', 'medium'],
        ]],
        ['form' => 5, 'name' => 'Inheritance', 'subtopics' => [
            'Mendelian Genetics', 'Dominant Traits', 'Recessive Traits', 'Monohybrid Cross',
        ], 'skills' => [
            ['Draw genetic diagrams', 'medium'],
            ['Predict inheritance outcomes', 'hard'],
        ]],
        ['form' => 5, 'name' => 'Evolution', 'subtopics' => [
            'Natural Selection', 'Adaptation', 'Evolution Theory',
        ], 'skills' => [
            ['Explain evolution', 'medium'],
            ['Analyze adaptation examples', 'medium'],
        ]],
        ['form' => 5, 'name' => 'Technology in Genetics', 'subtopics' => [
            'Genetic Engineering', 'Biotechnology', 'DNA Profiling',
        ], 'skills' => [
            ['Explain biotechnology applications', 'medium'],
            ['Evaluate ethical issues', 'hard'],
        ]],
        ['form' => 5, 'name' => 'Health and Human Body', 'subtopics' => [
            'Diseases', 'Lifestyle Disorders', 'Health Management',
        ], 'skills' => [
            ['Analyze health risks', 'medium'],
            ['Recommend healthy practices', 'easy'],
        ]],
        ['form' => 5, 'name' => 'Biotechnology and Sustainability', 'subtopics' => [
            'Modern Biotechnology', 'Bioeconomy', 'Sustainable Development',
        ], 'skills' => [
            ['Explain biotechnology impacts', 'medium'],
            ['Evaluate sustainability benefits', 'medium'],
        ]],
    ];
}

/** Starter diagram bank. image_url is left NULL — admins upload later. */
function biology_kssm_diagrams(): array
{
    return [
        ['Cell Biology and Organisation',                  'Animal Cell',            'Typical animal cell with major organelles.', 'nucleus|cytoplasm|cell membrane|mitochondrion|ribosome|endoplasmic reticulum|golgi apparatus|lysosome'],
        ['Cell Biology and Organisation',                  'Plant Cell',             'Typical plant cell — note the rigid cell wall and chloroplasts.', 'cell wall|cell membrane|nucleus|chloroplast|vacuole|mitochondrion|cytoplasm'],
        ['Cell Biology and Organisation',                  'Cell Organelles Overview', 'Side-by-side organelle structure & function reference.', 'mitochondrion|chloroplast|ribosome|nucleus|ER|golgi'],
        ['Movement of Substances Across Plasma Membrane',  'Plasma Membrane',        'Fluid mosaic model of the cell membrane.', 'phospholipid bilayer|integral protein|peripheral protein|cholesterol|glycoprotein'],
        ['Chemical Composition in Cells',                  'DNA Double Helix',       'Watson–Crick double helix structure.', 'sugar-phosphate backbone|adenine|thymine|cytosine|guanine|hydrogen bonds'],
        ['Cell Division',                                  'Stages of Mitosis',      'Prophase → metaphase → anaphase → telophase.', 'prophase|metaphase|anaphase|telophase|cytokinesis'],
        ['Cell Division',                                  'Stages of Meiosis',      'Meiosis I and II producing four haploid cells.', 'meiosis I|meiosis II|crossing over|tetrad|haploid'],
        ['Respiration',                                    'Mitochondrion',          'Cross-section showing cristae and matrix.', 'outer membrane|inner membrane|cristae|matrix|intermembrane space'],
        ['Human Digestive System',                         'Human Digestive System', 'Full alimentary canal from mouth to anus.', 'mouth|oesophagus|stomach|small intestine|large intestine|liver|pancreas|rectum|anus'],
        ['Transport in Humans and Animals',                'Human Heart',            'Four-chamber heart with major vessels.', 'right atrium|left atrium|right ventricle|left ventricle|aorta|pulmonary artery|vena cava|valves'],
        ['Transport in Humans and Animals',                'Blood Vessels',          'Artery vs vein vs capillary cross-sections.', 'artery|vein|capillary|tunica intima|tunica media|tunica externa'],
        ['Transport in Humans and Animals',                'Blood Components',       'Plasma + red cells + white cells + platelets.', 'red blood cell|white blood cell|platelet|plasma'],
        ['Immunity',                                       'Antibody Structure',     'Y-shaped immunoglobulin with antigen-binding sites.', 'heavy chain|light chain|antigen-binding site|constant region|variable region'],
        ['Transport in Plants',                            'Xylem and Phloem',       'Vascular tissue cross-section in a dicot stem.', 'xylem vessel|phloem sieve tube|companion cell|cambium|cortex'],
        ['Transport in Plants',                            'Transpiration Pathway',  'Root → stem → leaf water movement.', 'root hair|cortex|xylem|stomata|guard cells'],
        ['Ecosystem',                                      'Food Web',               'Example tropical food web with producers and consumers.', 'producer|primary consumer|secondary consumer|tertiary consumer|decomposer'],
        ['Ecosystem',                                      'Ecological Pyramid',     'Pyramid of energy / biomass / numbers.', 'producer|herbivore|carnivore|apex predator'],
        ['Reproduction and Growth',                        'Male Reproductive System', 'Side view of male reproductive anatomy.', 'testis|epididymis|vas deferens|prostate|urethra|penis'],
        ['Reproduction and Growth',                        'Female Reproductive System', 'Front view of female reproductive anatomy.', 'ovary|fallopian tube|uterus|cervix|vagina|endometrium'],
        ['Reproduction and Growth',                        'Menstrual Cycle',        '28-day hormonal cycle with endometrium thickness.', 'menstruation|follicular phase|ovulation|luteal phase|FSH|LH|oestrogen|progesterone'],
        ['Inheritance',                                    'Monohybrid Cross',       'Punnett square for a Tt × Tt cross.', 'TT|Tt|tt|dominant|recessive|3:1 ratio'],
        ['Health and Human Body',                          'Nephron Structure',      'Functional unit of the kidney.', 'glomerulus|bowmans capsule|proximal tubule|loop of henle|distal tubule|collecting duct'],
    ];
}

/** Starter flashcard bank. Topic match against catalog topic name. */
function biology_kssm_flashcards(): array
{
    return [
        ['Cell Biology and Organisation', 'What is the function of mitochondria?', 'Produces ATP through cellular (aerobic) respiration — the "powerhouse" of the cell.', 'easy'],
        ['Cell Biology and Organisation', 'What is the function of the nucleus?', 'Controls the activities of the cell and contains the genetic material (DNA).', 'easy'],
        ['Cell Biology and Organisation', 'Which organelle is responsible for photosynthesis?', 'Chloroplast — found only in plant cells; contains chlorophyll.', 'easy'],
        ['Cell Biology and Organisation', 'Name two structures found in plant cells but not animal cells.', 'Cell wall and chloroplasts (and typically a large central vacuole).', 'easy'],
        ['Cell Biology and Organisation', 'What is the role of ribosomes?', 'Site of protein synthesis (translation of mRNA into polypeptides).', 'easy'],
        ['Cell Biology and Organisation', 'What is the function of the rough endoplasmic reticulum?', 'Synthesizes and transports proteins; has ribosomes attached to its surface.', 'medium'],
        ['Cell Biology and Organisation', 'What is the role of the Golgi apparatus?', 'Modifies, packages and ships proteins/lipids — often into vesicles.', 'medium'],

        ['Movement of Substances Across Plasma Membrane', 'Define diffusion.', 'Net movement of particles from a region of higher to lower concentration, down the concentration gradient.', 'easy'],
        ['Movement of Substances Across Plasma Membrane', 'Define osmosis.', 'Net movement of water molecules from a dilute (high water potential) to a more concentrated (low water potential) solution across a partially permeable membrane.', 'easy'],
        ['Movement of Substances Across Plasma Membrane', 'How is active transport different from diffusion?', 'Active transport moves substances against the concentration gradient and requires ATP; diffusion is passive and follows the gradient.', 'medium'],
        ['Movement of Substances Across Plasma Membrane', 'What happens to a red blood cell placed in a hypotonic solution?', 'Water enters by osmosis, the cell swells and may burst (haemolysis).', 'medium'],

        ['Chemical Composition in Cells', 'Name the four main biomolecules in cells.', 'Carbohydrates, proteins, lipids and nucleic acids.', 'easy'],
        ['Chemical Composition in Cells', 'What is the building block of proteins?', 'Amino acids — joined by peptide bonds.', 'easy'],
        ['Chemical Composition in Cells', 'What food test is used for starch?', 'Iodine solution turns from brown/yellow to blue-black.', 'easy'],
        ['Chemical Composition in Cells', 'What food test is used for reducing sugars?', 'Benedict\'s solution — heated; brick-red precipitate indicates a positive result.', 'easy'],

        ['Metabolism and Enzymes', 'Define an enzyme.', 'A biological catalyst (protein) that speeds up a specific biochemical reaction without being consumed.', 'easy'],
        ['Metabolism and Enzymes', 'How does temperature affect enzyme activity?', 'Rate rises with temperature up to the optimum, then drops sharply as the enzyme denatures (active site changes shape).', 'medium'],
        ['Metabolism and Enzymes', 'What is meant by enzyme specificity?', 'Each enzyme has an active site that fits only one or a small group of substrates (lock-and-key / induced-fit model).', 'medium'],

        ['Cell Division', 'How many daughter cells does mitosis produce?', 'Two genetically identical diploid daughter cells.', 'easy'],
        ['Cell Division', 'How many daughter cells does meiosis produce?', 'Four genetically different haploid daughter cells.', 'easy'],
        ['Cell Division', 'In which stage of mitosis do chromosomes line up at the equator?', 'Metaphase.', 'easy'],
        ['Cell Division', 'What is crossing over and when does it happen?', 'Exchange of segments between homologous chromosomes during prophase I of meiosis — increases genetic variation.', 'hard'],

        ['Respiration', 'Write the word equation for aerobic respiration.', 'Glucose + oxygen → carbon dioxide + water + energy (ATP).', 'easy'],
        ['Respiration', 'What is produced by anaerobic respiration in muscle cells?', 'Lactic acid (and a small amount of ATP).', 'easy'],
        ['Respiration', 'How much more ATP does aerobic respiration produce compared with anaerobic?', 'Aerobic ≈ 36–38 ATP per glucose; anaerobic only 2 ATP.', 'medium'],

        ['Nutrition', 'Name the seven classes of food in a balanced diet.', 'Carbohydrates, proteins, lipids, vitamins, minerals, water and fibre (roughage).', 'easy'],
        ['Nutrition', 'What is the function of dietary fibre?', 'Adds bulk to faeces, prevents constipation and helps the muscles of the gut work properly.', 'easy'],

        ['Human Digestive System', 'Which enzyme digests starch in the mouth?', 'Salivary amylase (ptyalin) — breaks starch into maltose.', 'easy'],
        ['Human Digestive System', 'Where does most absorption of digested food take place?', 'In the small intestine, especially the ileum, via villi and microvilli.', 'easy'],
        ['Human Digestive System', 'What is the role of bile in digestion?', 'Emulsifies lipids into small droplets so lipase can digest them more efficiently. Bile is produced by the liver and stored in the gall bladder.', 'medium'],

        ['Transport in Humans and Animals', 'List the four chambers of the human heart.', 'Right atrium, right ventricle, left atrium, left ventricle.', 'easy'],
        ['Transport in Humans and Animals', 'Which side of the heart pumps oxygenated blood to the body?', 'The left side (left ventricle → aorta).', 'easy'],
        ['Transport in Humans and Animals', 'Why do arteries have thicker walls than veins?', 'They carry blood at higher pressure from the heart, so they need thick muscular and elastic walls.', 'medium'],
        ['Transport in Humans and Animals', 'What is the function of valves in veins?', 'Prevent backflow of blood so it flows in one direction back to the heart.', 'easy'],
        ['Transport in Humans and Animals', 'Why are red blood cells biconcave and have no nucleus?', 'Biconcave shape increases surface area for gas exchange; lack of nucleus leaves more room for haemoglobin.', 'medium'],

        ['Immunity', 'What is an antigen?', 'A foreign molecule (usually a protein on the surface of a pathogen) that triggers an immune response.', 'easy'],
        ['Immunity', 'What is an antibody?', 'A Y-shaped protein produced by B-lymphocytes that binds to a specific antigen.', 'easy'],
        ['Immunity', 'What is the difference between active and passive immunity?', 'Active immunity is produced by your own immune system (vaccine or infection) and is long-lasting; passive immunity is borrowed antibodies (e.g. from mother) and is short-lived.', 'medium'],

        ['Transport in Plants', 'What does xylem transport?', 'Water and dissolved mineral ions from roots to leaves (upward only).', 'easy'],
        ['Transport in Plants', 'What does phloem transport?', 'Sucrose and amino acids from the leaves (source) to the rest of the plant (sink) — translocation, in both directions.', 'medium'],
        ['Transport in Plants', 'Name three factors that increase the rate of transpiration.', 'Higher temperature, lower humidity, increased air movement (wind), and higher light intensity.', 'medium'],

        ['Ecosystem', 'What is a food chain?', 'A linear sequence showing how energy and nutrients pass from one organism to the next, starting with a producer.', 'easy'],
        ['Ecosystem', 'Why are there usually fewer top predators than producers?', 'Energy is lost (as heat, in respiration and excretion) at each trophic level, so less is available higher up the chain.', 'medium'],
        ['Ecosystem', 'What is the role of decomposers?', 'Break down dead organic matter, returning nutrients (e.g. nitrogen) to the soil.', 'easy'],

        ['Sustainability of Environment', 'Name two greenhouse gases.', 'Carbon dioxide (CO₂) and methane (CH₄). (Also water vapour, nitrous oxide.)', 'easy'],
        ['Sustainability of Environment', 'What is one effect of deforestation?', 'Loss of biodiversity, increased CO₂ in the atmosphere, soil erosion, and disruption of the water cycle.', 'medium'],

        ['Reproduction and Growth', 'Where does fertilisation usually occur in humans?', 'In the fallopian tube (oviduct).', 'easy'],
        ['Reproduction and Growth', 'What is the function of the placenta?', 'Allows exchange of gases, nutrients and waste between mother and foetus, and produces hormones to maintain pregnancy.', 'medium'],
        ['Reproduction and Growth', 'Which hormone triggers ovulation?', 'Luteinising hormone (LH) — surge around day 14 of the cycle.', 'medium'],

        ['Variation', 'Give one example of continuous variation in humans.', 'Height, mass, or skin colour — quantitative traits influenced by many genes and environment.', 'easy'],
        ['Variation', 'Give one example of discontinuous variation in humans.', 'Blood group (A/B/AB/O), tongue rolling, or attached/free earlobes — controlled by a single gene with distinct categories.', 'easy'],

        ['Inheritance', 'What does "genotype" mean?', 'The genetic make-up of an organism for a particular trait (e.g. Tt).', 'easy'],
        ['Inheritance', 'What does "phenotype" mean?', 'The observable characteristic of an organism (e.g. tall plant).', 'easy'],
        ['Inheritance', 'In a Tt × Tt cross, what is the expected phenotype ratio?', '3 dominant : 1 recessive (e.g. 3 tall : 1 short).', 'medium'],
        ['Inheritance', 'What is a test cross?', 'A cross with a homozygous recessive individual to determine whether an unknown is homozygous or heterozygous dominant.', 'hard'],

        ['Evolution', 'Who proposed the theory of evolution by natural selection?', 'Charles Darwin (and independently Alfred Russel Wallace).', 'easy'],
        ['Evolution', 'State the four principles of natural selection.', '1. Variation exists in a population. 2. More offspring are produced than survive. 3. Individuals with favourable traits are more likely to survive and reproduce. 4. Favourable traits accumulate over generations.', 'medium'],

        ['Technology in Genetics', 'What is genetic engineering?', 'The deliberate modification of an organism\'s DNA by adding, removing or altering genes — often using restriction enzymes and vectors (plasmids).', 'medium'],
        ['Technology in Genetics', 'Give one beneficial application of biotechnology.', 'Production of human insulin by genetically modified bacteria; golden rice with extra vitamin A; disease-resistant crops.', 'easy'],

        ['Health and Human Body', 'Name two non-communicable lifestyle diseases.', 'Type 2 diabetes, coronary heart disease, hypertension, obesity.', 'easy'],
        ['Health and Human Body', 'Name three ways to reduce the risk of cardiovascular disease.', 'Balanced low-fat diet, regular exercise, no smoking, limited alcohol, manage stress.', 'easy'],

        ['Biotechnology and Sustainability', 'What is bioeconomy?', 'An economy that uses renewable biological resources (plants, animals, microorganisms) to produce food, materials and energy sustainably.', 'medium'],
        ['Biotechnology and Sustainability', 'Give one example of modern biotechnology that supports sustainability.', 'Biofuels from algae, biodegradable plastics from PHA-producing bacteria, or enzyme-based industrial processes that use less energy.', 'medium'],
    ];
}

function biology_kssm_run(): array
{
    $subject = db_one("SELECT id FROM subjects WHERE slug = 'biology' LIMIT 1");
    if (!$subject) {
        return ['status' => 'no_subject', 'message' => 'Biology subject not found. Run install / seed_subjects first.'];
    }
    $sid = (int) $subject['id'];

    $diagramsTable   = (bool) db_one("SELECT 1 AS x FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'biology_diagrams'");
    $flashcardsTable = (bool) db_one("SELECT 1 AS x FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'biology_flashcards'");

    $catalog = biology_kssm_catalog();
    $topicsCreated = $topicsKept = $topicsAdopted = 0;
    $subCreated = $subKept = 0;
    $skillsCreated = $skillsKept = 0;
    $diagramsCreated = $diagramsKept = 0;
    $cardsCreated = $cardsKept = 0;

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

    if ($diagramsTable) {
        foreach (biology_kssm_diagrams() as $idx => [$topicName, $title, $desc, $labels]) {
            $tid = $topicIds[$topicName] ?? null;
            $existing = db_one(
                'SELECT id FROM biology_diagrams WHERE topic_id <=> ? AND title = ? LIMIT 1',
                [$tid, $title]
            );
            if ($existing) {
                $diagramsKept++;
            } else {
                db_exec(
                    'INSERT INTO biology_diagrams (topic_id, topic_label, title, description, labels, sort_order)
                     VALUES (?, ?, ?, ?, ?, ?)',
                    [$tid, $topicName, $title, $desc, $labels, $idx + 1]
                );
                $diagramsCreated++;
            }
        }
    }

    if ($flashcardsTable) {
        foreach (biology_kssm_flashcards() as $idx => [$topicName, $q, $a, $diff]) {
            $tid = $topicIds[$topicName] ?? null;
            $existing = db_one(
                'SELECT id FROM biology_flashcards WHERE topic_id <=> ? AND question = ? LIMIT 1',
                [$tid, $q]
            );
            if ($existing) {
                $cardsKept++;
            } else {
                db_exec(
                    'INSERT INTO biology_flashcards (topic_id, topic_label, question, answer, difficulty, sort_order)
                     VALUES (?, ?, ?, ?, ?, ?)',
                    [$tid, $topicName, $q, $a, $diff, $idx + 1]
                );
                $cardsCreated++;
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
        'diagrams_created'   => $diagramsCreated,
        'diagrams_kept'      => $diagramsKept,
        'flashcards_created' => $cardsCreated,
        'flashcards_kept'    => $cardsKept,
        'catalog_topics'     => count($catalog),
        'diagrams_table'     => $diagramsTable,
        'flashcards_table'   => $flashcardsTable,
    ];
}

if (PHP_SAPI === 'cli') {
    $r = biology_kssm_run();
    if (($r['status'] ?? '') !== 'ok') {
        fwrite(STDERR, ($r['message'] ?? 'unknown error') . "\n");
        exit(1);
    }
    echo "KSSM Biology seeded —\n";
    echo "  Topics:     created {$r['topics_created']}, adopted {$r['topics_adopted']}, kept {$r['topics_kept']} (catalog {$r['catalog_topics']})\n";
    echo "  Subtopics:  created {$r['subtopics_created']}, kept {$r['subtopics_kept']}\n";
    echo "  Skills:     created {$r['skills_created']}, kept {$r['skills_kept']}\n";
    if ($r['diagrams_table']) {
        echo "  Diagrams:   created {$r['diagrams_created']}, kept {$r['diagrams_kept']}\n";
    } else {
        echo "  Diagrams:   table missing — run migrations to enable.\n";
    }
    if ($r['flashcards_table']) {
        echo "  Flashcards: created {$r['flashcards_created']}, kept {$r['flashcards_kept']}\n";
    } else {
        echo "  Flashcards: table missing — run migrations to enable.\n";
    }
}
