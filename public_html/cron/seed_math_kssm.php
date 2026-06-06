<?php
/**
 * KSSM SPM Mathematics syllabus seeder. Seeds the full Form 4 + Form 5
 * topic/subtopic tree and a few starter skills. Idempotent — topics,
 * subtopics and skills already present (by name within their parent) are
 * kept and reused.
 *
 *   php public_html/cron/seed_math_kssm.php
 */
declare(strict_types=1);

require_once __DIR__ . '/../inc/db.php';

/** Full Form 4 + Form 5 syllabus. Numbered = chapter order. */
function math_kssm_catalog(): array
{
    return [
        // ============= FORM 4 =============
        ['form' => 4, 'name' => 'Functions and Quadratic Equations in One Variable', 'subtopics' => [
            'Quadratic Functions',
            'Quadratic Equations',
            'Roots of Quadratic Equations',
            'Graph of Quadratic Functions',
            'Solving Quadratic Problems',
        ]],
        ['form' => 4, 'name' => 'Number Bases', 'subtopics' => [
            'Base Numbers',
            'Conversion Between Bases',
            'Arithmetic Operations in Different Bases',
        ]],
        ['form' => 4, 'name' => 'Logical Reasoning', 'subtopics' => [
            'Statements',
            'Truth Values',
            'Logical Connectives',
            'Implications',
            'Arguments',
        ]],
        ['form' => 4, 'name' => 'Set Operations', 'subtopics' => [
            'Universal Set',
            'Complement',
            'Intersection',
            'Union',
            'Combined Operations',
            'Venn Diagrams',
        ]],
        ['form' => 4, 'name' => 'Network in Graph Theory', 'subtopics' => [
            'Graphs',
            'Networks',
            'Euler Paths',
            'Hamiltonian Circuits',
            'Shortest Path Problems',
        ]],
        ['form' => 4, 'name' => 'Linear Inequalities in Two Variables', 'subtopics' => [
            'Linear Inequalities',
            'Graphing Inequalities',
            'Feasible Regions',
            'Optimization Problems',
        ]],
        ['form' => 4, 'name' => 'Motion Graphs', 'subtopics' => [
            'Distance-Time Graph',
            'Speed-Time Graph',
            'Acceleration Concepts',
            'Interpretation of Motion Graphs',
        ]],
        ['form' => 4, 'name' => 'Measures of Dispersion of Ungrouped Data', 'subtopics' => [
            'Range',
            'Interquartile Range',
            'Variance',
            'Standard Deviation',
        ]],
        ['form' => 4, 'name' => 'Probability of Combined Events', 'subtopics' => [
            'Combined Events',
            'Tree Diagrams',
            'Mutually Exclusive Events',
            'Independent Events',
        ]],
        ['form' => 4, 'name' => 'Consumer Mathematics: Financial Management', 'subtopics' => [
            'Budgeting',
            'Savings',
            'Investments',
            'Loans',
            'Hire Purchase',
            'Credit Cards',
            'Insurance',
            'Financial Planning',
        ]],

        // ============= FORM 5 =============
        ['form' => 5, 'name' => 'Circles', 'subtopics' => [
            'Tangents',
            'Chords',
            'Angles in Circles',
            'Circle Theorems',
        ]],
        ['form' => 5, 'name' => 'Differentiation of Algebraic Functions', 'subtopics' => [
            'Gradient',
            'First Derivative',
            'Stationary Points',
            'Maximum and Minimum Values',
            'Applications of Differentiation',
        ]],
        ['form' => 5, 'name' => 'Consumer Mathematics: Insurance', 'subtopics' => [
            'Insurance Concepts',
            'Risk Management',
            'Insurance Policies',
            'Claims',
        ]],
        ['form' => 5, 'name' => 'Consumer Mathematics: Taxation', 'subtopics' => [
            'Income Tax',
            'Tax Relief',
            'Tax Calculation',
            'Personal Tax',
        ]],
        ['form' => 5, 'name' => 'Trigonometric Functions', 'subtopics' => [
            'Sine',
            'Cosine',
            'Tangent',
            'Trigonometric Graphs',
            'Trigonometric Identities',
        ]],
        ['form' => 5, 'name' => 'Angles and Lines in 3D Space', 'subtopics' => [
            'Lines in 3D',
            'Planes',
            'Angles Between Lines',
            'Angles Between Planes',
        ]],
        ['form' => 5, 'name' => 'Plans and Elevations', 'subtopics' => [
            'Orthogonal Projection',
            'Plan View',
            'Elevation View',
            'Scale Drawings',
        ]],
        ['form' => 5, 'name' => 'Vectors', 'subtopics' => [
            'Vector Representation',
            'Magnitude',
            'Vector Addition',
            'Vector Subtraction',
            'Problem Solving with Vectors',
        ]],
        ['form' => 5, 'name' => 'Probability Distribution', 'subtopics' => [
            'Random Variables',
            'Probability Distribution',
            'Expected Value',
            'Mean',
            'Variance',
        ]],
        ['form' => 5, 'name' => 'Mathematics of Finance', 'subtopics' => [
            'Simple Interest',
            'Compound Interest',
            'Annuities',
            'Investments',
            'Depreciation',
        ]],
    ];
}

