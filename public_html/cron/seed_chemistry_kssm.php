<?php
/**
 * KSSM SPM Chemistry syllabus seeder. Form 4 + Form 5, 16 chapters with
 * subtopics + starter skills, plus a Chemistry formula bank.
 *
 * Idempotent: topics matched by (subject_id, name, form_level). Legacy
 * same-named topics with NULL form_level are adopted.
 *
 *   php public_html/cron/seed_chemistry_kssm.php
 */
declare(strict_types=1);

require_once __DIR__ . '/../inc/db.php';

function chemistry_kssm_catalog(): array
{
    return [
        // ============= FORM 4 =============
        ['form' => 4, 'name' => 'Introduction to Chemistry', 'subtopics' => [
            'Development of Chemistry', 'Importance of Chemistry', 'Scientific Investigation', 'Laboratory Rules',
        ], 'skills' => [
            ['Understand scientific methods', 'easy'],
            ['Apply lab safety procedures', 'easy'],
            ['Conduct investigations', 'medium'],
        ]],
        ['form' => 4, 'name' => 'Matter and Atomic Structure', 'subtopics' => [
            'Matter', 'Atomic Structure', 'Subatomic Particles', 'Isotopes',
        ], 'skills' => [
            ['Identify particles', 'easy'],
            ['Calculate proton/neutron numbers', 'medium'],
            ['Explain isotopes', 'medium'],
        ]],
        ['form' => 4, 'name' => 'Formulae and Chemical Equations', 'subtopics' => [
            'Chemical Formulae', 'Empirical Formula', 'Molecular Formula', 'Chemical Equations',
        ], 'skills' => [
            ['Write equations', 'medium'],
            ['Balance equations', 'medium'],
            ['Determine formulas', 'medium'],
        ]],
        ['form' => 4, 'name' => 'Periodic Table', 'subtopics' => [
            'Group Classification', 'Periods', 'Metals', 'Non-Metals', 'Transition Elements',
        ], 'skills' => [
            ['Identify element groups', 'easy'],
            ['Explain periodic trends', 'medium'],
            ['Predict properties', 'medium'],
        ]],
        ['form' => 4, 'name' => 'Chemical Bonds', 'subtopics' => [
            'Ionic Bonds', 'Covalent Bonds', 'Metallic Bonds',
        ], 'skills' => [
            ['Draw bonding diagrams', 'medium'],
            ['Compare bond types', 'medium'],
            ['Explain properties', 'medium'],
        ]],
        ['form' => 4, 'name' => 'Acids, Bases and Salts', 'subtopics' => [
            'Acids', 'Alkalis', 'pH Scale', 'Neutralisation', 'Salt Preparation',
        ], 'skills' => [
            ['Identify acids and bases', 'easy'],
            ['Calculate pH', 'medium'],
            ['Write neutralisation equations', 'medium'],
        ]],
        ['form' => 4, 'name' => 'Rate of Reaction', 'subtopics' => [
            'Collision Theory', 'Factors Affecting Rate', 'Catalysts',
        ], 'skills' => [
            ['Interpret graphs', 'medium'],
            ['Explain reaction rates', 'medium'],
            ['Predict outcomes', 'medium'],
        ]],
        ['form' => 4, 'name' => 'Manufactured Substances', 'subtopics' => [
            'Glass', 'Ceramics', 'Composite Materials',
        ], 'skills' => [
            ['Compare materials', 'easy'],
            ['Explain applications', 'medium'],
        ]],

        // ============= FORM 5 =============
        ['form' => 5, 'name' => 'Redox Equilibrium', 'subtopics' => [
            'Oxidation', 'Reduction', 'Oxidising Agents', 'Reducing Agents',
        ], 'skills' => [
            ['Determine oxidation numbers', 'medium'],
            ['Identify redox reactions', 'medium'],
        ]],
        ['form' => 5, 'name' => 'Carbon Compounds', 'subtopics' => [
            'Hydrocarbons', 'Homologous Series', 'Alcohols', 'Carboxylic Acids', 'Esters',
        ], 'skills' => [
            ['Name compounds', 'medium'],
            ['Draw structures', 'medium'],
            ['Explain reactions', 'medium'],
        ]],
        ['form' => 5, 'name' => 'Thermochemistry', 'subtopics' => [
            'Endothermic Reactions', 'Exothermic Reactions', 'Heat of Reaction',
        ], 'skills' => [
            ['Interpret energy diagrams', 'medium'],
            ['Calculate heat changes', 'medium'],
        ]],
        ['form' => 5, 'name' => 'Polymers', 'subtopics' => [
            'Natural Polymers', 'Synthetic Polymers', 'Polymerisation',
        ], 'skills' => [
            ['Identify polymers', 'easy'],
            ['Explain uses', 'easy'],
        ]],
        ['form' => 5, 'name' => 'Consumer and Industrial Chemistry', 'subtopics' => [
            'Food Additives', 'Medicines', 'Cosmetics', 'Nanotechnology',
        ], 'skills' => [
            ['Explain industrial applications', 'medium'],
            ['Analyze chemical products', 'medium'],
        ]],
        ['form' => 5, 'name' => 'Electrochemistry', 'subtopics' => [
            'Electrolytes', 'Electrolysis', 'Electrochemical Cells',
        ], 'skills' => [
            ['Predict products', 'medium'],
            ['Draw cell diagrams', 'medium'],
            ['Explain electrolysis', 'medium'],
        ]],
        ['form' => 5, 'name' => 'Chemical Reactions', 'subtopics' => [
            'Precipitation Reactions', 'Acid-Metal Reactions', 'Displacement Reactions',
        ], 'skills' => [
            ['Write equations', 'medium'],
            ['Predict products', 'medium'],
        ]],
        ['form' => 5, 'name' => 'Chemicals for Consumer Use', 'subtopics' => [
            'Soap', 'Detergent', 'Food Preservatives', 'Medicines',
        ], 'skills' => [
            ['Compare products', 'easy'],
            ['Evaluate usage', 'medium'],
        ]],
    ];
}

