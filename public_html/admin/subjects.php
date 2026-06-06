<?php
declare(strict_types=1);
require_once __DIR__ . '/../inc/auth.php';
require_once __DIR__ . '/../inc/ai_questions.php';
require_once __DIR__ . '/../inc/admin_layout.php';

$admin  = require_role('admin');
$levels = db_all('SELECT id, name FROM education_levels ORDER BY sort_order');
$hasAiCols = subject_ai_columns_present();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $action = input('action');

    if ($action === 'reset_prompt' && $hasAiCols) {
        $id = input_int('id');
        $row = db_one('SELECT * FROM subjects WHERE id = ?', [$id]);
        if ($row) {
            db_exec('UPDATE subjects SET ai_prompt = ? WHERE id = ?', [subject_ai_default($row), $id]);
            flash('success', 'AI prompt reset to the composed default.');
        }
        redirect('admin/subjects.php?edit=' . $id);
    }

    if ($action === 'create' || $action === 'update') {
        $name        = input('name');
        $level       = input_int('education_level_id') ?: null;
        $desc        = input('description');
        $aiPrompt    = input('ai_prompt');
        $aiType      = in_array(input('ai_subject_type'), array_keys(subject_ai_types()), true) ? input('ai_subject_type') : null;
        $aiBoard     = input('ai_exam_board');
        $aiLanguage  = input('ai_language');
        $aiNotes     = input('ai_notes');
        $slug        = preg_replace('/[^a-z0-9]+/', '-', strtolower($name));
        if ($name === '') {
            flash('error', 'Name is required.');
        } elseif ($action === 'create') {
            if ($hasAiCols) {
                db_exec(
                    'INSERT INTO subjects (education_level_id, name, slug, description, ai_prompt, ai_subject_type, ai_exam_board, ai_language, ai_notes)
                     VALUES (?,?,?,?,?,?,?,?,?)',
                    [$level, $name, $slug, $desc, $aiPrompt ?: null, $aiType, $aiBoard ?: null, $aiLanguage ?: null, $aiNotes ?: null]
                );
            } else {
                db_exec(
                    'INSERT INTO subjects (education_level_id, name, slug, description) VALUES (?,?,?,?)',
                    [$level, $name, $slug, $desc]
                );
            }
            flash('success', 'Subject created.');
        } else {
            if ($hasAiCols) {
                db_exec(
                    'UPDATE subjects SET education_level_id = ?, name = ?, description = ?, ai_prompt = ?, ai_subject_type = ?, ai_exam_board = ?, ai_language = ?, ai_notes = ? WHERE id = ?',
                    [$level, $name, $desc, $aiPrompt ?: null, $aiType, $aiBoard ?: null, $aiLanguage ?: null, $aiNotes ?: null, input_int('id')]
                );
            } else {
                db_exec(
                    'UPDATE subjects SET education_level_id = ?, name = ?, description = ? WHERE id = ?',
                    [$level, $name, $desc, input_int('id')]
                );
            }
            flash('success', 'Subject updated.');
        }
    } elseif ($action === 'delete') {
        db_exec('DELETE FROM subjects WHERE id = ?', [input_int('id')]);
        flash('success', 'Subject deleted.');
    }
    redirect('admin/subjects.php');
}

$editId = input_int('edit');
$edit   = $editId ? db_one('SELECT * FROM subjects WHERE id = ?', [$editId]) : null;

// --- Filters + pagination ---
$q         = trim(input('q'));
$levelId   = input_int('level');
$aiType    = input('ai_type');
$aiType    = in_array($aiType, array_keys(subject_ai_types()), true) ? $aiType : '';
$promptFilter = input('prompt_filter');  // 'custom' | 'default' | ''
$promptFilter = in_array($promptFilter, ['custom', 'default'], true) ? $promptFilter : '';
$page      = max(1, input_int('page', 1));
$perPage   = max(20, min(200, input_int('per_page', 50)));
$offset    = ($page - 1) * $perPage;

