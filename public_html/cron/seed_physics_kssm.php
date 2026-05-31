<?php
/**
 * KSSM SPM Physics syllabus seeder. Form 4 + Form 5, 15 chapters with
 * subtopics + starter skills, plus the formula card catalog.
 *
 * Idempotent: topics matched by (subject_id, name, form_level). If a
 * legacy same-named topic exists with NULL form_level it is adopted and
 * its form_level filled in (so 'Electricity' / 'Forces and Motion' from
 * the original content.sql don't become orphan duplicates).
 *
 *   php public_html/cron/seed_physics_kssm.php
 */
declare(strict_types=1);

require_once __DIR__ . '/../inc/db.php';

function physics_kssm_catalog(): array
{
    return [
        // ============= FORM 4 =============
        ['form' => 4, 'name' => 'Introduction to Physics', 'subtopics' => [
            'Physical Quantities', 'Base Quantities', 'Derived Quantities', 'Scientific Notation', 'Scalars and Vectors',
        ], 'skills' => [
            ['Convert units', 'easy'],
            ['Identify physical quantities', 'easy'],
            ['Distinguish scalar and vector quantities', 'easy'],
        ]],
        ['form' => 4, 'name' => 'Force and Motion I', 'subtopics' => [
            'Distance and Displacement', 'Speed and Velocity', 'Acceleration', 'Motion Graphs', 'Free Fall Motion',
        ], 'skills' => [
            ['Calculate velocity', 'medium'],
            ['Interpret graphs', 'medium'],
            ['Solve acceleration problems', 'medium'],
        ]],
        ['form' => 4, 'name' => 'Force and Motion II', 'subtopics' => [
            'Momentum', 'Principle of Conservation of Momentum', 'Impulse', 'Impulsive Force',
        ], 'skills' => [
            ['Calculate momentum', 'medium'],
            ['Apply conservation laws', 'medium'],
            ['Solve collision problems', 'hard'],
        ]],
        ['form' => 4, 'name' => 'Heat', 'subtopics' => [
            'Thermal Equilibrium', 'Specific Heat Capacity', 'Specific Latent Heat', 'Heating and Cooling Curves',
        ], 'skills' => [
            ['Calculate heat energy', 'medium'],
            ['Solve SHC problems', 'medium'],
            ['Interpret heating curves', 'medium'],
        ]],
        ['form' => 4, 'name' => 'Waves', 'subtopics' => [
            'Characteristics of Waves', 'Wave Parameters', 'Reflection', 'Refraction', 'Diffraction', 'Interference',
        ], 'skills' => [
            ['Calculate wavelength', 'medium'],
            ['Analyze wave behavior', 'medium'],
            ['Apply wave equations', 'medium'],
        ]],
        ['form' => 4, 'name' => 'Light', 'subtopics' => [
            'Reflection', 'Refraction', 'Critical Angle', 'Total Internal Reflection',
        ], 'skills' => [
            ['Draw ray diagrams', 'medium'],
            ['Calculate refractive index', 'medium'],
            ['Explain optical phenomena', 'medium'],
        ]],
        ['form' => 4, 'name' => 'Electricity', 'subtopics' => [
            'Electric Current', 'Potential Difference', 'Resistance', "Ohm's Law",
        ], 'skills' => [
            ['Calculate current', 'medium'],
            ['Solve circuit equations', 'medium'],
            ["Apply Ohm's law", 'medium'],
        ]],
        ['form' => 4, 'name' => 'Electromagnetism', 'subtopics' => [
            'Magnetic Fields', 'Electromagnets', 'Force on Current-Carrying Conductors',
        ], 'skills' => [
            ['Identify magnetic effects', 'medium'],
            ['Explain electromagnetic applications', 'medium'],
        ]],

        // ============= FORM 5 =============
        ['form' => 5, 'name' => 'Force and Motion III', 'subtopics' => [
            "Newton's Laws", 'Inertia', 'Force Analysis',
        ], 'skills' => [
            ["Apply Newton's laws", 'medium'],
            ['Draw force diagrams', 'medium'],
            ['Solve motion problems', 'medium'],
        ]],
        ['form' => 5, 'name' => 'Pressure', 'subtopics' => [
            'Pressure', 'Liquid Pressure', 'Atmospheric Pressure', 'Pascal Principle', 'Archimedes Principle',
        ], 'skills' => [
            ['Calculate pressure', 'medium'],
            ['Apply buoyancy principles', 'medium'],
            ['Explain hydraulic systems', 'medium'],
        ]],
        ['form' => 5, 'name' => 'Electricity', 'subtopics' => [
            'Series Circuits', 'Parallel Circuits', 'Electrical Energy', 'Electrical Power', 'Domestic Electricity',
        ], 'skills' => [
            ['Analyze circuits', 'medium'],
            ['Calculate power', 'medium'],
            ['Determine electricity costs', 'medium'],
        ]],
        ['form' => 5, 'name' => 'Electronics', 'subtopics' => [
            'Diodes', 'Transistors', 'Logic Gates',
        ], 'skills' => [
            ['Read logic gate diagrams', 'medium'],
            ['Determine outputs', 'medium'],
            ['Explain electronic components', 'medium'],
        ]],
        ['form' => 5, 'name' => 'Electromagnetism', 'subtopics' => [
            'Electromagnetic Induction', 'Transformers', 'AC and DC Current',
        ], 'skills' => [
            ["Apply Faraday's Law", 'hard'],
            ['Solve transformer calculations', 'medium'],
            ['Compare AC and DC systems', 'easy'],
        ]],
        ['form' => 5, 'name' => 'Nuclear Physics', 'subtopics' => [
            'Radioactivity', 'Radioactive Decay', 'Half-Life', 'Nuclear Energy',
        ], 'skills' => [
            ['Calculate half-life', 'medium'],
            ['Explain radiation effects', 'medium'],
            ['Apply nuclear concepts', 'hard'],
        ]],
        ['form' => 5, 'name' => 'Quantum Physics', 'subtopics' => [
            'Photoelectric Effect', 'Wave-Particle Duality', 'Einstein Theory',
        ], 'skills' => [
            ['Explain quantum phenomena', 'hard'],
            ['Apply photon energy calculations', 'hard'],
        ]],
    ];
}

