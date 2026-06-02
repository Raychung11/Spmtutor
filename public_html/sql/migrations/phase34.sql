-- =====================================================================
-- Phase 34 migration: Asas Kepintaran Buatan (AI Foundations) ref tables.
--   - ai_concepts : istilah AI/ML, code snippets, prompt patterns.
--                   Powers Library + Flashcards + AI Trainer.
--   - ai_timeline : peristiwa penting sejarah AI (1956 Dartmouth →
--                   2025 reasoning models). Powers Library +
--                   chronological timeline view.
-- =====================================================================
SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS ai_concepts (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  topic_id      INT NULL,
  topic_label   VARCHAR(120) NULL,
  type          VARCHAR(30) NOT NULL DEFAULT 'istilah',
  name          VARCHAR(180) NOT NULL,
  definition    TEXT NOT NULL,
  code_snippet  MEDIUMTEXT NULL,
  language      VARCHAR(30) NULL,
  example       TEXT NULL,
  category      VARCHAR(60) NULL,
  year_invented VARCHAR(20) NULL,
  sort_order    INT NOT NULL DEFAULT 0,
  status        VARCHAR(20) NOT NULL DEFAULT 'active',
  created_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_ac_topic (topic_id),
  INDEX idx_ac_type (type),
  INDEX idx_ac_category (category),
  UNIQUE KEY uq_ac_name (name)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS ai_timeline (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  topic_id     INT NULL,
  topic_label  VARCHAR(120) NULL,
  era          VARCHAR(60) NULL,
  year_label   VARCHAR(40) NOT NULL,
  event_date   VARCHAR(60) NULL,
  title        VARCHAR(200) NOT NULL,
  description  TEXT NULL,
  importance   VARCHAR(20) NOT NULL DEFAULT 'medium',
  sort_order   INT NOT NULL DEFAULT 0,
  status       VARCHAR(20) NOT NULL DEFAULT 'active',
  created_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_ait_topic (topic_id),
  INDEX idx_ait_era (era),
  UNIQUE KEY uq_ait_title (title)
) ENGINE=InnoDB;
