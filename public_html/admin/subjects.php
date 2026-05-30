<?php
declare(strict_types=1);
require_once __DIR__ . '/../inc/auth.php';
require_once __DIR__ . '/../inc/ai_questions.php';
require_once __DIR__ . '/../inc/admin_layout.php';

$admin  = require_role('admin');
$levels = db_all('SELECT id, name FROM education_levels ORDER BY sort_order');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $action = input('action');

    if ($action === 'reset_prompt') {
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
            db_exec(
                'INSERT INTO subjects (education_level_id, name, slug, description, ai_prompt, ai_subject_type, ai_exam_board, ai_language, ai_notes)
                 VALUES (?,?,?,?,?,?,?,?,?)',
                [$level, $name, $slug, $desc, $aiPrompt ?: null, $aiType, $aiBoard ?: null, $aiLanguage ?: null, $aiNotes ?: null]
            );
            flash('success', 'Subject created.');
        } else {
            db_exec(
                'UPDATE subjects SET education_level_id = ?, name = ?, description = ?, ai_prompt = ?, ai_subject_type = ?, ai_exam_board = ?, ai_language = ?, ai_notes = ? WHERE id = ?',
                [$level, $name, $desc, $aiPrompt ?: null, $aiType, $aiBoard ?: null, $aiLanguage ?: null, $aiNotes ?: null, input_int('id')]
            );
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
$q        = trim(input('q'));
$levelId  = input_int('level');
$page     = max(1, input_int('page', 1));
$perPage  = 20;
$offset   = ($page - 1) * $perPage;

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
$whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$total = (int) (db_one("SELECT COUNT(*) c FROM subjects s $whereSql", $params)['c'] ?? 0);
$pages = max(1, (int) ceil($total / $perPage));
if ($page > $pages) { $page = $pages; $offset = ($page - 1) * $perPage; }

$rows = db_all(
    "SELECT s.*, l.name AS level,
            (SELECT COUNT(*) FROM topics t WHERE t.subject_id = s.id) AS topic_count
     FROM subjects s LEFT JOIN education_levels l ON l.id = s.education_level_id
     $whereSql
     ORDER BY s.sort_order, s.id
     LIMIT $perPage OFFSET $offset",
    $params
);

/** Build a filter URL preserving current params. */
function subjects_link(array $overrides = []): string
{
    global $q, $levelId, $page;
    $args = array_merge(['q' => $q, 'level' => $levelId, 'page' => $page], $overrides);
    $args = array_filter($args, fn($v) => $v !== '' && $v !== null && $v !== 0);
    return url('admin/subjects.php' . ($args ? '?' . http_build_query($args) : ''));
}

admin_layout_start('Subjects', $admin, 'subjects.php');
?>
<div class="card" style="margin-bottom:18px">
  <h3 style="margin-top:0"><?= $edit ? 'Edit subject' : 'Add subject' ?></h3>
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
    <div class="field"><label>Description</label><textarea name="description"><?= e($edit['description'] ?? '') ?></textarea></div>

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
            <option <?= ($edit['ai_language'] ?? '') === 'English' ? 'selected' : '' ?>>English</option>
            <option <?= ($edit['ai_language'] ?? '') === 'Bahasa Melayu' ? 'selected' : '' ?>>Bahasa Melayu</option>
            <option <?= ($edit['ai_language'] ?? '') === 'Bilingual' ? 'selected' : '' ?>>Bilingual</option>
            <option <?= ($edit['ai_language'] ?? '') === 'Chinese' ? 'selected' : '' ?>>Chinese</option>
            <option <?= ($edit['ai_language'] ?? '') === 'Tamil' ? 'selected' : '' ?>>Tamil</option>
            <option <?= ($edit['ai_language'] ?? '') === 'Arabic' ? 'selected' : '' ?>>Arabic</option>
          </select>
        </div>
      </div>
      <div class="field"><label>Extra rules / banned topics (optional)</label><textarea name="ai_notes" placeholder="e.g. Avoid politically sensitive examples. Use Malaysian currency RM. Always include working in maths explanations."><?= e($edit['ai_notes'] ?? '') ?></textarea></div>
      <div class="field"><label>Full AI prompt <span class="muted" style="font-size:13px">— leave blank to auto-compose from the fields above, or override here</span></label><textarea name="ai_prompt" style="min-height:130px;font-family:monospace;font-size:13px" placeholder="<?= e($edit ? subject_ai_default($edit) : '(blank = auto from the fields above)') ?>"><?= e($edit['ai_prompt'] ?? '') ?></textarea></div>
      <?php if ($edit): ?>
        <p style="margin:6px 0 0">
          <button type="submit" form="reset-prompt-form" class="btn btn--sm btn--ghost" formaction="<?= url('admin/subjects.php') ?>">Reset prompt to default</button>
          <span class="muted" style="font-size:12px;margin-left:8px">(writes the composed default into the full AI prompt field)</span>
        </p>
      <?php endif; ?>
    </details>

    <button class="btn"><?= $edit ? 'Save changes' : 'Create' ?></button>
    <?php if ($edit): ?><a class="btn btn--ghost" href="<?= e(subjects_link(['edit' => null])) ?>">Cancel</a><?php endif; ?>
  </form>
  <?php if ($edit): ?>
    <form id="reset-prompt-form" method="post" style="display:none">
      <?= csrf_field() ?>
      <input type="hidden" name="action" value="reset_prompt">
      <input type="hidden" name="id" value="<?= (int)$edit['id'] ?>">
    </form>
  <?php endif; ?>
</div>

<div class="card">
  <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;margin-bottom:12px">
    <h3 style="margin:0">All subjects <span class="muted" style="font-size:14px">(<?= $total ?>)</span></h3>
    <div style="display:flex;gap:6px;flex-wrap:wrap">
      <a class="btn btn--sm <?= $levelId === 0 ? '' : 'btn--ghost' ?>" href="<?= e(subjects_link(['level' => 0, 'page' => 1])) ?>">All levels</a>
      <?php foreach ($levels as $l): ?>
        <a class="btn btn--sm <?= $levelId === (int)$l['id'] ? '' : 'btn--ghost' ?>" href="<?= e(subjects_link(['level' => (int)$l['id'], 'page' => 1])) ?>"><?= e($l['name']) ?></a>
      <?php endforeach; ?>
    </div>
  </div>
  <form method="get" style="display:flex;gap:8px;margin-bottom:14px;flex-wrap:wrap">
    <input class="input" name="q" placeholder="Search subject name or slug" value="<?= e($q) ?>" style="flex:1;min-width:220px">
    <?php if ($levelId > 0): ?><input type="hidden" name="level" value="<?= $levelId ?>"><?php endif; ?>
    <button class="btn btn--sm">Search</button>
    <?php if ($q !== ''): ?><a class="btn btn--sm btn--ghost" href="<?= e(subjects_link(['q' => '', 'page' => 1])) ?>">Clear</a><?php endif; ?>
  </form>

  <table class="table">
    <thead><tr><th>Name</th><th>Level</th><th>Topics</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($rows as $s): ?>
      <tr>
        <td><?= e($s['name']) ?><br><span class="muted" style="font-size:12px"><?= e($s['slug']) ?></span></td>
        <td class="muted"><?= e($s['level'] ?? '—') ?></td>
        <td><?= (int)$s['topic_count'] ?></td>
        <td style="white-space:nowrap">
          <a class="btn btn--sm btn--ghost" href="<?= e(subjects_link(['edit' => (int)$s['id']])) ?>">Edit</a>
          <form method="post" style="display:inline" onsubmit="return confirm('Delete this subject and its topics?')">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="<?= (int)$s['id'] ?>">
            <button class="btn btn--sm btn--danger">Delete</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
    <?php if (!$rows): ?><tr><td colspan="4" class="muted">No subjects match these filters.</td></tr><?php endif; ?>
    </tbody>
  </table>

  <?php if ($pages > 1): ?>
    <div style="display:flex;justify-content:space-between;align-items:center;margin-top:14px;gap:10px;flex-wrap:wrap">
      <span class="muted" style="font-size:13px">Page <?= $page ?> of <?= $pages ?> · <?= $total ?> total</span>
      <div style="display:flex;gap:6px">
        <?php if ($page > 1): ?>
          <a class="btn btn--sm btn--ghost" href="<?= e(subjects_link(['page' => 1])) ?>">« First</a>
          <a class="btn btn--sm btn--ghost" href="<?= e(subjects_link(['page' => $page - 1])) ?>">‹ Prev</a>
        <?php endif; ?>
        <?php
          $from = max(1, $page - 2);
          $to   = min($pages, $page + 2);
          for ($p = $from; $p <= $to; $p++):
        ?>
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
<?php
admin_layout_end();