/** Formula cards. topic_match is matched against catalog topic name. */
function physics_kssm_formulas(): array
{
    return [
        ['Force and Motion I',  'Velocity (kinematics)',    'v = u + at',                'Constant acceleration kinematics — final velocity.'],
        ['Force and Motion I',  'Distance (kinematics)',    's = ut + ½at²',             'Distance with constant acceleration.'],
        ['Force and Motion I',  'Velocity-squared',         'v² = u² + 2as',             'Useful when time is not given.'],
        ['Force and Motion II', 'Momentum',                 'p = mv',                    'Linear momentum of a body.'],
        ['Force and Motion II', 'Impulse',                  'Ft = mv − mu',              'Impulse equals change in momentum.'],
        ['Force and Motion III', "Newton's second law",     'F = ma',                    'Resultant force equals mass × acceleration.'],
        ['Heat',                'Heat energy',              'Q = mcθ',                   'Energy to change temperature.'],
        ['Heat',                'Latent heat',              'Q = mL',                    'Energy to change phase at constant temperature.'],
        ['Waves',               'Wave speed',               'v = fλ',                    'Speed equals frequency × wavelength.'],
        ['Light',               'Snell / refractive index', 'n = sin i / sin r',         "Snell's law for refraction.'"],
        ['Electricity',         "Ohm's law",                'V = IR',                    'Potential difference equals current × resistance. (Form 4)'],
        ['Electricity',         'Electrical power',         'P = VI',                    'Power dissipated in a component. (Form 5)'],
        ['Electricity',         'Electrical energy',        'E = Pt',                    'Energy used over time. (Form 5)'],
        ['Electricity',         'Series resistance',        'R = R₁ + R₂ + R₃',          'Sum of resistors in series.'],
        ['Electricity',         'Parallel resistance',      '1/R = 1/R₁ + 1/R₂ + 1/R₃',  'Reciprocal sum for parallel resistors.'],
        ['Electromagnetism',    'Transformer ratio',        'Vp/Vs = Np/Ns',             'Ideal transformer turns ratio.'],
        ['Pressure',            'Pressure',                 'P = F/A',                    'Force per unit area.'],
        ['Pressure',            'Liquid pressure',          'P = ρgh',                    'Pressure at depth h in a fluid.'],
        ['Nuclear Physics',     'Half-life decay',          'N = N₀ (1/2)^(t/T)',         'Remaining nuclei after time t.'],
        ['Quantum Physics',     'Photon energy',            'E = hf',                     'Planck relation for photon energy.'],
    ];
}

