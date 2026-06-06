<?php
/**
 * Idempotent course seeder. Adds topics, skills and MCQs across all
 * subjects. Skips items that already exist (by slug/name/question text),
 * so it is safe to run multiple times.
 *
 *   php public_html/cron/seed_courses.php
 *
 * Also called from install.php after seed.sql + content.sql so fresh
 * installs get the full catalog automatically.
 */
declare(strict_types=1);

require_once __DIR__ . '/../inc/db.php';

/**
 * Define the catalog as nested PHP data. Each subject is keyed by its slug;
 * topics by slug (within the subject); questions are MCQs with 4 options.
 */
function seed_courses_catalog(): array
{
    return [
        // ===== Mathematics =====
        'mathematics' => [
            ['name' => 'Algebra', 'slug' => 'algebra', 'sort_order' => 1, 'skills' => [
                ['name' => 'Factorise quadratic expressions', 'difficulty' => 'medium'],
                ['name' => 'Solve simultaneous linear equations', 'difficulty' => 'medium'],
                ['name' => 'Solve linear inequalities', 'difficulty' => 'easy'],
            ], 'questions' => [
                ['text' => 'Factorise x^2 - 9.', 'difficulty' => 'easy', 'explanation' => 'Difference of two squares: a^2 - b^2 = (a-b)(a+b), so x^2 - 9 = (x-3)(x+3).', 'options' => [
                    ['text' => '(x-3)(x+3)', 'correct' => true],
                    ['text' => '(x-9)(x+1)', 'correct' => false],
                    ['text' => '(x-3)^2', 'correct' => false],
                    ['text' => '(x+3)^2', 'correct' => false],
                ]],
                ['text' => 'Solve the simultaneous equations: x + y = 7 and x - y = 1.', 'difficulty' => 'medium', 'explanation' => 'Add the equations: 2x = 8 so x = 4, then y = 3.', 'options' => [
                    ['text' => 'x = 4, y = 3', 'correct' => true],
                    ['text' => 'x = 3, y = 4', 'correct' => false],
                    ['text' => 'x = 5, y = 2', 'correct' => false],
                    ['text' => 'x = 6, y = 1', 'correct' => false],
                ]],
                ['text' => 'Solve the inequality 2x - 5 > 1.', 'difficulty' => 'easy', 'explanation' => 'Add 5: 2x > 6, then divide by 2: x > 3.', 'options' => [
                    ['text' => 'x > 3', 'correct' => true],
                    ['text' => 'x < 3', 'correct' => false],
                    ['text' => 'x >= 3', 'correct' => false],
                    ['text' => 'x > 6', 'correct' => false],
                ]],
            ]],
            ['name' => 'Geometry', 'slug' => 'geometry', 'sort_order' => 2, 'skills' => [
                ['name' => 'Compute areas of common shapes', 'difficulty' => 'easy'],
                ['name' => 'Use angle properties of triangles and lines', 'difficulty' => 'easy'],
            ], 'questions' => [
                ['text' => 'What is the area of a triangle with base 10 cm and height 6 cm?', 'difficulty' => 'easy', 'explanation' => 'Area = 1/2 x base x height = 1/2 x 10 x 6 = 30 cm^2.', 'options' => [
                    ['text' => '30 cm^2', 'correct' => true],
                    ['text' => '60 cm^2', 'correct' => false],
                    ['text' => '16 cm^2', 'correct' => false],
                    ['text' => '15 cm^2', 'correct' => false],
                ]],
                ['text' => 'The sum of the interior angles of any triangle is:', 'difficulty' => 'easy', 'explanation' => 'A triangle\'s interior angles always sum to 180 degrees.', 'options' => [
                    ['text' => '90 degrees', 'correct' => false],
                    ['text' => '180 degrees', 'correct' => true],
                    ['text' => '270 degrees', 'correct' => false],
                    ['text' => '360 degrees', 'correct' => false],
                ]],
                ['text' => 'The circumference of a circle of radius 7 cm (use pi = 22/7) is:', 'difficulty' => 'medium', 'explanation' => 'C = 2 x pi x r = 2 x 22/7 x 7 = 44 cm.', 'options' => [
                    ['text' => '22 cm', 'correct' => false],
                    ['text' => '44 cm', 'correct' => true],
                    ['text' => '49 cm', 'correct' => false],
                    ['text' => '154 cm', 'correct' => false],
                ]],
            ]],
            ['name' => 'Trigonometry', 'slug' => 'trigonometry', 'sort_order' => 3, 'questions' => [
                ['text' => 'What is the value of sin 30 degrees?', 'difficulty' => 'easy', 'explanation' => 'sin 30 = 1/2.', 'options' => [
                    ['text' => '0', 'correct' => false],
                    ['text' => '1/2', 'correct' => true],
                    ['text' => 'sqrt(3)/2', 'correct' => false],
                    ['text' => '1', 'correct' => false],
                ]],
                ['text' => 'In a right-angled triangle, the side opposite the right angle is called the:', 'difficulty' => 'easy', 'explanation' => 'It is called the hypotenuse — the longest side.', 'options' => [
                    ['text' => 'Adjacent', 'correct' => false],
                    ['text' => 'Opposite', 'correct' => false],
                    ['text' => 'Hypotenuse', 'correct' => true],
                    ['text' => 'Base', 'correct' => false],
                ]],
                ['text' => 'tan 45 degrees equals:', 'difficulty' => 'easy', 'explanation' => 'tan 45 = sin 45 / cos 45 = 1.', 'options' => [
                    ['text' => '0', 'correct' => false],
                    ['text' => '1/2', 'correct' => false],
                    ['text' => '1', 'correct' => true],
                    ['text' => 'sqrt(3)', 'correct' => false],
                ]],
            ]],
            ['name' => 'Statistics', 'slug' => 'statistics', 'sort_order' => 4, 'questions' => [
                ['text' => 'What is the mean of 2, 4, 6, 8?', 'difficulty' => 'easy', 'explanation' => 'Mean = (2+4+6+8)/4 = 20/4 = 5.', 'options' => [
                    ['text' => '4', 'correct' => false],
                    ['text' => '5', 'correct' => true],
                    ['text' => '6', 'correct' => false],
                    ['text' => '20', 'correct' => false],
                ]],
                ['text' => 'What is the median of 3, 5, 9, 11, 15?', 'difficulty' => 'easy', 'explanation' => 'The middle value of an ordered list is the median; here it is 9.', 'options' => [
                    ['text' => '5', 'correct' => false],
                    ['text' => '9', 'correct' => true],
                    ['text' => '11', 'correct' => false],
                    ['text' => '8.6', 'correct' => false],
                ]],
                ['text' => 'A fair six-sided die is rolled. The probability of getting an even number is:', 'difficulty' => 'easy', 'explanation' => '3 even outcomes (2, 4, 6) out of 6 = 1/2.', 'options' => [
                    ['text' => '1/6', 'correct' => false],
                    ['text' => '1/3', 'correct' => false],
                    ['text' => '1/2', 'correct' => true],
                    ['text' => '2/3', 'correct' => false],
                ]],
            ]],
        ],

        // ===== Additional Mathematics =====
        'add-maths' => [
            ['name' => 'Functions', 'slug' => 'am-functions', 'sort_order' => 1, 'questions' => [
                ['text' => 'If f(x) = 2x + 1 and g(x) = x^2, find f(g(2)).', 'difficulty' => 'medium', 'explanation' => 'g(2) = 4; f(4) = 2(4) + 1 = 9.', 'options' => [
                    ['text' => '5', 'correct' => false],
                    ['text' => '9', 'correct' => true],
                    ['text' => '25', 'correct' => false],
                    ['text' => '13', 'correct' => false],
                ]],
                ['text' => 'Find the inverse function of f(x) = 2x - 1.', 'difficulty' => 'medium', 'explanation' => 'y = 2x - 1 => x = (y+1)/2, so f^-1(x) = (x+1)/2.', 'options' => [
                    ['text' => '(x+1)/2', 'correct' => true],
                    ['text' => '(x-1)/2', 'correct' => false],
                    ['text' => '2x + 1', 'correct' => false],
                    ['text' => '1/(2x-1)', 'correct' => false],
                ]],
            ]],
            ['name' => 'Differentiation', 'slug' => 'am-differentiation', 'sort_order' => 2, 'questions' => [
                ['text' => 'Find the derivative of y = 5x^2 evaluated at x = 2.', 'difficulty' => 'medium', 'explanation' => 'dy/dx = 10x; at x = 2 this is 20.', 'options' => [
                    ['text' => '10', 'correct' => false],
                    ['text' => '20', 'correct' => true],
                    ['text' => '25', 'correct' => false],
                    ['text' => '40', 'correct' => false],
                ]],
                ['text' => 'What is the derivative of a constant (e.g. y = 7)?', 'difficulty' => 'easy', 'explanation' => 'The derivative of any constant is 0.', 'options' => [
                    ['text' => '0', 'correct' => true],
                    ['text' => '1', 'correct' => false],
                    ['text' => '7', 'correct' => false],
                    ['text' => 'x', 'correct' => false],
                ]],
            ]],
            ['name' => 'Indices and Logarithms', 'slug' => 'am-indices', 'sort_order' => 3, 'skills' => [
                ['name' => 'Apply index laws', 'difficulty' => 'easy'],
                ['name' => 'Evaluate simple logarithms', 'difficulty' => 'medium'],
            ], 'questions' => [
                ['text' => 'Simplify 2^3 x 2^4.', 'difficulty' => 'easy', 'explanation' => 'Add the indices: 2^(3+4) = 2^7 = 128.', 'options' => [
                    ['text' => '2^7', 'correct' => true],
                    ['text' => '2^12', 'correct' => false],
                    ['text' => '4^7', 'correct' => false],
                    ['text' => '2^1', 'correct' => false],
                ]],
                ['text' => 'Find log10(100).', 'difficulty' => 'easy', 'explanation' => '100 = 10^2, so log10(100) = 2.', 'options' => [
                    ['text' => '1', 'correct' => false],
                    ['text' => '2', 'correct' => true],
                    ['text' => '10', 'correct' => false],
                    ['text' => '100', 'correct' => false],
                ]],
                ['text' => 'Solve 3^x = 27.', 'difficulty' => 'easy', 'explanation' => '27 = 3^3, so x = 3.', 'options' => [
                    ['text' => '2', 'correct' => false],
                    ['text' => '3', 'correct' => true],
                    ['text' => '9', 'correct' => false],
                    ['text' => '27', 'correct' => false],
                ]],
            ]],
            ['name' => 'Coordinate Geometry', 'slug' => 'am-coord', 'sort_order' => 4, 'questions' => [
                ['text' => 'What is the midpoint of the line from (0, 0) to (4, 6)?', 'difficulty' => 'easy', 'explanation' => 'Midpoint = ((0+4)/2, (0+6)/2) = (2, 3).', 'options' => [
                    ['text' => '(2, 3)', 'correct' => true],
                    ['text' => '(4, 6)', 'correct' => false],
                    ['text' => '(1, 1.5)', 'correct' => false],
                    ['text' => '(0, 0)', 'correct' => false],
                ]],
                ['text' => 'What is the distance between (0, 0) and (3, 4)?', 'difficulty' => 'easy', 'explanation' => 'Pythagoras: sqrt(3^2 + 4^2) = sqrt(25) = 5.', 'options' => [
                    ['text' => '5', 'correct' => true],
                    ['text' => '7', 'correct' => false],
                    ['text' => '12', 'correct' => false],
                    ['text' => 'sqrt(7)', 'correct' => false],
                ]],
            ]],
        ],

        // ===== Physics =====
        'physics' => [
            ['name' => 'Forces and Motion', 'slug' => 'phy-forces', 'sort_order' => 1, 'questions' => [
                ['text' => 'What is the weight of a 5 kg mass on Earth? (Use g = 10 N/kg.)', 'difficulty' => 'easy', 'explanation' => 'W = mg = 5 x 10 = 50 N.', 'options' => [
                    ['text' => '0.5 N', 'correct' => false],
                    ['text' => '5 N', 'correct' => false],
                    ['text' => '50 N', 'correct' => true],
                    ['text' => '500 N', 'correct' => false],
                ]],
                ['text' => 'A 2 kg object moves at 3 m/s. What is its momentum?', 'difficulty' => 'easy', 'explanation' => 'p = mv = 2 x 3 = 6 kg m/s.', 'options' => [
                    ['text' => '5 kg m/s', 'correct' => false],
                    ['text' => '6 kg m/s', 'correct' => true],
                    ['text' => '9 kg m/s', 'correct' => false],
                    ['text' => '1.5 kg m/s', 'correct' => false],
                ]],
                ['text' => 'A car travels 100 m in 10 s. What is its average speed?', 'difficulty' => 'easy', 'explanation' => 'speed = distance / time = 100 / 10 = 10 m/s.', 'options' => [
                    ['text' => '1 m/s', 'correct' => false],
                    ['text' => '10 m/s', 'correct' => true],
                    ['text' => '100 m/s', 'correct' => false],
                    ['text' => '1000 m/s', 'correct' => false],
                ]],
            ]],
            ['name' => 'Electricity', 'slug' => 'phy-electricity', 'sort_order' => 2, 'questions' => [
                ['text' => 'A 12 V supply drives a current of 2 A. What is the electrical power?', 'difficulty' => 'easy', 'explanation' => 'P = VI = 12 x 2 = 24 W.', 'options' => [
                    ['text' => '6 W', 'correct' => false],
                    ['text' => '14 W', 'correct' => false],
                    ['text' => '24 W', 'correct' => true],
                    ['text' => '120 W', 'correct' => false],
                ]],
                ['text' => 'How much energy does a 60 W bulb use in 10 seconds?', 'difficulty' => 'easy', 'explanation' => 'E = P x t = 60 x 10 = 600 J.', 'options' => [
                    ['text' => '6 J', 'correct' => false],
                    ['text' => '60 J', 'correct' => false],
                    ['text' => '600 J', 'correct' => true],
                    ['text' => '6000 J', 'correct' => false],
                ]],
                ['text' => 'Two 4 ohm resistors in series have a combined resistance of:', 'difficulty' => 'easy', 'explanation' => 'Series resistances add: 4 + 4 = 8 ohm.', 'options' => [
                    ['text' => '2 ohm', 'correct' => false],
                    ['text' => '4 ohm', 'correct' => false],
                    ['text' => '8 ohm', 'correct' => true],
                    ['text' => '16 ohm', 'correct' => false],
                ]],
            ]],
            ['name' => 'Light and Optics', 'slug' => 'phy-light', 'sort_order' => 3, 'questions' => [
                ['text' => 'According to the law of reflection, the angle of incidence equals the:', 'difficulty' => 'easy', 'explanation' => 'Angle of incidence = angle of reflection, both measured from the normal.', 'options' => [
                    ['text' => 'Angle of refraction', 'correct' => false],
                    ['text' => 'Angle of reflection', 'correct' => true],
                    ['text' => 'Angle of diffraction', 'correct' => false],
                    ['text' => 'Critical angle', 'correct' => false],
                ]],
                ['text' => 'A convex lens is also known as a:', 'difficulty' => 'easy', 'explanation' => 'A convex lens converges parallel rays to a focal point.', 'options' => [
                    ['text' => 'Diverging lens', 'correct' => false],
                    ['text' => 'Converging lens', 'correct' => true],
                    ['text' => 'Plano lens', 'correct' => false],
                    ['text' => 'Cylindrical lens', 'correct' => false],
                ]],
            ]],
            ['name' => 'Heat', 'slug' => 'phy-heat', 'sort_order' => 4, 'questions' => [
                ['text' => 'The SI unit of temperature is the:', 'difficulty' => 'easy', 'explanation' => 'Kelvin (K) is the SI unit of thermodynamic temperature.', 'options' => [
                    ['text' => 'Celsius', 'correct' => false],
                    ['text' => 'Fahrenheit', 'correct' => false],
                    ['text' => 'Kelvin', 'correct' => true],
                    ['text' => 'Joule', 'correct' => false],
                ]],
                ['text' => 'The heat required to raise 2 kg of water by 5 degrees C (c = 4200 J/kg/C) is:', 'difficulty' => 'medium', 'explanation' => 'Q = mcDeltaT = 2 x 4200 x 5 = 42 000 J.', 'options' => [
                    ['text' => '8 400 J', 'correct' => false],
                    ['text' => '21 000 J', 'correct' => false],
                    ['text' => '42 000 J', 'correct' => true],
                    ['text' => '420 000 J', 'correct' => false],
                ]],
            ]],
        ],

        // ===== Chemistry =====
        'chemistry' => [
            ['name' => 'The Mole', 'slug' => 'chem-mole', 'sort_order' => 1, 'questions' => [
                ['text' => 'Avogadro\'s number is approximately:', 'difficulty' => 'easy', 'explanation' => '1 mole contains 6.02 x 10^23 particles.', 'options' => [
                    ['text' => '6.02 x 10^23', 'correct' => true],
                    ['text' => '3.01 x 10^23', 'correct' => false],
                    ['text' => '1.00 x 10^6', 'correct' => false],
                    ['text' => '9.81', 'correct' => false],
                ]],
                ['text' => 'At STP, the molar volume of any ideal gas is approximately:', 'difficulty' => 'easy', 'explanation' => 'At standard temperature and pressure, 1 mole of an ideal gas occupies about 22.4 L.', 'options' => [
                    ['text' => '1 L', 'correct' => false],
                    ['text' => '10 L', 'correct' => false],
                    ['text' => '22.4 L', 'correct' => true],
                    ['text' => '44.8 L', 'correct' => false],
                ]],
            ]],
            ['name' => 'Acids and Bases', 'slug' => 'chem-acids', 'sort_order' => 2, 'questions' => [
                ['text' => 'A solution with pH 4 is best described as:', 'difficulty' => 'easy', 'explanation' => 'pH below 7 is acidic.', 'options' => [
                    ['text' => 'Strongly basic', 'correct' => false],
                    ['text' => 'Weakly basic', 'correct' => false],
                    ['text' => 'Neutral', 'correct' => false],
                    ['text' => 'Acidic', 'correct' => true],
                ]],
                ['text' => 'When hydrochloric acid neutralises sodium hydroxide, the salt formed is:', 'difficulty' => 'easy', 'explanation' => 'HCl + NaOH -> NaCl + H2O. The salt is sodium chloride.', 'options' => [
                    ['text' => 'Sodium nitrate', 'correct' => false],
                    ['text' => 'Sodium chloride', 'correct' => true],
                    ['text' => 'Sodium sulfate', 'correct' => false],
                    ['text' => 'Sodium carbonate', 'correct' => false],
                ]],
            ]],
            ['name' => 'Periodic Table', 'slug' => 'chem-periodic', 'sort_order' => 3, 'questions' => [
                ['text' => 'Group 1 of the periodic table contains the:', 'difficulty' => 'easy', 'explanation' => 'Group 1 = the alkali metals (Li, Na, K, ...).', 'options' => [
                    ['text' => 'Halogens', 'correct' => false],
                    ['text' => 'Alkali metals', 'correct' => true],
                    ['text' => 'Noble gases', 'correct' => false],
                    ['text' => 'Transition metals', 'correct' => false],
                ]],
                ['text' => 'The noble gases are found in which group?', 'difficulty' => 'easy', 'explanation' => 'Noble gases are in Group 18 (also labelled Group 0).', 'options' => [
                    ['text' => 'Group 1', 'correct' => false],
                    ['text' => 'Group 7', 'correct' => false],
                    ['text' => 'Group 17', 'correct' => false],
                    ['text' => 'Group 18', 'correct' => true],
                ]],
            ]],
            ['name' => 'Electrochemistry', 'slug' => 'chem-electrochem', 'sort_order' => 4, 'questions' => [
                ['text' => 'In an electrolytic cell, the electrode where oxidation occurs is the:', 'difficulty' => 'easy', 'explanation' => 'Oxidation happens at the anode (positive electrode in electrolysis).', 'options' => [
                    ['text' => 'Cathode', 'correct' => false],
                    ['text' => 'Anode', 'correct' => true],
                    ['text' => 'Electrolyte', 'correct' => false],
                    ['text' => 'Salt bridge', 'correct' => false],
                ]],
                ['text' => 'Which of the following is the best conductor of electricity in solution?', 'difficulty' => 'easy', 'explanation' => 'Strong electrolytes like NaCl(aq) conduct best; sugar solution does not.', 'options' => [
                    ['text' => 'Sugar solution', 'correct' => false],
                    ['text' => 'Distilled water', 'correct' => false],
                    ['text' => 'Sodium chloride solution', 'correct' => true],
                    ['text' => 'Cooking oil', 'correct' => false],
                ]],
            ]],
        ],

        // ===== Biology =====
        'biology' => [
            ['name' => 'Cell Biology', 'slug' => 'bio-cells', 'sort_order' => 1, 'questions' => [
                ['text' => 'Which structure is found in plant cells but NOT in animal cells?', 'difficulty' => 'easy', 'explanation' => 'Plant cells have a rigid cell wall made of cellulose.', 'options' => [
                    ['text' => 'Cell membrane', 'correct' => false],
                    ['text' => 'Cell wall', 'correct' => true],
                    ['text' => 'Mitochondrion', 'correct' => false],
                    ['text' => 'Nucleus', 'correct' => false],
                ]],
                ['text' => 'The movement of water from a dilute to a concentrated solution through a partially permeable membrane is called:', 'difficulty' => 'easy', 'explanation' => 'This describes osmosis.', 'options' => [
                    ['text' => 'Diffusion', 'correct' => false],
                    ['text' => 'Osmosis', 'correct' => true],
                    ['text' => 'Active transport', 'correct' => false],
                    ['text' => 'Phagocytosis', 'correct' => false],
                ]],
            ]],
            ['name' => 'Genetics', 'slug' => 'bio-genetics', 'sort_order' => 2, 'questions' => [
                ['text' => 'An individual with genotype Aa is described as:', 'difficulty' => 'easy', 'explanation' => 'Two different alleles for a gene means heterozygous.', 'options' => [
                    ['text' => 'Homozygous dominant', 'correct' => false],
                    ['text' => 'Homozygous recessive', 'correct' => false],
                    ['text' => 'Heterozygous', 'correct' => true],
                    ['text' => 'Hemizygous', 'correct' => false],
                ]],
                ['text' => 'In a cross AA x aa, what is the genotype of every F1 offspring?', 'difficulty' => 'easy', 'explanation' => 'Every offspring inherits one A and one a, so all are Aa.', 'options' => [
                    ['text' => 'AA', 'correct' => false],
                    ['text' => 'aa', 'correct' => false],
                    ['text' => 'Aa', 'correct' => true],
                    ['text' => 'Mixed AA and aa', 'correct' => false],
                ]],
            ]],
            ['name' => 'Respiration', 'slug' => 'bio-respiration', 'sort_order' => 3, 'questions' => [
                ['text' => 'The word equation for aerobic respiration is:', 'difficulty' => 'easy', 'explanation' => 'Glucose + oxygen -> carbon dioxide + water (+ energy).', 'options' => [
                    ['text' => 'Glucose + oxygen -> carbon dioxide + water', 'correct' => true],
                    ['text' => 'Carbon dioxide + water -> glucose + oxygen', 'correct' => false],
                    ['text' => 'Glucose -> lactic acid', 'correct' => false],
                    ['text' => 'Glucose + nitrogen -> protein', 'correct' => false],
                ]],
                ['text' => 'Which type of respiration produces lactic acid in human muscle cells?', 'difficulty' => 'easy', 'explanation' => 'Anaerobic respiration in muscle produces lactic acid.', 'options' => [
                    ['text' => 'Aerobic', 'correct' => false],
                    ['text' => 'Anaerobic', 'correct' => true],
                    ['text' => 'Photo-respiration', 'correct' => false],
                    ['text' => 'Fermentation in yeast', 'correct' => false],
                ]],
            ]],
            ['name' => 'Photosynthesis', 'slug' => 'bio-photo', 'sort_order' => 4, 'questions' => [
                ['text' => 'Photosynthesis takes place mainly in the:', 'difficulty' => 'easy', 'explanation' => 'Chloroplasts contain chlorophyll and are the site of photosynthesis.', 'options' => [
                    ['text' => 'Mitochondria', 'correct' => false],
                    ['text' => 'Chloroplasts', 'correct' => true],
                    ['text' => 'Nucleus', 'correct' => false],
                    ['text' => 'Ribosomes', 'correct' => false],
                ]],
                ['text' => 'Which gas is taken in during photosynthesis?', 'difficulty' => 'easy', 'explanation' => 'Plants take in CO2 and release O2.', 'options' => [
                    ['text' => 'Oxygen', 'correct' => false],
                    ['text' => 'Nitrogen', 'correct' => false],
                    ['text' => 'Carbon dioxide', 'correct' => true],
                    ['text' => 'Hydrogen', 'correct' => false],
                ]],
            ]],
        ],

        // ===== English =====
        'english' => [
            ['name' => 'Grammar', 'slug' => 'eng-grammar', 'sort_order' => 1, 'questions' => [
                ['text' => 'Choose the correct sentence.', 'difficulty' => 'easy', 'explanation' => '"She doesn\'t like..." uses the correct third-person singular form.', 'options' => [
                    ['text' => 'She don\'t like apples.', 'correct' => false],
                    ['text' => 'She doesn\'t likes apples.', 'correct' => false],
                    ['text' => 'She doesn\'t like apples.', 'correct' => true],
                    ['text' => 'She not like apples.', 'correct' => false],
                ]],
                ['text' => 'Identify the past tense of "go".', 'difficulty' => 'easy', 'explanation' => 'The simple past of "go" is "went".', 'options' => [
                    ['text' => 'goed', 'correct' => false],
                    ['text' => 'gone', 'correct' => false],
                    ['text' => 'went', 'correct' => true],
                    ['text' => 'going', 'correct' => false],
                ]],
                ['text' => 'Which sentence uses the present continuous correctly?', 'difficulty' => 'easy', 'explanation' => '"They are playing football." is correct present continuous.', 'options' => [
                    ['text' => 'They are play football.', 'correct' => false],
                    ['text' => 'They are playing football.', 'correct' => true],
                    ['text' => 'They is playing football.', 'correct' => false],
                    ['text' => 'They playing football.', 'correct' => false],
                ]],
            ]],
            ['name' => 'Vocabulary', 'slug' => 'eng-vocab', 'sort_order' => 2, 'questions' => [
                ['text' => 'Choose the closest synonym for "happy".', 'difficulty' => 'easy', 'explanation' => '"Joyful" is closest in meaning to "happy".', 'options' => [
                    ['text' => 'Sad', 'correct' => false],
                    ['text' => 'Joyful', 'correct' => true],
                    ['text' => 'Angry', 'correct' => false],
                    ['text' => 'Tired', 'correct' => false],
                ]],
                ['text' => 'Choose the antonym of "ancient".', 'difficulty' => 'easy', 'explanation' => '"Modern" is the opposite of "ancient".', 'options' => [
                    ['text' => 'Old', 'correct' => false],
                    ['text' => 'Antique', 'correct' => false],
                    ['text' => 'Modern', 'correct' => true],
                    ['text' => 'Historic', 'correct' => false],
                ]],
            ]],
        ],

        // ===== Bahasa Melayu =====
        'bahasa-melayu' => [
            ['name' => 'Tatabahasa', 'slug' => 'bm-tatabahasa', 'sort_order' => 1, 'questions' => [
                ['text' => 'Apakah jenis perkataan "berlari"?', 'difficulty' => 'easy', 'explanation' => '"Berlari" ialah kata kerja yang menerangkan perbuatan.', 'options' => [
                    ['text' => 'Kata nama', 'correct' => false],
                    ['text' => 'Kata kerja', 'correct' => true],
                    ['text' => 'Kata adjektif', 'correct' => false],
                    ['text' => 'Kata hubung', 'correct' => false],
                ]],
                ['text' => 'Pilih ayat yang betul dari segi tatabahasa.', 'difficulty' => 'easy', 'explanation' => 'Ayat "Saya pergi ke sekolah setiap hari." adalah betul.', 'options' => [
                    ['text' => 'Saya pergi sekolah setiap hari.', 'correct' => false],
                    ['text' => 'Saya pergi ke sekolah setiap hari.', 'correct' => true],
                    ['text' => 'Saya ke sekolah pergi setiap hari.', 'correct' => false],
                    ['text' => 'Sekolah saya pergi setiap hari.', 'correct' => false],
                ]],
            ]],
            ['name' => 'Kosakata', 'slug' => 'bm-kosakata', 'sort_order' => 2, 'questions' => [
                ['text' => 'Apakah sinonim bagi perkataan "gembira"?', 'difficulty' => 'easy', 'explanation' => '"Riang" bermaksud sama seperti "gembira".', 'options' => [
                    ['text' => 'Sedih', 'correct' => false],
                    ['text' => 'Marah', 'correct' => false],
                    ['text' => 'Riang', 'correct' => true],
                    ['text' => 'Penat', 'correct' => false],
                ]],
                ['text' => 'Apakah antonim bagi "rajin"?', 'difficulty' => 'easy', 'explanation' => '"Malas" ialah lawan kata bagi "rajin".', 'options' => [
                    ['text' => 'Pintar', 'correct' => false],
                    ['text' => 'Malas', 'correct' => true],
                    ['text' => 'Tekun', 'correct' => false],
                    ['text' => 'Cepat', 'correct' => false],
                ]],
            ]],
        ],
    ];
}