$where  = [];
$params = [];
if ($q !== '') {
    $where[]  = '(s.name LIKE ? OR s.slug LIKE ?)';
    $params[] = '%' . $q . '%';
    $params[] = '%' . $q . '%';
}
if ($levelId > 0) {
    $where[]  = 's.education_level_id = ?';
    $params[] = $levelId;
}
if ($hasAiCols && $aiType !== '') {
    $where[]  = 's.ai_subject_type = ?';
    $params[] = $aiType;
}
if ($hasAiCols && $promptFilter === 'custom') {
    $where[] = "(s.ai_prompt IS NOT NULL AND s.ai_prompt <> '')";
} elseif ($hasAiCols && $promptFilter === 'default') {
    $where[] = "(s.ai_prompt IS NULL OR s.ai_prompt = '')";
}
$whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$total = (int) (db_one("SELECT COUNT(*) c FROM subjects s $whereSql", $params)['c'] ?? 0);
$pages = max(1, (int) ceil($total / $perPage));
if ($page > $pages) { $page = $pages; $offset = ($page - 1) * $perPage; }

// Subject rows + per-subject stats in one query.
$rows = db_all(
    "SELECT s.*, l.name AS level,
            (SELECT COUNT(*) FROM topics t WHERE t.subject_id = s.id) AS topic_count,
            (SELECT COUNT(*) FROM topics t LEFT JOIN skills sk ON sk.topic_id = t.id WHERE t.subject_id = s.id AND sk.id IS NOT NULL) AS skill_count,
            (SELECT COUNT(*) FROM questions q WHERE q.subject_id = s.id) AS question_count,
            (SELECT COUNT(*) FROM questions q WHERE q.subject_id = s.id AND q.status = 'pending') AS pending_count
     FROM subjects s LEFT JOIN education_levels l ON l.id = s.education_level_id
     $whereSql
     ORDER BY s.sort_order, s.id
     LIMIT $perPage OFFSET $offset",
    $params
);

// Per-AI-type chip counts (only meaningful when phase9 cols exist).
$aiTypeCounts = [];
if ($hasAiCols) {
    foreach (db_all("SELECT ai_subject_type, COUNT(*) c FROM subjects WHERE ai_subject_type IS NOT NULL AND ai_subject_type <> '' GROUP BY ai_subject_type") as $r) {
        $aiTypeCounts[(string) $r['ai_subject_type']] = (int) $r['c'];
    }
}

// Level pill counts (respect other filters).
$lcWhere  = [];
$lcParams = [];
if ($q !== '') {
    $lcWhere[]  = '(s.name LIKE ? OR s.slug LIKE ?)';
    $lcParams[] = '%' . $q . '%';
    $lcParams[] = '%' . $q . '%';
}
if ($hasAiCols && $aiType !== '') {
    $lcWhere[]  = 's.ai_subject_type = ?';
    $lcParams[] = $aiType;
}
$lcWhereSql = $lcWhere ? ' AND ' . implode(' AND ', $lcWhere) : '';
$levelCounts = [];
$levelCountsAll = 0;
foreach (db_all("SELECT s.education_level_id, COUNT(*) c FROM subjects s WHERE 1=1 $lcWhereSql GROUP BY s.education_level_id", $lcParams) as $r) {
    $levelCounts[(int) ($r['education_level_id'] ?? 0)] = (int) $r['c'];
    $levelCountsAll += (int) $r['c'];
}

/** Build a filter URL preserving current params. */
function subjects_link(array $overrides = []): string
{
    global $q, $levelId, $aiType, $promptFilter, $page, $perPage;
    $args = array_merge([
        'q' => $q, 'level' => $levelId, 'ai_type' => $aiType,
        'prompt_filter' => $promptFilter, 'page' => $page, 'per_page' => $perPage,
    ], $overrides);
    $defaults = ['q' => '', 'level' => 0, 'ai_type' => '', 'prompt_filter' => '', 'page' => 1, 'per_page' => 50];
    foreach ($defaults as $k => $v) {
        if (($args[$k] ?? null) === $v) {
            unset($args[$k]);
        }
    }
    return url('admin/subjects.php' . ($args ? '?' . http_build_query($args) : ''));
}

