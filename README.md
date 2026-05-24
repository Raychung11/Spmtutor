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
| 10b | School / class management | **Built** (classes, roster, assignments + submissions, schools table) |
| 14 | Admin panel | **Built** (dashboard, analytics, users, subjects, topics, skills, questions, subscriptions, schools, AI prompts) |
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
  forgot-password.php  reset-password.php  install.php
  logs/     app.log  (not web-accessible)
  config/   config.php  db_config.php  (.htaccess deny)
  inc/      db.php auth.php helpers.php ui.php ai.php progress.php
            *_layout.php  (.htaccess deny)
  notifications.php  (shared, role-aware)
  inc/      ... + diagnostic.php learning.php billing.php reports.php
            marking.php notifications.php classes.php whatsapp.php api.php
            ratelimit.php mailer.php
  admin/    dashboard analytics users subjects topics skills questions
            subscriptions schools ai_prompts landing
  student/  dashboard tutor diagnostic learning_path practice snap_check
            assignments progress subscription api_tokens
  parent/   dashboard
  teacher/  dashboard classes review
  api/      tutor.php topics.php billplz_callback.php  openapi.yaml
  api/v1/   index auth logout me subjects progress tutor diagnostic
            learning_path assignments notifications tokens  (mobile REST)
  cron/     weekly_reports.php reminders.php seed_demo.php  (CLI only)
  sql/      schema.sql seed.sql content.sql
  sql/migrations/ phase4.sql phase5.sql  (for existing installs)
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
- **Phase 4 (done):** Class & assignment management (+ schools table), advanced
  analytics dashboard, WhatsApp + study-reminder cron, token-based mobile REST
  API (`/api/v1`).
- **Phase 5 (done):** Production hardening (login lockout, rate limiting, global
  error logging, email + password-reset), expanded content pack (Add Maths,
  Physics, Chemistry, Biology), CLI demo-data seeder, and an expanded mobile API
  (diagnostic, learning path, assignments, tokens, logout) with an OpenAPI spec
  and a student token-management screen.

## Mobile API (`/api/v1`)

Bearer-token REST API for a future mobile app. Tokens are issued on login and
stored hashed (`api_tokens`).

| Method | Endpoint | Purpose |
|--------|----------|---------|
| POST | `/api/v1/auth.php` | Login `{email,password}` → `{token,user}` |
| GET  | `/api/v1/me.php` | Current user, XP, streak, progress, unread count |
| GET  | `/api/v1/subjects.php` | Subjects (`?subject_id=` → topics) |
| GET  | `/api/v1/progress.php` | Summary + subject/topic stats |
| POST | `/api/v1/tutor.php` | Ask the AI tutor `{message,subject_id?,session_id?}` |
| GET/POST | `/api/v1/notifications.php` | List / mark-all-read |
| POST | `/api/v1/logout.php` | Revoke the current token |
| GET/POST | `/api/v1/diagnostic.php` | Questions/attempts; submit a diagnostic |
| GET/POST | `/api/v1/learning_path.php` | Active paths; update item status |
| GET/POST | `/api/v1/assignments.php` | List / submit assignments |
| GET/POST | `/api/v1/tokens.php` | List / revoke tokens |

Send `Authorization: Bearer <token>` on authenticated calls. CORS enabled.
Full spec: [`/api/openapi.yaml`](public_html/api/openapi.yaml). Students manage
tokens at **Student → API Access**.

## Production hardening (Phase 5)

- **Login lockout:** after `LOGIN_MAX_ATTEMPTS` failures from an email/IP within
  `LOGIN_LOCKOUT_MINUTES`, further attempts are blocked.
- **Rate limiting:** `rate_limit()` (table-backed) caps AI tutor calls per user.
- **Error logging:** uncaught exceptions/fatals are logged to `logs/app.log`;
  users see a generic message (no stack traces) unless `APP_DEBUG`.
- **Email:** `send_email()` (PHP mail, gated by `MAIL_ENABLED`) powers the
  password-reset flow (`forgot-password.php` → `reset-password.php`).
- **Demo data:** `php cron/seed_demo.php` populates students, attempts,
  subscriptions, a class, assignments, a Snap & Check marking and parent reports.

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
7. Schedule `cron/weekly_reports.php` weekly and `cron/reminders.php` daily
   (Hostinger cron) for parent reports and study reminders.
8. To enable WhatsApp reminders, set `WHATSAPP_TOKEN` + `WHATSAPP_PHONE_ID`
   (Meta Cloud API). Without them, reminders are in-app only and logged.
9. Existing databases: run `sql/migrations/phase4.sql` then `phase5.sql` once.
10. To send real emails, set `MAIL_ENABLED=true` and `MAIL_FROM`. Otherwise the
    password-reset link is written to `logs/app.log`.
11. (Optional) populate a demo environment: `php cron/seed_demo.php`.

## 8. AI safety

Prompts (editable in Admin → AI Prompts) instruct the tutor to teach
step-by-step, never give answers without explanation, admit uncertainty, avoid
exam-outcome predictions, stay encouraging, and respect BM/English. Server-side
guards: auth + role checks on every AI endpoint, CSRF, and rate-friendly history
windows.
