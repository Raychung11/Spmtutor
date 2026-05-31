<?php
/**
 * KSSM SPM Additional Mathematics syllabus seeder. Form 4 + Form 5,
 * 20 chapters with subtopics and the starter skills given by the
 * syllabus brief. Idempotent — matches topics/subtopics/skills by name
 * within their parent, so reruns refresh form_level only.
 *
 *   php public_html/cron/seed_addmath_kssm.php
 */
declare(strict_types=1);

require_once __DIR__ . '/../inc/db.php';

function addmath_kssm_catalog(): array
{
    return [
        // ============= FORM 4 =============
        ['form' => 4, 'name' => 'Functions', 'subtopics' => [
            'Relations', 'Functions', 'Composite Functions', 'Inverse Functions',
        ], 'skills' => [
            ['Identify function notation', 'easy'],
            ['Find composite functions',   'medium'],
            ['Find inverse functions',     'medium'],
            ['Solve function problems',    'medium'],
        ]],
        ['form' => 4, 'name' => 'Quadratic Functions', 'subtopics' => [
            'Quadratic Graphs', 'Maximum and Minimum Points', 'Quadratic Models',
        ], 'skills' => [
            ['Sketch graphs',                  'easy'],
            ['Identify turning points',        'medium'],
            ['Solve quadratic applications',   'medium'],
        ]],
        ['form' => 4, 'name' => 'Systems of Equations', 'subtopics' => [
            'Simultaneous Equations', 'Linear and Non-linear Systems',
        ], 'skills' => [
            ['Solve systems algebraically', 'medium'],
            ['Solve graphically',           'medium'],
            ['Model real-life situations',  'medium'],
        ]],
        ['form' => 4, 'name' => 'Indices, Surds and Logarithms', 'subtopics' => [
            'Laws of Indices', 'Surds', 'Rationalisation', 'Logarithms', 'Logarithmic Laws',
        ], 'skills' => [
            ['Simplify surds',               'medium'],
            ['Apply logarithm laws',         'medium'],
            ['Solve logarithmic equations',  'hard'],
        ]],
        ['form' => 4, 'name' => 'Progressions', 'subtopics' => [
            'Arithmetic Progression (AP)', 'Geometric Progression (GP)', 'Sum of AP', 'Sum of GP',
        ], 'skills' => [
            ['Find nth term',                  'medium'],
            ['Calculate sums',                 'medium'],
            ['Solve progression applications', 'medium'],
        ]],
        ['form' => 4, 'name' => 'Linear Law', 'subtopics' => [
            'Linear Transformation', 'Linear Relationships', 'Graph Interpretation',
        ], 'skills' => [
            ['Convert equations to linear form', 'medium'],
            ['Determine gradient',               'medium'],
            ['Interpret constants',              'medium'],
        ]],
        ['form' => 4, 'name' => 'Coordinate Geometry', 'subtopics' => [
            'Distance Formula', 'Midpoint Formula', 'Equation of Line', 'Parallel Lines', 'Perpendicular Lines',
        ], 'skills' => [
            ['Find equations of lines',     'medium'],
            ['Solve coordinate problems',   'medium'],
            ['Apply geometric concepts',    'medium'],
        ]],
        ['form' => 4, 'name' => 'Circular Measure', 'subtopics' => [
            'Radians', 'Arc Length', 'Sector Area',
        ], 'skills' => [
            ['Convert degree ↔ radian', 'easy'],
            ['Calculate arc length',    'medium'],
            ['Calculate sector area',   'medium'],
        ]],
        ['form' => 4, 'name' => 'Differentiation', 'subtopics' => [
            'First Principles', 'Differentiation Rules', 'Tangent and Gradient',
        ], 'skills' => [
            ['Differentiate functions',     'medium'],
            ['Find gradients',              'medium'],
            ['Solve basic applications',    'medium'],
        ]],
        ['form' => 4, 'name' => 'Probability Distribution', 'subtopics' => [
            'Discrete Random Variables', 'Probability Distribution Tables',
        ], 'skills' => [
            ['Calculate expected values', 'medium'],
            ['Find probabilities',        'medium'],
            ['Interpret distributions',   'medium'],
        ]],

        // ============= FORM 5 =============
        ['form' => 5, 'name' => 'Circular Functions', 'subtopics' => [
            'Sine Function', 'Cosine Function', 'Tangent Function', 'Graphs',
        ], 'skills' => [
            ['Sketch trig graphs',  'medium'],
            ['Identify amplitudes', 'medium'],
            ['Solve trig equations','hard'],
        ]],
        ['form' => 5, 'name' => 'Differentiation Applications', 'subtopics' => [
            'Stationary Points', 'Maximum and Minimum Values', 'Rate of Change',
        ], 'skills' => [
            ['Optimization',  'hard'],
            ['Related rates', 'hard'],
            ['Curve analysis','hard'],
        ]],
        ['form' => 5, 'name' => 'Integration', 'subtopics' => [
            'Indefinite Integrals', 'Definite Integrals', 'Area Under Curve',
        ], 'skills' => [
            ['Integrate functions',         'medium'],
            ['Find areas',                  'medium'],
            ['Apply integration formulas',  'medium'],
        ]],
        ['form' => 5, 'name' => 'Permutations and Combinations', 'subtopics' => [
            'Factorials', 'Permutations', 'Combinations',
        ], 'skills' => [
            ['Count arrangements',     'medium'],
            ['Solve selection problems','medium'],
        ]],
        ['form' => 5, 'name' => 'Binomial Expansion', 'subtopics' => [
            'Binomial Theorem', 'Expansion', 'General Term',
        ], 'skills' => [
            ['Expand expressions',     'medium'],
            ['Find coefficients',      'medium'],
            ['Determine specific terms','hard'],
        ]],
        ['form' => 5, 'name' => 'Trigonometric Functions', 'subtopics' => [
            'Identities', 'Equations', 'Double Angle Formula',
        ], 'skills' => [
            ['Prove identities',       'hard'],
            ['Solve trig equations',   'hard'],
        ]],
        ['form' => 5, 'name' => 'Projection of Vectors', 'subtopics' => [
            'Scalar Product', 'Vector Projection',
        ], 'skills' => [
            ['Find vector projections',   'medium'],
            ['Solve geometry problems',   'medium'],
        ]],
        ['form' => 5, 'name' => 'Vectors', 'subtopics' => [
            'Vector Algebra', 'Magnitude', 'Direction',
        ], 'skills' => [
            ['Perform vector operations', 'medium'],
            ['Solve vector applications', 'medium'],
        ]],
        ['form' => 5, 'name' => 'Solution of Triangles', 'subtopics' => [
            'Sine Rule', 'Cosine Rule', 'Area of Triangle',
        ], 'skills' => [
            ['Solve non-right triangles', 'medium'],
            ['Calculate areas',           'medium'],
        ]],
        ['form' => 5, 'name' => 'Mathematical Modelling', 'subtopics' => [
            'Real-life Models', 'Optimization Models', 'Interpretation',
        ], 'skills' => [
            ['Build models',     'hard'],
            ['Analyze results',  'hard'],
            ['Make predictions', 'hard'],
        ]],
    ];
}