admin_layout_start('Subjects', $admin, 'subjects.php');
?>
<?php if ($edit): ?>
<div class="card" style="margin-bottom:18px">
  <h3 style="margin-top:0">Edit subject</h3>
  <?php render_subject_form($edit, $levels, $hasAiCols); ?>
</div>
<?php else: ?>
<details class="card" style="margin-bottom:18px">
  <summary style="cursor:pointer;font-weight:600;font-size:15px">+ Add new subject</summary>
  <div style="margin-top:14px">
    <?php render_subject_form(null, $levels, $hasAiCols); ?>
  </div>
</details>
<?php endif; ?>

<?php if (!$hasAiCols): ?>
  <div class="flash flash--info">
    <strong>Database migrations are out of date.</strong> The per-subject AI columns are missing —
    AI type filter chips and the AI prompt section are disabled. Run them from
    <a href="<?= url('admin/seeders.php') ?>">Admin → Seeders → Run database migrations</a>.
  </div>
<?php endif; ?>

<?php if ($hasAiCols && $aiTypeCounts): ?>
<div class="card" style="margin-bottom:18px">
  <h4 style="margin:0 0 10px;font-size:13px;text-transform:uppercase;letter-spacing:.08em;color:var(--muted)">Jump by AI subject type</h4>
  <div style="display:flex;flex-wrap:wrap;gap:8px">
    <a class="chip" href="<?= e(subjects_link(['ai_type' => '', 'page' => 1])) ?>"
       style="<?= $aiType === '' ? 'border-color:var(--primary);color:var(--primary)' : '' ?>">
      All <span class="muted" style="margin-left:4px">· <?= (int) array_sum($aiTypeCounts) ?></span>
    </a>
    <?php foreach (subject_ai_types() as $key => $label):
      $c = (int) ($aiTypeCounts[$key] ?? 0);
      if ($c === 0) continue;
    ?>
      <a class="chip" href="<?= e(subjects_link(['ai_type' => $key, 'page' => 1])) ?>"
         style="<?= $aiType === $key ? 'border-color:var(--primary);color:var(--primary)' : '' ?>">
        <?= e($label) ?>
        <span class="muted" style="margin-left:4px">· <?= $c ?></span>
      </a>
    <?php endforeach; ?>
  </div>
</div>
<?php endif; ?>