/** Chemistry formula cards. topic_match against catalog topic name. */
function chemistry_kssm_formulas(): array
{
    return [
        ['Formulae and Chemical Equations', 'Moles from mass',          'n = m / Mr',                  'Number of moles from mass (m, grams) and relative molecular mass (Mr).'],
        ['Formulae and Chemical Equations', 'Mass from moles',          'm = n × Mr',                  'Inverse of n = m / Mr.'],
        ['Formulae and Chemical Equations', 'Avogadro\'s number',       'N = n × NA',                  'Particles = moles × 6.02 × 10²³.'],
        ['Formulae and Chemical Equations', 'Molar gas volume (STP)',   'V = n × 22.4 L',              'At standard temperature and pressure.'],
        ['Formulae and Chemical Equations', 'Molar gas volume (RT/RP)', 'V = n × 24 L',                'At room temperature and pressure (Malaysian SPM convention).'],
        ['Acids, Bases and Salts',          'Concentration (molarity)', 'C = n / V',                   'Moles per dm³ (volume in dm³).'],
        ['Acids, Bases and Salts',          'Dilution',                 'M₁V₁ = M₂V₂',                 'Conservation of moles when diluting.'],
        ['Acids, Bases and Salts',          'pH',                       'pH = -log[H⁺]',               'pH from hydrogen ion concentration.'],
        ['Acids, Bases and Salts',          'Neutralisation ratio',     'MaVa / MbVb = a / b',         'For an acid–base titration with stoichiometric ratio a:b.'],
        ['Formulae and Chemical Equations', 'Density',                  'ρ = m / V',                   'Mass per unit volume.'],
        ['Formulae and Chemical Equations', 'Percentage yield',         'Actual / Theoretical × 100%', 'Yield efficiency of a reaction.'],
        ['Formulae and Chemical Equations', 'Percentage purity',        '(Pure mass / Total mass) × 100%', 'Purity of a sample by mass.'],
        ['Thermochemistry',                 'Heat of reaction',         'ΔH = -mcΔT',                  'Per mole of reactant (use Q = mcΔT to find Q, then divide by moles).'],
        ['Thermochemistry',                 'Heat energy',              'Q = mcΔT',                    'Heat absorbed/released by the surroundings.'],
        ['Rate of Reaction',                'Average rate',             'Rate = ΔAmount / ΔTime',      'Average rate of reaction across an interval.'],
        ['Matter and Atomic Structure',     'Relative atomic mass',     'Ar = Σ(isotope mass × %) / 100', 'Weighted average of isotope masses.'],
        ['Electrochemistry',                'Charge passed',            'Q = It',                      'Charge in coulombs from current and time.'],
        ['Electrochemistry',                'Moles of electrons',       'n_e = Q / F',                 'F is Faraday\'s constant (96 500 C/mol).'],
    ];
}

