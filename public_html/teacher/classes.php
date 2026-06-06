<?php
declare(strict_types=1);
require_once __DIR__ . '/../inc/auth.php';
require_once __DIR__ . '/../inc/classes.php';
require_once __DIR__ . '/../inc/teacher_layout.php';

$user = require_role('teacher');
$tid  = (int) $user['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $action = input('action');

    if ($action === 'create_class') {
        $name = input('name');
        if ($name !== '') {
            create_class($tid, $name, input_int('subject_id') ?: null);
            flash('success', 'Class created.');
        } else {
            flash('error', 'Class name required.');
        }
    } elseif ($action === 'add_student') {
        $classId = input_int('class_id');
        if (teacher_owns_class($tid, $classId)) {
            [$ok, $msg] = add_student_to_class($classId, strtolower(input('email')));
            flash($ok ? 'success' : 'error', $msg);
        }
    } elseif ($action === 'create_assignment') {
        $classId = input_int('class_id');
        if (teacher_owns_class($tid, $classId) && input('title') !== '') {
            create_assignment($classId, input('title'), input('description'), input('due_date') ?: null);
            flash('success', 'Assignment created and students notified.');
        } else {
            flash('error', 'Title required.');
        }
    }
    redirect('teacher/classes.php');
}

$subjects = db_all('SELECT id, name FROM subjects WHERE status = "active" ORDER BY name');
$classes  = teacher_classes($tid);
$openId   = input_int('class');

teacher_layout_start('My Classes', $user, 'classes.php');
?>
<div class="card">
  <h3>Create a class</h3>
  <form method="post" style="display:flex;gap:10px;flex-wrap:wrap;align-items:end">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="create_class">
    <div class="field" style="margin:0;flex:1;min-width:200px"><label>Class name</label><input class="input" name="name" required></div>
    <div class="field" style="margin:0"><label>Subject</label>
      <select name="subject_id" class="input"><option value="">-- optional --</option>
        <?php foreach ($subjects as $s): ?><option value="<?= (int)$s['id'] ?>"><?= e($s['name']) ?></option><?php endforeach; ?>
      </select>
    </div>
    <button class="btn">Create</button>
  </form>
</div>

<?php foreach ($classes as $c): $cid = (int) $c['id']; $open = $cid === $openId; ?>
  <div class="card" style="margin-top:18px">
    <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:8px">
      <h3 style="margin:0"><?= e($c['name']) ?> <span class="muted" style="font-size:13px"><?= e($c['subject'] ?? '') ?> · <?= (int)$c['student_count'] ?> students</span></h3>
      <a class="btn btn--sm btn--ghost" href="<?= url('teacher/classes.php?class=' . $cid) ?>"><?= $open ? 'Hide' : 'Manage' ?></a>
    </div>
    <?php if ($open):
        $students = class_students($cid);
        $assignments = class_assignments($cid); ?>
      <div class="grid grid--2" style="margin-top:14px">
        <div>
          <strong>Students</strong>
          <table class="table"><tbody>
          <?php foreach ($students as $st): ?>
            <tr><td><?= e($st['name']) ?></td><td><?= (int)$st['answered'] ?> Q</td><td><?= e(number_format((float)$st['avg_score'],0)) ?>%</td><td><?= (int)$st['streak'] ?>🔥</td></tr>
          <?php endforeach; ?>
          <?php if (!$students): ?><tr><td class="muted">No students yet.</td></tr><?php endif; ?>
          </tbody></table>
          <form method="post" style="display:flex;gap:8px;margin-top:8px">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="add_student">
            <input type="hidden" name="class_id" value="<?= $cid ?>">
            <input class="input" name="email" type="email" placeholder="Student email" required>
            <button class="btn btn--sm">Add</button>
          </form>
        </div>
        <div>
          <strong>Assignments</strong>
          <table class="table"><tbody>
          <?php foreach ($assignments as $a): ?>
            <tr><td><?= e($a['title']) ?></td><td class="muted"><?= $a['due_date'] ? e(date('d M', strtotime((string)$a['due_date']))) : '—' ?></td><td><?= (int)$a['submissions'] ?> subs</td></tr>
          <?php endforeach; ?>
          <?php if (!$assignments): ?><tr><td class="muted">No assignments yet.</td></tr><?php endif; ?>
          </tbody></table>
          <form method="post" style="margin-top:8px">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="create_assignment">
            <input type="hidden" name="class_id" value="<?= $cid ?>">
            <div class="field" style="margin:6px 0"><input class="input" name="title" placeholder="Assignment title" required></div>
            <div class="field" style="margin:6px 0"><textarea name="description" placeholder="Instructions"></textarea></div>
            <div class="field" style="margin:6px 0"><input class="input" type="date" name="due_date"></div>
            <button class="btn btn--sm">Create assignment</button>
          </form>
        </div>
      </div>
    <?php endif; ?>
  </div>
<?php endforeach; ?>
<?php if (!$classes): ?><div class="card" style="margin-top:18px"><p class="muted">No classes yet. Create your first class above.</p></div><?php endif; ?>
<?php
teacher_layout_end();