<div class="card">
  <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;margin-bottom:12px">
    <h3 style="margin:0">All subjects <span class="muted" style="font-size:14px">(<?= $total ?>)</span></h3>
    <div style="display:flex;gap:6px;flex-wrap:wrap">
      <a class="btn btn--sm <?= $levelId === 0 ? '' : 'btn--ghost' ?>" href="<?= e(subjects_link(['level' => 0, 'page' => 1])) ?>">
        All levels <span class="muted" style="font-size:11px;margin-left:4px">· <?= $levelCountsAll ?></span>
      </a>
      <?php foreach ($levels as $l):
        $c = (int) ($levelCounts[(int) $l['id']] ?? 0);
      ?>
        <a class="btn btn--sm <?= $levelId === (int)$l['id'] ? '' : 'btn--ghost' ?>" href="<?= e(subjects_link(['level' => (int)$l['id'], 'page' => 1])) ?>">
          <?= e($l['name']) ?> <span class="muted" style="font-size:11px;margin-left:4px">· <?= $c ?></span>
        </a>
      <?php endforeach; ?>
    </div>
  </div>

  <form method="get" style="display:flex;gap:8px;margin-bottom:14px;flex-wrap:wrap;align-items:center">
    <input class="input" name="q" placeholder="Search subject name or slug" value="<?= e($q) ?>" style="flex:1;min-width:220px">
    <?php if ($levelId > 0): ?><input type="hidden" name="level" value="<?= $levelId ?>"><?php endif; ?>
    <?php if ($aiType !== ''): ?><input type="hidden" name="ai_type" value="<?= e($aiType) ?>"><?php endif; ?>
    <?php if ($hasAiCols): ?>
    <select name="prompt_filter" class="input" style="max-width:200px" onchange="this.form.submit()">
      <option value="">All AI prompts</option>
      <option value="custom"  <?= $promptFilter === 'custom'  ? 'selected' : '' ?>>Custom prompt set</option>
      <option value="default" <?= $promptFilter === 'default' ? 'selected' : '' ?>>Default prompt only</option>
    </select>
    <?php endif; ?>
    <select name="per_page" class="input" style="max-width:130px" onchange="this.form.submit()">
      <?php foreach ([20, 50, 100, 200] as $pp): ?>
        <option value="<?= $pp ?>" <?= $perPage === $pp ? 'selected' : '' ?>><?= $pp ?> / page</option>
      <?php endforeach; ?>
    </select>
    <button class="btn btn--sm">Search</button>
    <?php if ($q !== '' || $levelId > 0 || $aiType !== '' || $promptFilter !== ''): ?>
      <a class="btn btn--sm btn--ghost" href="<?= e(subjects_link(['q' => '', 'level' => 0, 'ai_type' => '', 'prompt_filter' => '', 'page' => 1])) ?>">Clear</a>
    <?php endif; ?>
  </form>

  <?php if (!$rows): ?>
    <p class="muted">No subjects match these filters.</p>
  <?php else: ?>
    <div class="subj-list">
      <?php foreach ($rows as $s):
        $hasCustom = $hasAiCols && !empty($s['ai_prompt']);
        $aiTypeLabel = $hasAiCols && !empty($s['ai_subject_type'])
          ? (subject_ai_types()[$s['ai_subject_type']] ?? $s['ai_subject_type'])
          : '';
      ?>
        <div class="subj-row">
          <div class="subj-row__main">
            <div class="subj-row__name">
              <?= e($s['name']) ?>
              <?php if ($s['level']): ?>
                <span class="badge" style="margin-left:6px;font-size:10px"><?= e($s['level']) ?></span>
              <?php endif; ?>
              <?php if ($hasAiCols && $aiTypeLabel): ?>
                <span class="badge" style="margin-left:4px;font-size:10px;color:var(--accent);border-color:var(--accent)"><?= e($s['ai_subject_type']) ?></span>
              <?php endif; ?>
              <?php if ($hasAiCols): ?>
                <?php if ($hasCustom): ?>
                  <span class="badge badge--good" style="margin-left:4px;font-size:10px" title="Custom AI prompt is set">AI ✓</span>
                <?php else: ?>
                  <span class="badge" style="margin-left:4px;font-size:10px" title="Using composed default prompt">AI default</span>
                <?php endif; ?>
              <?php endif; ?>
            </div>
            <div class="subj-row__slug muted"><?= e($s['slug']) ?></div>
          </div>
          <a class="subj-row__stat" href="<?= url('admin/topics.php?subject=' . (int)$s['id']) ?>">
            <span class="muted">Topics</span>
            <strong><?= (int)$s['topic_count'] ?></strong>
          </a>
          <a class="subj-row__stat" href="<?= url('admin/skills.php?subject=' . (int)$s['id']) ?>">
            <span class="muted">Skills</span>
            <strong><?= (int)$s['skill_count'] ?></strong>
          </a>
          <a class="subj-row__stat" href="<?= url('admin/questions.php?subject=' . (int)$s['id']) ?>">
            <span class="muted">Qs</span>
            <strong><?= (int)$s['question_count'] ?></strong>
            <?php if ((int)$s['pending_count'] > 0): ?>
              <span class="muted" style="font-size:11px;color:var(--warn)"><?= (int)$s['pending_count'] ?> pend.</span>
            <?php endif; ?>
          </a>
          <div class="subj-row__actions">
            <a class="btn btn--sm btn--ghost" href="<?= e(subjects_link(['edit' => (int)$s['id']])) ?>">Edit</a>
            <form method="post" style="display:inline" onsubmit="return confirm('Delete this subject and its topics?')">
              <?= csrf_field() ?>
              <input type="hidden" name="action" value="delete">
              <input type="hidden" name="id" value="<?= (int)$s['id'] ?>">
              <button class="btn btn--sm btn--danger">Delete</button>
            </form>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <?php if ($pages > 1): ?>
    <div style="display:flex;justify-content:space-between;align-items:center;margin-top:18px;gap:10px;flex-wrap:wrap">
      <span class="muted" style="font-size:13px">Page <?= $page ?> of <?= $pages ?> · <?= $total ?> total</span>
      <div style="display:flex;gap:6px;flex-wrap:wrap">
        <?php if ($page > 1): ?>
          <a class="btn btn--sm btn--ghost" href="<?= e(subjects_link(['page' => 1])) ?>">« First</a>
          <a class="btn btn--sm btn--ghost" href="<?= e(subjects_link(['page' => $page - 1])) ?>">‹ Prev</a>
        <?php endif; ?>
        <?php for ($p = max(1, $page - 2); $p <= min($pages, $page + 2); $p++): ?>
          <a class="btn btn--sm <?= $p === $page ? '' : 'btn--ghost' ?>" href="<?= e(subjects_link(['page' => $p])) ?>"><?= $p ?></a>
        <?php endfor; ?>
        <?php if ($page < $pages): ?>
          <a class="btn btn--sm btn--ghost" href="<?= e(subjects_link(['page' => $page + 1])) ?>">Next ›</a>
          <a class="btn btn--sm btn--ghost" href="<?= e(subjects_link(['page' => $pages])) ?>">Last »</a>
        <?php endif; ?>
      </div>
    </div>
  <?php endif; ?>