function physics_kssm_run(): array
{
    $subject = db_one("SELECT id FROM subjects WHERE slug = 'physics' LIMIT 1");
    if (!$subject) {
        return ['status' => 'no_subject', 'message' => 'Physics subject not found. Run install / seed_subjects first.'];
    }
    $sid = (int) $subject['id'];

    $catalog = physics_kssm_catalog();
    $topicsCreated = $topicsKept = $topicsAdopted = 0;
    $subCreated = $subKept = 0;
    $skillsCreated = $skillsKept = 0;
    $formulasCreated = $formulasKept = 0;

    $topicIds = []; // [topic_name => first id seen]

    foreach ($catalog as $i => $t) {
        $name = $t['name'];
        $form = (int) $t['form'];
        $sortOrder = ($form === 4 ? 0 : 100) + $i;

        // Strict match: same name + same form_level.
        $existing = db_one(
            'SELECT id FROM topics WHERE subject_id = ? AND name = ? AND form_level <=> ? LIMIT 1',
            [$sid, $name, $form]
        );
        if ($existing) {
            $tid = (int) $existing['id'];
            $topicsKept++;
        } else {
            // Legacy match: same name but no form_level yet. Adopt it.
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
        // Remember the first occurrence (Form 4 preferred) for formula linking.
        if (!isset($topicIds[$name])) {
            $topicIds[$name] = $tid;
        }

        foreach ($t['subtopics'] as $idx => $subName) {
            $sub = db_one('SELECT id FROM subtopics WHERE topic_id = ? AND name = ? LIMIT 1', [$tid, $subName]);
            if ($sub) {
                $subKept++;
            } else {
                db_exec(
                    'INSERT INTO subtopics (topic_id, name, sort_order) VALUES (?, ?, ?)',
                    [$tid, $subName, $idx + 1]
                );
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

    // Formulas. Idempotent on (topic_id, formula_name).
    foreach (physics_kssm_formulas() as $idx => [$topicName, $fname, $formula, $desc]) {
        $tid = $topicIds[$topicName] ?? null;
        $existing = db_one(
            'SELECT id FROM physics_formulas WHERE topic_id <=> ? AND formula_name = ? LIMIT 1',
            [$tid, $fname]
        );
        if ($existing) {
            $formulasKept++;
        } else {
            db_exec(
                'INSERT INTO physics_formulas (topic_id, topic_label, formula_name, formula, description, sort_order)
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
    $r = physics_kssm_run();
    if (($r['status'] ?? '') !== 'ok') {
        fwrite(STDERR, ($r['message'] ?? 'unknown error') . "\n");
        exit(1);
    }
    echo "KSSM Physics seeded —\n";
    echo "  Topics:    created {$r['topics_created']}, adopted {$r['topics_adopted']}, kept {$r['topics_kept']} (catalog {$r['catalog_topics']})\n";
    echo "  Subtopics: created {$r['subtopics_created']}, kept {$r['subtopics_kept']}\n";
    echo "  Skills:    created {$r['skills_created']}, kept {$r['skills_kept']}\n";
    echo "  Formulas:  created {$r['formulas_created']}, kept {$r['formulas_kept']}\n";
}
