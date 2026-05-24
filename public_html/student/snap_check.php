<?php
declare(strict_types=1);
require_once __DIR__ . '/../inc/auth.php';
require_once __DIR__ . '/../inc/marking.php';
require_once __DIR__ . '/../inc/student_layout.php';

$user = require_role('student');
$uid  = (int) $user['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $questionId = input_int('question_id') ?: null;
    $typed      = input('answer_text');
    $file       = $_FILES['answer_image'] ?? [];

    $uploadId = store_answer_upload($uid, $questionId, $file, $typed);
    if ($uploadId === null) {
        flash('error', 'Please upload a JPG/PNG/WebP image (max 5MB) or type your answer.');
        redirect('student/snap_check.php');
    }
    try {
        run_ai_marking($uploadId);
        flash('success', 'Marked! See your feedback below.');
    } catch (Throwable $e) {
        flash('error', 'Marking failed: ' . (APP_DEBUG ? $e->getMessage() : 'please try again.'));
    }
    redirect('student/snap_check.php?upload=' . $uploadId);
}

$subjects  = db_all('SELECT id, name FROM subjects WHERE status = "active" ORDER BY sort_order');
$questions = db_all('SELECT id, question_text FROM questions WHERE status = "active" ORDER BY id DESC LIMIT 50');

$viewId = input_int('upload');
$uploads = db_all(
    'SELECT au.*, q.question_text FROM answer_uploads au
     LEFT JOIN questions q ON q.id = au.question_id
     WHERE au.user_id = ? ORDER BY au.id DESC LIMIT 20',
    [$uid]
);

student_layout_start('Snap & Check', $user, 'snap_check.php');
?>
<div class="card">
  <h2>Snap &amp; Check</h2>
  <p class="muted">Upload a photo of your handwritten answer (or type it), and the AI will mark it, point out mistakes, and suggest what to practise next. A teacher can review the mark.</p>
  <form method="post" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <div class="field">
      <label>Question (optional)</label>
      <select name="question_id" class="input">
        <option value="">-- not linked to a specific question --</option>
        <?php foreach ($questions as $q): ?>
          <option value="<?= (int) $q['id'] ?>">#<?= (int) $q['id'] ?> — <?= e(mb_substr($q['question_text'], 0, 70)) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="field"><label>Photo of your answer (JPG/PNG/WebP, max 5MB)</label><input class="input" type="file" name="answer_image" accept="image/*"></div>
    <div class="field"><label>Or type your answer / working</label><textarea name="answer_text" placeholder="Type your answer here (used for marking when no OCR is available)"></textarea></div>
    <button class="btn" type="submit">Mark my answer</button>
  </form>
</div>

<?php if ($viewId):
    $res = marking_result($viewId);
    $up  = db_one('SELECT * FROM answer_uploads WHERE id = ? AND user_id = ?', [$viewId, $uid]);
    if ($res && $up): ?>
  <div class="card" style="margin-top:18px">
    <h3>AI feedback</h3>
    <dl class="snap-result">
      <?php if ($res['score'] !== null): ?><dt>Score</dt><dd><strong><?= e((string) $res['score']) ?></strong></dd><?php endif; ?>
      <?php if ($res['correct_parts']): ?><dt>What you got right</dt><dd><?= nl2br(e($res['correct_parts'])) ?></dd><?php endif; ?>
      <?php if ($res['mistakes']): ?><dt>Mistakes</dt><dd><?= nl2br(e($res['mistakes'])) ?></dd><?php endif; ?>
      <?php if ($res['correction']): ?><dt>Suggested correction</dt><dd><?= nl2br(e($res['correction'])) ?></dd><?php endif; ?>
      <?php if ($res['topic_weakness']): ?><dt>Topic to focus on</dt><dd><?= e($res['topic_weakness']) ?></dd><?php endif; ?>
      <?php if ($res['next_recommendation']): ?><dt>Practise next</dt><dd><?= nl2br(e($res['next_recommendation'])) ?></dd><?php endif; ?>
      <?php if ($res['confidence'] !== null): ?><dt>AI confidence</dt><dd><?= e(number_format((float) $res['confidence'] * 100, 0)) ?>%</dd><?php endif; ?>
    </dl>
    <p class="muted" style="font-size:13px">A teacher can review and adjust this mark.</p>
  </div>
  <?php endif; endif; ?>

<div class="card" style="margin-top:18px">
  <h3>Recent submissions</h3>
  <?php if ($uploads): ?>
    <table class="table"><thead><tr><th>#</th><th>Question</th><th>Status</th><th>Date</th><th></th></tr></thead><tbody>
    <?php foreach ($uploads as $u): ?>
      <tr>
        <td><?= (int) $u['id'] ?></td>
        <td><?= e($u['question_text'] ? mb_substr($u['question_text'], 0, 50) : 'Free answer') ?></td>
        <td><span class="badge <?= $u['status'] === 'marked' ? 'badge--good' : '' ?>"><?= e($u['status']) ?></span></td>
        <td class="muted"><?= e(date('d M, H:i', strtotime((string) $u['created_at']))) ?></td>
        <td><a href="<?= url('student/snap_check.php?upload=' . (int) $u['id']) ?>">View</a></td>
      </tr>
    <?php endforeach; ?>
    </tbody></table>
  <?php else: ?><p class="muted">No submissions yet.</p><?php endif; ?>
</div>
<?php
student_layout_end();
