/* SkillTutor AI - front-end helpers */
(function () {
  'use strict';

  // ----- AI Tutor chat -----
  var chat = document.getElementById('chat');
  if (chat) {
    var log = document.getElementById('chatLog');
    var form = document.getElementById('chatForm');
    var input = document.getElementById('chatInput');
    var sessionField = document.getElementById('sessionId');
    var apiUrl = chat.getAttribute('data-api');
    var csrf = chat.getAttribute('data-csrf');

    function scrollDown() { log.scrollTop = log.scrollHeight; }

    function addMsg(role, text) {
      var div = document.createElement('div');
      div.className = 'msg msg--' + role;
      div.textContent = text;
      log.appendChild(div);
      scrollDown();
      return div;
    }

    document.querySelectorAll('.chip[data-prompt]').forEach(function (c) {
      c.addEventListener('click', function () {
        input.value = c.getAttribute('data-prompt');
        input.focus();
      });
    });

    form.addEventListener('submit', function (ev) {
      ev.preventDefault();
      var text = input.value.trim();
      if (!text) return;
      addMsg('user', text);
      input.value = '';
      var subject = document.getElementById('subjectSel');
      var pending = addMsg('assistant', '...');
      var btn = form.querySelector('button');
      btn.disabled = true;

      var body = new URLSearchParams();
      body.set('_csrf', csrf);
      body.set('message', text);
      body.set('session_id', sessionField.value || '');
      if (subject) body.set('subject_id', subject.value || '');

      fetch(apiUrl, { method: 'POST', body: body, headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(function (r) { return r.json(); })
        .then(function (data) {
          pending.textContent = data.reply || data.error || 'No response.';
          if (data.session_id) sessionField.value = data.session_id;
          scrollDown();
        })
        .catch(function () { pending.textContent = 'Network error. Please try again.'; })
        .finally(function () { btn.disabled = false; });
    });
    scrollDown();
  }

  // ----- Cascading subject -> topic selects -----
  document.querySelectorAll('[data-load-topics]').forEach(function (sel) {
    var target = document.getElementById(sel.getAttribute('data-target'));
    if (!target) return;
    sel.addEventListener('change', function () {
      var url = sel.getAttribute('data-load-topics') + '?subject_id=' + encodeURIComponent(sel.value);
      fetch(url).then(function (r) { return r.json(); }).then(function (rows) {
        target.innerHTML = '<option value="">-- select topic --</option>';
        rows.forEach(function (t) {
          var o = document.createElement('option');
          o.value = t.id; o.textContent = t.name;
          target.appendChild(o);
        });
      });
    });
  });
})();