</div>

<style>
.subj-list { display:flex; flex-direction:column; gap:8px; }
.subj-row {
  display:flex; align-items:center; gap:14px;
  padding:12px 14px;
  border:1px solid var(--border);
  border-radius:10px;
  background:var(--card-2);
}
.subj-row:hover { border-color:var(--primary); }
.subj-row__main { flex:1; min-width:200px; }
.subj-row__name { font-weight:600; font-size:14px; display:flex; align-items:center; flex-wrap:wrap; }
.subj-row__slug { font-size:11px; margin-top:3px; }
.subj-row__stat {
  display:flex; flex-direction:column; align-items:center;
  width:80px; text-align:center; font-size:12px;
  padding:6px 4px; border-radius:8px;
  text-decoration:none; color:inherit;
  transition: background .15s;
}
.subj-row__stat:hover { background:rgba(139,92,246,.08); }
.subj-row__stat strong { font-size:16px; font-weight:700; margin:2px 0; color:var(--text); }
.subj-row__actions { display:flex; gap:6px; flex-wrap:wrap; }
@media (max-width: 880px) {
  .subj-row { flex-wrap:wrap; }
  .subj-row__stat { width:auto; min-width:64px; flex:1; }
  .subj-row__actions { width:100%; justify-content:flex-end; margin-top:8px; }
}
</style>
<?php
admin_layout_end();

