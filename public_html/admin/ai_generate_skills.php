<?php
declare(strict_types=1);
require_once __DIR__ . '/../inc/auth.php';
require_once __DIR__ . '/../inc/ai_questions.php';
require_once __DIR__ . '/../inc/admin_layout.php';

$admin   = require_role('admin', 'creator');
$topicId = input_int('topic_id');

if (!$topicId) {
    flash('error', 'No topic selected.');
    redirect('admin/topics.php');
}
$topic = fetch_topic_with_subject_ai($topicId);
if (!$topic) {
    flash('error', 'Topic not found.');
    redirect('admin/topics.php');
}
$migrationsMissing = !subject_ai_columns_present();

$result = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $count = max(1, min(15, input_int('count', 5)));
    @set_time_limit(120);
    try {
        $result = generate_skills_for_topic($topicId, $count);
    } catch (Throwable $e) {
        error_log('[ai_generate_skills] ' . $e->getMessage());
        $result = ['inserted' => 0, 'errors' => ['Generation crashed: ' . $e->getMessage()]];
    }
}

admin_layout_start('Generate skills with AI', $admin, 'topics.php');
?>
<div class="card" style="margin-bottom:18px">
  <h2 style="margin:0">Generate skills with AI</h2>
  <p class="muted" style="margin:6px 0 0">
    Subject: <strong><?= e($topic['subject']) ?></strong> · Topic: <strong><?= e($topic['name']) ?></strong>
  </p>
</div>

<?php if ($migrationsMissing): ?>
  <div class="flash flash--error">
    <strong>Database migrations are out of date.</strong> The per-subject AI columns are missing.
    Run them from <a href="<?= url('admin/seeders.php') ?>">Admin → Seeders → Run database migrations</a>.
    The AI will still work, but it will use a generic default prompt instead of your subject-specific one.
  </div>
<?php endif; ?>
<?php if ($result): ?>
  <div class="flash flash--<?= $result['inserted'] > 0 ? 'success' : 'error' ?>">
    <strong>Generated:</strong> <?= (int)$result['inserted'] ?> skill(s) added (duplicates skipped).
    <?php if (!empty($result['errors'])): ?>
      <ul style="margin:6px 0 0">
        <?php foreach ($result['errors'] as $err): ?><li><?= e($err) ?></li><?php endforeach; ?>
      </ul>
    <?php endif; ?>
    <?php if ($result['inserted'] > 0): ?>
      <p style="margin-top:8px"><a class="btn btn--sm" href="<?= url('admin/skills.php?topic=' . $topicId) ?>">Review skills for this topic</a></p>
    <?php endif; ?>
  </div>
<?php endif; ?>

<div class="card">
  <h3 style="margin-top:0">Settings</h3>
  <form method="post">
    <?= csrf_field() ?>
    <div class="field"><label>How many skills?</label>
      <input class="input" type="number" name="count" min="1" max="15" value="5" style="max-width:160px">
    </div>
    <button class="btn">Generate</button>
    <a class="btn btn--ghost" href="<?= url('admin/topics.php?subject=' . (int)$topic['subject_id']) ?>">Back to topics</a>
  </form>
  <p class="muted" style="font-size:13px;margin-top:10px">Skills are inserted as active and deduped by name. Edit the per-subject prompt under <a href="<?= url('admin/subjects.php?edit=' . (int)$topic['subject_id']) ?>">Admin → Subjects</a> to change the AI's framing.</p>
</div>
<?php
admin_layout_end();