function addmath_kssm_run(): array
{
    $subject = db_one("SELECT id FROM subjects WHERE slug = 'add-maths' LIMIT 1");
    if (!$subject) {
        return ['status' => 'no_subject', 'message' => 'Additional Mathematics subject not found. Run install / seed_subjects first.'];
    }
    $sid = (int) $subject['id'];

    $catalog = addmath_kssm_catalog();
    $topicsCreated = $topicsKept = 0;
    $subCreated = $subKept = 0;
    $skillsCreated = $skillsKept = 0;

    foreach ($catalog as $i => $t) {
        $name = $t['name'];
        $form = (int) $t['form'];
        $sortOrder = ($form === 4 ? 0 : 100) + $i;

        $existing = db_one('SELECT id FROM topics WHERE subject_id = ? AND name = ? LIMIT 1', [$sid, $name]);
        if ($existing) {
            $tid = (int) $existing['id'];
            db_exec('UPDATE topics SET form_level = ? WHERE id = ?', [$form, $tid]);
            $topicsKept++;
        } else {
            $slug = preg_replace('/[^a-z0-9]+/', '-', strtolower($name));
            $tid  = db_exec(
                'INSERT INTO topics (subject_id, form_level, name, slug, sort_order) VALUES (?, ?, ?, ?, ?)',
                [$sid, $form, $name, $slug, $sortOrder]
            );
            $topicsCreated++;
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
                continue;
            }
            db_exec(
                'INSERT INTO skills (topic_id, name, difficulty) VALUES (?, ?, ?)',
                [$tid, $skillName, $diff]
            );
            $skillsCreated++;
        }
    }

    return [
        'status'            => 'ok',
        'topics_created'    => $topicsCreated,
        'topics_kept'       => $topicsKept,
        'subtopics_created' => $subCreated,
        'subtopics_kept'    => $subKept,
        'skills_created'    => $skillsCreated,
        'skills_kept'       => $skillsKept,
        'catalog_topics'    => count($catalog),
    ];
}

if (PHP_SAPI === 'cli') {
    $r = addmath_kssm_run();
    if (($r['status'] ?? '') !== 'ok') {
        fwrite(STDERR, ($r['message'] ?? 'unknown error') . "\n");
        exit(1);
    }
    echo "KSSM Additional Mathematics seeded —\n";
    echo "  Topics:    created {$r['topics_created']}, kept {$r['topics_kept']} (catalog {$r['catalog_topics']})\n";
    echo "  Subtopics: created {$r['subtopics_created']}, kept {$r['subtopics_kept']}\n";
    echo "  Skills:    created {$r['skills_created']}, kept {$r['skills_kept']}\n";
}
