<?php
declare(strict_types=1);
require_once __DIR__ . '/../inc/auth.php';
require_once __DIR__ . '/../inc/notifications.php';
require_once __DIR__ . '/../inc/teacher_layout.php';

$user = require_role('teacher');
$tid  = (int) $user['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $uploadId = input_int('upload_id');
    $override = input('override_score');
    $comment  = input('comment');
    $upload   = db_one('SELECT * FROM answer_uploads WHERE id = ?', [$uploadId]);
    if ($upload) {
        db_exec(
            'INSERT INTO teacher_review_results (upload_id, teacher_user_id, override_score, comment) VALUES (?,?,?,?)',
            [$uploadId, $tid, $override === '' ? null : (float) $override, $comment ?: null]
        );
        db_exec('UPDATE answer_uploads SET status = "reviewed" WHERE id = ?', [$uploadId]);
        notify(
            (int) $upload['user_id'],
            'A teacher reviewed your answer',
            ($override !== '' ? 'Updated score: ' . $override . '. ' : '') . ($comment ?: ''),
            'review'
        );
        flash('success', 'Review saved and student notified.');
    } else {
        flash('error', 'Upload not found.');
    }
    redirect('teacher/review.php');
}

$rows = db_all(
    'SELECT au.id, au.file_path, au.ocr_text, au.status, au.created_at,
            u.name AS student, q.question_text,
            r.score AS ai_score, r.correct_parts, r.mistakes, r.correction, r.topic_weakness, r.confidence,
            tr.override_score, tr.comment AS teacher_comment
     FROM answer_uploads au
     JOIN users u ON u.id = au.user_id
     LEFT JOIN questions q ON q.id = au.question_id
     LEFT JOIN ai_marking_results r ON r.upload_id = au.id
     LEFT JOIN teacher_review_results tr ON tr.upload_id = au.id
     WHERE au.status IN ("marked","reviewed","uploaded")
     ORDER BY au.id DESC LIMIT 50'
);

teacher_layout_start('Review AI Marking', $user, 'review.php');
?>
<?php if (!$rows): ?>
  <div class="card"><p class="muted">No submissions to review yet. Student Snap &amp; Check uploads will appear here.</p></div>
<?php else: foreach ($rows as $r): ?>
  <div class="card" style="margin-bottom:18px">
    <div style="display:flex;justify-content:space-between;flex-wrap:wrap;gap:10px">
      <h3 style="margin:0"><?= e($r['student']) ?> <span class="muted" style="font-size:13px"><?= e(date('d M, H:i', strtotime((string) $r['created_at']))) ?></span></h3>
      <span class="badge <?= $r['status'] === 'reviewed' ? 'badge--good' : 'badge--warn' ?>"><?= e($r['status']) ?></span>
    </div>
    <?php if ($r['question_text']): ?><p class="muted"><strong>Q:</strong> <?= e($r['question_text']) ?></p><?php endif; ?>
    <?php if ($r['file_path']): ?><p><a href="<?= url($r['file_path']) ?>" target="_blank">View uploaded image</a></p><?php endif; ?>
    <?php if ($r['ocr_text']): ?><p class="muted"><strong>Answer:</strong> <?= nl2br(e($r['ocr_text'])) ?></p><?php endif; ?>

    <div class="grid grid--2">
      <div>
        <strong>AI marking</strong>
        <p class="muted">Score: <?= $r['ai_score'] !== null ? e((string) $r['ai_score']) : 'n/a' ?><?= $r['confidence'] !== null ? ' (confidence ' . e(number_format((float) $r['confidence'] * 100, 0)) . '%)' : '' ?></p>
        <?php if ($r['mistakes']): ?><p class="muted"><strong>Mistakes:</strong> <?= e($r['mistakes']) ?></p><?php endif; ?>
        <?php if ($r['topic_weakness']): ?><p class="muted"><strong>Weakness:</strong> <?= e($r['topic_weakness']) ?></p><?php endif; ?>
      </div>
      <div>
        <strong>Your review</strong>
        <?php if ($r['teacher_comment'] !== null || $r['override_score'] !== null): ?>
          <p class="muted">Override: <?= $r['override_score'] !== null ? e((string) $r['override_score']) : '—' ?><br><?= e($r['teacher_comment'] ?? '') ?></p>
        <?php endif; ?>
        <form method="post">
          <?= csrf_field() ?>
          <input type="hidden" name="upload_id" value="<?= (int) $r['id'] ?>">
          <div class="field" style="margin:6px 0"><input class="input" name="override_score" placeholder="Override score (optional)"></div>
          <div class="field" style="margin:6px 0"><textarea name="comment" placeholder="Comment to the student"></textarea></div>
          <button class="btn btn--sm">Save review</button>
        </form>
      </div>
    </div>
  </div>
<?php endforeach; endif; ?>
<?php
teacher_layout_end();
