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
    $count    = max(1, min(20, input_int('count', 5)));
    $diff     = in_array(input('difficulty'), ['mixed', 'easy', 'medium', 'hard'], true) ? input('difficulty') : 'mixed';
    $critique = input('critique') === '1';
    @set_time_limit(180);
    try {
        $result = generate_questions_for_topic($topicId, $count, $diff, $critique);
    } catch (Throwable $e) {
        error_log('[ai_generate] ' . $e->getMessage());
        $result = ['inserted' => 0, 'flagged' => 0, 'errors' => ['Generation crashed: ' . $e->getMessage()]];
    }
}

$effectivePrompt = subject_ai_effective($topic);

admin_layout_start('Generate questions with AI', $admin, 'topics.php');
?>
<div class="card" style="margin-bottom:18px">
  <h2 style="margin:0">Generate questions with AI</h2>
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
    <strong>Generated:</strong> <?= (int)$result['inserted'] ?> questions saved as <code>pending</code>.
    <?php if ($result['flagged'] > 0): ?>
      <br><strong>Flagged by AI critic:</strong> <?= (int)$result['flagged'] ?> — those have a warning note in the explanation.
    <?php endif; ?>
    <?php if (!empty($result['errors'])): ?>
      <br><strong>Notes:</strong>
      <ul style="margin:6px 0 0">
        <?php foreach ($result['errors'] as $err): ?><li><?= e($err) ?></li><?php endforeach; ?>
      </ul>
    <?php endif; ?>
    <?php if ($result['inserted'] > 0): ?>
      <p style="margin-top:8px"><a class="btn btn--sm" href="<?= url('admin/questions.php?subject=' . (int)$topic['subject_id'] . '&topic=' . $topicId . '&status=pending') ?>">Review pending questions</a></p>
    <?php endif; ?>
  </div>
<?php endif; ?>

<div class="card">
  <h3 style="margin-top:0">Generation settings</h3>
  <form method="post">
    <?= csrf_field() ?>
    <div class="grid grid--3">
      <div class="field"><label>How many?</label>
        <input class="input" type="number" name="count" min="1" max="20" value="5">
      </div>
      <div class="field"><label>Difficulty mix</label>
        <select name="difficulty" class="input">
          <option value="mixed">Mixed (easy / medium / hard)</option>
          <option value="easy">All easy</option>
          <option value="medium">All medium</option>
          <option value="hard">All hard</option>
        </select>
      </div>
      <div class="field"><label>AI critic (2-pass)</label>
        <select name="critique" class="input">
          <option value="1" selected>On (safer, ~2x slower)</option>
          <option value="0">Off (faster, no flagging)</option>
        </select>
      </div>
    </div>
    <button class="btn" type="submit">Generate</button>
    <a class="btn btn--ghost" href="<?= url('admin/topics.php?subject=' . (int)$topic['subject_id']) ?>">Back to topics</a>
  </form>
</div>

<div class="card" style="margin-top:18px">
  <h3 style="margin-top:0">Effective subject prompt</h3>
  <p class="muted" style="font-size:13px;margin:0 0 8px">
    This is what the AI receives as its system prompt — edit it under <a href="<?= url('admin/subjects.php?edit=' . (int)$topic['subject_id']) ?>">Admin → Subjects → Edit <?= e($topic['subject']) ?></a>.
  </p>
  <pre style="white-space:pre-wrap;font-family:monospace;font-size:13px;background:var(--bg-2);padding:14px;border-radius:10px;border:1px solid var(--border);margin:0"><?= e($effectivePrompt) ?></pre>
</div>
<?php
admin_layout_end();