/** Run the seeder; returns counts of inserted items. */
function seed_courses(): array
{
    $stats = ['topics' => 0, 'skills' => 0, 'questions' => 0, 'skipped' => 0];

    foreach (seed_courses_catalog() as $subjectSlug => $topics) {
        $subject = db_one('SELECT id FROM subjects WHERE slug = ?', [$subjectSlug]);
        if (!$subject) {
            continue; // subject not present in this install
        }
        $sid = (int) $subject['id'];

        foreach ($topics as $t) {
            // Topic: match by (subject_id, slug) OR (subject_id, name) so existing
            // seed-installed topics get reused even if their slug differs.
            $existing = db_one('SELECT id FROM topics WHERE subject_id = ? AND (slug = ? OR name = ?) LIMIT 1', [$sid, $t['slug'], $t['name']]);
            if ($existing) {
                $tid = (int) $existing['id'];
            } else {
                $tid = db_exec(
                    'INSERT INTO topics (subject_id, name, slug, sort_order) VALUES (?, ?, ?, ?)',
                    [$sid, $t['name'], $t['slug'], $t['sort_order'] ?? 0]
                );
                $stats['topics']++;
            }

            foreach ($t['skills'] ?? [] as $sk) {
                $skExisting = db_one('SELECT id FROM skills WHERE topic_id = ? AND name = ?', [$tid, $sk['name']]);
                if (!$skExisting) {
                    db_exec(
                        'INSERT INTO skills (topic_id, name, difficulty, description) VALUES (?, ?, ?, ?)',
                        [$tid, $sk['name'], $sk['difficulty'] ?? 'medium', $sk['description'] ?? null]
                    );
                    $stats['skills']++;
                }
            }

            foreach ($t['questions'] ?? [] as $q) {
                $qExisting = db_one('SELECT id FROM questions WHERE subject_id = ? AND question_text = ?', [$sid, $q['text']]);
                if ($qExisting) {
                    $stats['skipped']++;
                    continue;
                }
                $qid = db_exec(
                    'INSERT INTO questions (subject_id, topic_id, type, difficulty, question_text, explanation, marks, status)
                     VALUES (?,?,?,?,?,?,?,?)',
                    [$sid, $tid, 'mcq', $q['difficulty'] ?? 'medium', $q['text'], $q['explanation'] ?? null, $q['marks'] ?? 1, 'active']
                );
                foreach (array_values($q['options']) as $i => $opt) {
                    db_exec(
                        'INSERT INTO question_options (question_id, label, option_text, is_correct, sort_order)
                         VALUES (?,?,?,?,?)',
                        [$qid, chr(65 + $i), $opt['text'], !empty($opt['correct']) ? 1 : 0, $i + 1]
                    );
                }
                $stats['questions']++;
            }
        }
    }

    return $stats;
}

if (PHP_SAPI === 'cli') {
    $s = seed_courses();
    echo "Courses seeded — topics: {$s['topics']}, skills: {$s['skills']}, questions: {$s['questions']} (skipped existing: {$s['skipped']})\n";
}
