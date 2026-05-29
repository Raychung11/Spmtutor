<?php
declare(strict_types=1);
require_once __DIR__ . '/../inc/auth.php';
require_once __DIR__ . '/../inc/student_layout.php';

$user = require_role('student');
$uid  = (int) $user['id'];

$subjects = db_all('SELECT id, name FROM subjects WHERE status = "active" ORDER BY sort_order');
$recent   = db_all('SELECT id, title, created_at FROM ai_chat_sessions WHERE user_id = ? ORDER BY updated_at DESC LIMIT 8', [$uid]);

student_layout_start('AI Tutor', $user, 'tutor.php');
?>
<div class="tutor-layout">
  <div class="card">
    <div id="chat" class="chat" data-api="<?= url('api/tutor.php') ?>" data-csrf="<?= e(csrf_token()) ?>">
      <div style="display:flex;gap:10px;align-items:center;margin-bottom:10px;flex-wrap:wrap">
        <select id="subjectSel" class="input" style="max-width:220px">
          <option value="">General</option>
          <?php foreach ($subjects as $s): ?>
            <option value="<?= (int)$s['id'] ?>"><?= e($s['name']) ?></option>
          <?php endforeach; ?>
        </select>
        <span class="muted" style="font-size:13px">LulusAI explains step-by-step.</span>
      </div>
      <div id="chatLog" class="chat__log">
        <div class="msg msg--assistant">Hi <?= e($user['name']) ?>! I'm your AI tutor. Pick a subject and ask me anything &mdash; I'll guide you step by step. 😊</div>
      </div>
      <div style="margin-top:10px">
        <span class="chip" data-prompt="Explain how to solve a linear equation step by step.">Linear equations</span>
        <span class="chip" data-prompt="Help me understand quadratic factorisation.">Quadratics</span>
        <span class="chip" data-prompt="Give me a study tip for SPM Maths.">SPM study tip</span>
      </div>
      <form id="chatForm" class="chat__form">
        <input type="hidden" id="sessionId" value="">
        <textarea id="chatInput" class="input" placeholder="Type your question..." required></textarea>
        <button class="btn" type="submit">Send</button>
      </form>
    </div>
  </div>
  <div class="card">
    <h3>Recent chats</h3>
    <?php if ($recent): ?>
      <?php foreach ($recent as $r): ?>
        <p class="muted" style="font-size:13px;border-bottom:1px solid var(--border);padding-bottom:8px">
          <?= e($r['title'] ?: 'Untitled chat') ?><br>
          <small><?= e(date('d M, H:i', strtotime($r['created_at']))) ?></small>
        </p>
      <?php endforeach; ?>
    <?php else: ?>
      <p class="muted">Your chat history will appear here.</p>
    <?php endif; ?>
  </div>
</div>
<?php
student_layout_end();
