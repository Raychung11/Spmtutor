# SkillTutor AI — Education OS

> "Not just an app. Your personal AI tutor."

An AI-powered skill education platform (ApexTutor-style) built as a scalable MVP
in **native PHP 8 + MySQL**, designed for **Hostinger shared hosting** first and
**upgrade-ready** for VPS / Laravel later. Mobile-first, dark/purple SaaS UI,
WhatsApp + AI API integration-ready.

---

## 1. System architecture

```
Browser (mobile-first, dark theme)
      │  HTML + a light app.js (fetch for AI chat & cascading selects)
      ▼
Native PHP 8 front controllers (public_html/*.php + role folders)
      │            │                 │
   inc/auth.php  inc/helpers.php   inc/ui.php (+ *_layout.php)
      │            │                 │
      ▼            ▼                 ▼
            inc/db.php (PDO, prepared statements)
                     │
                  MySQL (utf8mb4, InnoDB, FKs + indexes)
                     ▲
            inc/ai.php ── LLM provider (Anthropic / OpenAI), key-gated,
                          with safe offline fallback when no key is set.
```

**Principles**
- No framework, no Composer required → drops straight onto shared hosting.
- All DB access via PDO prepared statements (`db_one/db_all/db_exec`).
- Output escaped with `e()`; CSRF on every POST; hardened sessions.
- AI is abstracted behind `ai_chat()` + editable DB prompt templates, so the
  same code works with or without an API key and across providers.
- Layouts are functions, not a template engine — easy to port to Blade later.

## 2. Modules (15)

| # | Module | Status in this build |
|---|--------|----------------------|
| 1 | Auth & user management | **Built** (register/login/logout/forgot, roles, profiles, parent-child link, login logs) |
| 2 | Subject & skill management | **Built** (levels, subjects, topics, skills admin CRUD) |
| 3 | AI Tutor chat | **Built** (sessions, messages, editable prompt, provider + fallback) |
| 4 | Diagnostic engine | **Built** (subject quiz, auto grading, strong/weak topics, AI recommendation) |
| 5 | Practice question bank | **Built** (MCQ practice, attempts, options, admin CRUD) |
| 6 | Snap & Check / AI marking | **Built** (image/text upload, AI marking → score/mistakes/correction, teacher override) |
| 7 | Learning path engine | **Built** (auto-generated from diagnostic weak topics, item tracking) |
| 8 | Progress tracking | **Built** (summary, subject/topic stats, streaks) |
| 9 | Parent dashboard | **Built** (link child, view progress, weekly AI report) |
| 10 | Teacher dashboard | **Built** (student overview, AI-marking review + score override + comment) |
| 11 | Gamification | **Built** (XP, levels, streaks, badges) |
| 12 | Subscription & payment | **Built** (plans, auto trial, Billplz checkout + callback, invoices) |
| 13 | Notifications | **Built** (in-app bell + feed; email/WhatsApp channels integration-ready) |
| 14 | Admin panel | **Built** (dashboard, users, subjects, topics, skills, questions, AI prompts) |
| 15 | Landing page CMS | **Built** (editable sections, testimonials, FAQs) |

## 3. Database

Full schema in [`public_html/sql/schema.sql`](public_html/sql/schema.sql) — all
phases. Seed data in [`public_html/sql/seed.sql`](public_html/sql/seed.sql).
Every table uses `id`, `created_at`/`updated_at` where relevant, `status`
columns, foreign keys, and indexes on `user_id` / `subject_id` / `topic_id`.

## 4. Folder structure

```
public_html/
  index.php  pricing.php  login.php  register.php  logout.php
  forgot-password.php  install.php
  config/   config.php  db_config.php  (.htaccess deny)
  inc/      db.php auth.php helpers.php ui.php ai.php progress.php
            *_layout.php  (.htaccess deny)
  notifications.php  (shared, role-aware)
  inc/      ... + diagnostic.php learning.php billing.php reports.php
            marking.php notifications.php
  admin/    dashboard users subjects topics skills questions ai_prompts landing
  student/  dashboard tutor diagnostic learning_path practice snap_check
            progress subscription
  parent/   dashboard
  teacher/  dashboard review
  api/      tutor.php topics.php billplz_callback.php
  cron/     weekly_reports.php  (CLI only)
  uploads/  (no script execution)
  assets/   css/style.css  js/app.js  img/
  sql/      schema.sql  seed.sql  (.htaccess deny)
```

## 5. Development roadmap

- **Phase 1 (this build):** Auth, student dashboard, subject/topic/skill setup,
  AI tutor chat, question bank + practice, progress tracking, gamification,
  admin panel, landing CMS, pricing, plans + auto trial.
- **Phase 2 (done):** Diagnostic engine, learning-path generation, parent
  weekly AI reports (on-demand + cron), Billplz checkout + callback + invoices.
- **Phase 3 (done):** Snap & Check AI marking (image/typed answer + marking
  prompt, JSON-parsed feedback), teacher review & score override, in-app
  notifications with topbar bell. (OCR provider is integration-ready.)
- **Phase 4:** School/class management, advanced analytics, WhatsApp reminders,
  mobile app API.

## 6. MVP feature priority

1. Auth + role routing → 2. Subjects/topics/skills (admin) → 3. AI tutor →
4. Question bank + practice + progress → 5. Gamification → 6. Admin + landing CMS
→ 7. Pricing + trial. (All delivered in Phase 1.)

## 7. Install

1. Create a MySQL database on Hostinger; edit `public_html/config/db_config.php`
   (or set `DB_*` env vars).
2. Point your domain/document root at `public_html/`.
3. Visit `/install.php`, set the admin email + password, run it.
4. Delete `install.php` afterwards (an `install.lock` is also written).
5. To enable live AI, set env vars `AI_API_KEY` (and optionally `AI_PROVIDER`,
   `AI_MODEL`). Without a key the tutor runs in safe demo mode.
6. To enable live payments, set `BILLPLZ_API_KEY`, `BILLPLZ_COLLECTION_ID`,
   `BILLPLZ_X_SIGNATURE`, `APP_URL` (and `BILLPLZ_SANDBOX=false` for production).
   Without a key, checkout runs in demo mode and activates plans instantly.
7. Schedule `cron/weekly_reports.php` weekly (Hostinger cron) for parent reports.

## 8. AI safety

Prompts (editable in Admin → AI Prompts) instruct the tutor to teach
step-by-step, never give answers without explanation, admit uncertainty, avoid
exam-outcome predictions, stay encouraging, and respect BM/English. Server-side
guards: auth + role checks on every AI endpoint, CSRF, and rate-friendly history
windows.