// ----------------------------------------------------------------
// Subject form renderer (used for both create and edit).
// ----------------------------------------------------------------
function render_subject_form(?array $edit, array $levels, bool $hasAiCols): void
{
    ?>
    <form method="post">
      <?= csrf_field() ?>
      <input type="hidden" name="action" value="<?= $edit ? 'update' : 'create' ?>">
      <?php if ($edit): ?><input type="hidden" name="id" value="<?= (int)$edit['id'] ?>"><?php endif; ?>
      <div class="grid grid--2">
        <div class="field"><label>Name *</label><input class="input" name="name" value="<?= e($edit['name'] ?? '') ?>" required></div>
        <div class="field"><label>Education level</label>
          <select name="education_level_id" class="input">
            <option value="">-- none --</option>
            <?php foreach ($levels as $l): ?>
              <option value="<?= (int)$l['id'] ?>" <?= (int)($edit['education_level_id'] ?? 0) === (int)$l['id'] ? 'selected' : '' ?>><?= e($l['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
      <div class="field"><label>Description</label><textarea name="description" rows="2"><?= e($edit['description'] ?? '') ?></textarea></div>

      <?php if ($hasAiCols): ?>
      <details <?= $edit ? 'open' : '' ?> style="margin:14px 0;padding:14px;border:1px solid var(--border);border-radius:12px">
        <summary style="cursor:pointer;font-weight:600">AI generation settings <span class="muted" style="font-weight:400;font-size:13px">— controls how questions are generated for this subject</span></summary>
        <div class="grid grid--3" style="margin-top:14px">
          <div class="field"><label>Subject type</label>
            <select name="ai_subject_type" class="input">
              <option value="">-- pick --</option>
              <?php foreach (subject_ai_types() as $k => $label): ?>
                <option value="<?= e($k) ?>" <?= ($edit['ai_subject_type'] ?? '') === $k ? 'selected' : '' ?>><?= e($label) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="field"><label>Exam board / syllabus</label><input class="input" name="ai_exam_board" value="<?= e($edit['ai_exam_board'] ?? '') ?>" placeholder="e.g. Malaysian SPM (KSSM)"></div>
          <div class="field"><label>Language</label>
            <select name="ai_language" class="input">
              <option value="">-- pick --</option>
              <?php foreach (['English', 'Bahasa Melayu', 'Bilingual', 'Chinese', 'Tamil', 'Arabic'] as $lang): ?>
                <option <?= ($edit['ai_language'] ?? '') === $lang ? 'selected' : '' ?>><?= e($lang) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        <div class="field"><label>Extra rules / banned topics (optional)</label><textarea name="ai_notes" rows="2" placeholder="e.g. Avoid politically sensitive examples. Use Malaysian currency RM. Always include working in maths explanations."><?= e($edit['ai_notes'] ?? '') ?></textarea></div>
        <div class="field"><label>Full AI prompt <span class="muted" style="font-size:13px">— leave blank to auto-compose from the fields above, or override here</span></label><textarea name="ai_prompt" style="min-height:130px;font-family:monospace;font-size:13px" placeholder="<?= e($edit ? subject_ai_default($edit) : '(blank = auto from the fields above)') ?>"><?= e($edit['ai_prompt'] ?? '') ?></textarea></div>
        <?php if ($edit): ?>
          <p style="margin:6px 0 0">
            <button type="submit" form="reset-prompt-form" class="btn btn--sm btn--ghost">Reset prompt to default</button>
            <span class="muted" style="font-size:12px;margin-left:8px">(writes the composed default into the full AI prompt field)</span>
          </p>
        <?php endif; ?>
      </details>
      <?php endif; ?>

      <button class="btn"><?= $edit ? 'Save changes' : 'Create subject' ?></button>
      <?php if ($edit): ?><a class="btn btn--ghost" href="<?= e(subjects_link(['edit' => null])) ?>">Cancel</a><?php endif; ?>
    </form>
    <?php if ($edit && $hasAiCols): ?>
      <form id="reset-prompt-form" method="post" style="display:none">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="reset_prompt">
        <input type="hidden" name="id" value="<?= (int)$edit['id'] ?>">
      </form>
    <?php endif; ?>
    <?php
}