/** A few starter skills so the structure is visible right after seeding. */
function math_kssm_starter_skills(): array
{
    return [
        // [topic name, subtopic name, skill, difficulty]
        ['Functions and Quadratic Equations in One Variable', 'Quadratic Functions', 'Identify quadratic functions', 'easy'],
        ['Functions and Quadratic Equations in One Variable', 'Quadratic Functions', 'Sketch a quadratic graph', 'medium'],
        ['Functions and Quadratic Equations in One Variable', 'Quadratic Equations', 'Solve quadratic equations by factorisation', 'medium'],
        ['Functions and Quadratic Equations in One Variable', 'Quadratic Equations', 'Apply the quadratic formula', 'medium'],
        ['Differentiation of Algebraic Functions', 'First Derivative', 'Differentiate polynomials using the power rule', 'medium'],
        ['Differentiation of Algebraic Functions', 'Stationary Points', 'Find stationary points and classify them', 'hard'],
    ];
}

function math_kssm_run(): array
{
    $subject = db_one("SELECT id FROM subjects WHERE slug = 'mathematics' LIMIT 1");
    if (!$subject) {
        return ['status' => 'no_subject', 'message' => 'Mathematics subject not found. Run install / seed_subjects first.'];
    }
    $sid = (int) $subject['id'];

    $catalog       = math_kssm_catalog();
    $topicsCreated = 0;
    $topicsKept    = 0;
    $subCreated    = 0;
    $subKept       = 0;
    $skillsCreated = 0;
    $skillsKept    = 0;

    // Track topic + subtopic ids so we can link starter skills afterwards.
    $topicIds    = [];   // [name => id]
    $subtopicIds = [];   // [topic_name|sub_name => id]

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
        $topicIds[$name] = $tid;

        foreach ($t['subtopics'] as $idx => $subName) {
            $sub = db_one('SELECT id FROM subtopics WHERE topic_id = ? AND name = ? LIMIT 1', [$tid, $subName]);
            if ($sub) {
                $subId = (int) $sub['id'];
                $subKept++;
            } else {
                $subId = db_exec(
                    'INSERT INTO subtopics (topic_id, name, sort_order) VALUES (?, ?, ?)',
                    [$tid, $subName, $idx + 1]
                );
                $subCreated++;
            }
            $subtopicIds[$name . '|' . $subName] = $subId;
        }
    }

    foreach (math_kssm_starter_skills() as $row) {
        [$topicName, $subName, $skillName, $diff] = $row;
        $tid = $topicIds[$topicName] ?? null;
        $sub = $subtopicIds[$topicName . '|' . $subName] ?? null;
        if (!$tid) {
            continue;
        }
        $existing = db_one('SELECT id FROM skills WHERE topic_id = ? AND name = ? LIMIT 1', [$tid, $skillName]);
        if ($existing) {
            if ($sub) {
                db_exec('UPDATE skills SET subtopic_id = ? WHERE id = ?', [$sub, (int) $existing['id']]);
            }
            $skillsKept++;
        } else {
            db_exec(
                'INSERT INTO skills (topic_id, subtopic_id, name, difficulty) VALUES (?, ?, ?, ?)',
                [$tid, $sub, $skillName, $diff]
            );
            $skillsCreated++;
        }
    }

    return [
        'status'           => 'ok',
        'topics_created'   => $topicsCreated,
        'topics_kept'      => $topicsKept,
        'subtopics_created'=> $subCreated,
        'subtopics_kept'   => $subKept,
        'skills_created'   => $skillsCreated,
        'skills_kept'      => $skillsKept,
        'catalog_topics'   => count($catalog),
    ];
}

if (PHP_SAPI === 'cli') {
    $r = math_kssm_run();
    if (($r['status'] ?? '') !== 'ok') {
        fwrite(STDERR, ($r['message'] ?? 'unknown error') . "\n");
        exit(1);
    }
    echo "KSSM Mathematics seeded —\n";
    echo "  Topics:    created {$r['topics_created']}, kept {$r['topics_kept']} (catalog {$r['catalog_topics']})\n";
    echo "  Subtopics: created {$r['subtopics_created']}, kept {$r['subtopics_kept']}\n";
    echo "  Skills:    created {$r['skills_created']}, kept {$r['skills_kept']}\n";
}