function chemistry_kssm_run(): array
{
    $subject = db_one("SELECT id FROM subjects WHERE slug = 'chemistry' LIMIT 1");
    if (!$subject) {
        return ['status' => 'no_subject', 'message' => 'Chemistry subject not found. Run install / seed_subjects first.'];
    }
    $sid = (int) $subject['id'];

    $catalog = chemistry_kssm_catalog();
    $topicsCreated = $topicsKept = $topicsAdopted = 0;
    $subCreated = $subKept = 0;
    $skillsCreated = $skillsKept = 0;
    $formulasCreated = $formulasKept = 0;

    $topicIds = []; // [name => id] (first occurrence)

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

    foreach (chemistry_kssm_formulas() as $idx => [$topicName, $fname, $formula, $desc]) {
        $tid = $topicIds[$topicName] ?? null;
        $existing = db_one(
            'SELECT id FROM chemistry_formulas WHERE topic_id <=> ? AND formula_name = ? LIMIT 1',
            [$tid, $fname]
        );
        if ($existing) {
            $formulasKept++;
        } else {
            db_exec(
                'INSERT INTO chemistry_formulas (topic_id, topic_label, formula_name, formula, description, sort_order)
                 VALUES (?, ?, ?, ?, ?, ?)',
                [$tid, $topicName, $fname, $formula, $desc, $idx + 1]
            );
            $formulasCreated++;
        }
    }

    return [
        'status'            => 'ok',
        'topics_created'    => $topicsCreated,
        'topics_kept'       => $topicsKept,
        'topics_adopted'    => $topicsAdopted,
        'subtopics_created' => $subCreated,
        'subtopics_kept'    => $subKept,
        'skills_created'    => $skillsCreated,
        'skills_kept'       => $skillsKept,
        'formulas_created'  => $formulasCreated,
        'formulas_kept'     => $formulasKept,
        'catalog_topics'    => count($catalog),
    ];
}

if (PHP_SAPI === 'cli') {
    $r = chemistry_kssm_run();
    if (($r['status'] ?? '') !== 'ok') {
        fwrite(STDERR, ($r['message'] ?? 'unknown error') . "\n");
        exit(1);
    }
    echo "KSSM Chemistry seeded —\n";
    echo "  Topics:    created {$r['topics_created']}, adopted {$r['topics_adopted']}, kept {$r['topics_kept']} (catalog {$r['catalog_topics']})\n";
    echo "  Subtopics: created {$r['subtopics_created']}, kept {$r['subtopics_kept']}\n";
    echo "  Skills:    created {$r['skills_created']}, kept {$r['skills_kept']}\n";
    echo "  Formulas:  created {$r['formulas_created']}, kept {$r['formulas_kept']}\n";
}
