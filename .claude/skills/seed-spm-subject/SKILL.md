---
name: seed-spm-subject
description: Research a KSSM SPM subject's syllabus and seed it into LulusAI. Use when the user wants to add or extend an SPM subject (e.g. "/seed-spm-subject sejarah", "/seed-spm-subject geografi", "seed Pendidikan Islam"). Produces a migration (if subject-specific tables are needed), a cron seeder, an Admin → Seeders button, and a schema.sql update — following the exact pattern of the existing subjects.
allowed-tools: Read, Edit, Write, Bash, WebSearch, WebFetch, Agent
---

# Seed an SPM subject into LulusAI

You're extending the LulusAI codebase (native PHP 8 + MySQL on Hostinger). Every SPM subject so far has followed the same pattern. Stay inside that pattern — don't invent new conventions.

## Inputs

The user supplies a subject (slug, name, or full catalog). Examples:
- `/seed-spm-subject sejarah`
- `/seed-spm-subject geografi`
- `seed Pendidikan Islam`
- A pasted catalog block (chapters + subtopics + skills) — in this case skip research, go straight to building.

If the user gave only a name, **research the current KSSM SPM syllabus for that subject** via WebSearch (use 1-2 focused queries, e.g. `KSSM SPM <subject> Form 4 Form 5 syllabus DSKP topics 2025`). Use Malaysian official sources when possible (moe.gov.my, asiemodel.net DSKP PDFs, lp.moe.gov.my).

## Workflow

1. **Confirm subject exists**. Check `public_html/cron/seed_subjects.php` and `public_html/inc/ai_questions.php` (subject_default_traits) for the correct slug. If missing, add it to both files (don't seed against a missing subject row).

2. **Research the syllabus** if not provided. Aim for 10–22 chapters total split across Form 4 and Form 5. Capture subtopics + 2–4 starter skills per chapter (action verbs: "Analyse…", "Compare…", "Explain…").

3. **Present the proposed catalog to the user** in the SAME format the user uses (Chapter N — Name, Subtopics: …, Skills: …). Include a "Special data tables I'd propose" section if the subject benefits from a reference bank (formulas, peribahasa, vocabulary, diagrams, flashcards, timeline, maps, etc.). Wait for confirmation before building unless the user already gave explicit "proceed" approval.

4. **Build the migration** (`public_html/sql/migrations/phaseN.sql`) only if you need a subject-specific reference table. Use `N` = max existing phaseN + 1. Always include `SET NAMES utf8mb4;` and use `CREATE TABLE IF NOT EXISTS`. Add a UNIQUE key on the natural identifier (expression / word / title) so the seeder is idempotent. Don't touch `subjects` / `topics` / `subtopics` / `skills` schemas — those are already in place via earlier phases.

5. **Build the seeder** (`public_html/cron/seed_<slug>_kssm.php`). Follow `seed_chemistry_kssm.php` / `seed_biology_kssm.php` / `seed_english_kssm.php` as templates. The function MUST:
   - Be named `<slug>_kssm_run()` (snake_case, no hyphens; e.g. `sejarah_kssm_run`).
   - Look up the subject by slug; return `['status'=>'no_subject', 'message'=>…]` if missing.
   - Match topics by `(subject_id, name, form_level)` with the `<=>` NULL-safe equality.
   - Adopt legacy NULL-form topics with the same name.
   - Use `db_one` / `db_all` / `db_exec` — never raw PDO.
   - Sort order: `($form === 4 ? 0 : 100) + $i`.
   - Reference-bank inserts must check `information_schema.TABLES` first and skip gracefully if the table is missing (so the seeder still works on an unmigrated DB).
   - Have a `PHP_SAPI === 'cli'` guard at the bottom that prints a summary.

6. **Wire into `public_html/admin/seeders.php`**:
   - Add a `case '<slug>_kssm':` block in the switch (mirror the chemistry/biology/english blocks).
   - Add an entry in the `$seeders = […]` array with a short, accurate description.

7. **Update `public_html/sql/schema.sql`** — append the new tables (if any) next to the other subject-specific reference tables (chemistry_formulas, biology_flashcards, etc.).

8. **Lint** every changed PHP file with `php -l`. Fix any errors.

9. **Commit and push** to the current branch (`claude/skilltutorai-education-platform-3s2SC`). Use this commit message template:

   ```
   Seed the full KSSM SPM <Subject> syllabus + <reference bank if any>

   <one-paragraph summary of what was added>

   - sql/migrations/phaseN.sql: …
   - cron/seed_<slug>_kssm.php: <N> chapters (F4: x, F5: y) …
   - admin/seeders.php: one-click "<Subject>" card.
   - sql/schema.sql: …

   https://claude.ai/code/session_01JyDEu4yEU4SZcEY5UHPpXT
   ```

   Push with `git push -u origin claude/skilltutorai-education-platform-3s2SC`. PR #1 will pick up the commit automatically.

10. **Report counts** to the user (topics, subtopics, skills, reference-bank items) and tell them the two deploy steps: (a) Run database migrations from Admin → Seeders, (b) run the new "KSSM <Subject>" button.

## Hard rules

- Never write to a different branch than `claude/skilltutorai-education-platform-3s2SC` unless the user explicitly redirects.
- Never include the model identifier in commit messages, code, or PR bodies — chat replies only.
- Never use `git add -A`. Stage files by path.
- Never amend existing commits.
- Never include emojis unless the user has used them first in this session.
- Stay inside the existing UI/folder structure — don't introduce new top-level directories.

## Useful file pointers

- Existing seeders (templates): `public_html/cron/seed_math_kssm.php`, `seed_addmath_kssm.php`, `seed_physics_kssm.php`, `seed_chemistry_kssm.php`, `seed_biology_kssm.php`, `seed_bm_kssm.php`, `seed_english_kssm.php`
- Subjects catalog: `public_html/cron/seed_subjects.php`
- AI prompt traits: `public_html/inc/ai_questions.php` (`subject_default_traits` switch)
- Seeders UI: `public_html/admin/seeders.php`
- Schema: `public_html/sql/schema.sql`
- Migrations dir: `public_html/sql/migrations/`
- Highest existing migration: `phase16.sql` (English vocab + idioms + writing samples)
