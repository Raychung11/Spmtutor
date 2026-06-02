-- =====================================================================
-- Phase 31 migration: AI Karangan / Rumusan / Tatabahasa Marker.
--   - essay_submissions : student writing submissions + AI marking
--                         results (rubric, strengths, weaknesses,
--                         suggestions, errors). Powers the student
--                         Writing Marker page and teacher review.
-- =====================================================================
SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS essay_submissions (
  id              INT AUTO_INCREMENT PRIMARY KEY,
  user_id         INT NOT NULL,
  subject_id      INT NULL,
  task_type       VARCHAR(40) NOT NULL DEFAULT 'karangan',
  language        VARCHAR(20) NOT NULL DEFAULT 'bm',
  prompt          TEXT NULL,
  source_passage  MEDIUMTEXT NULL,
  submission      MEDIUMTEXT NOT NULL,
  word_count      INT NULL,
  score           INT NULL,
  max_score       INT NULL,
  band            VARCHAR(40) NULL,
  rubric_json     TEXT NULL,
  strengths_json  TEXT NULL,
  weaknesses_json TEXT NULL,
  suggestions_json TEXT NULL,
  errors_json     TEXT NULL,
  ai_raw          MEDIUMTEXT NULL,
  status          VARCHAR(20) NOT NULL DEFAULT 'pending',
  error_message   TEXT NULL,
  created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  marked_at       DATETIME NULL,
  INDEX idx_es_user (user_id),
  INDEX idx_es_subject (subject_id),
  INDEX idx_es_task (task_type),
  INDEX idx_es_status (status)
) ENGINE=InnoDB;
