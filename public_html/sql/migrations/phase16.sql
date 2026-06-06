-- =====================================================================
-- Phase 16 migration: English reference banks.
--   - english_vocabulary       : SPM-level word bank for the Vocabulary Coach
--   - english_idioms           : idioms + phrasal verbs reference
--   - english_writing_samples  : model essays the AI Essay Marker uses as exemplars
-- =====================================================================
SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS english_vocabulary (
  id             INT AUTO_INCREMENT PRIMARY KEY,
  word           VARCHAR(120) NOT NULL,
  part_of_speech VARCHAR(30)  NOT NULL DEFAULT 'noun',
  meaning        TEXT NOT NULL,
  example        TEXT NULL,
  synonyms       VARCHAR(255) NULL,
  antonyms       VARCHAR(255) NULL,
  level          VARCHAR(20)  NOT NULL DEFAULT 'intermediate',
  theme          VARCHAR(80)  NULL,
  sort_order     INT NOT NULL DEFAULT 0,
  status         VARCHAR(20)  NOT NULL DEFAULT 'active',
  created_at     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_ev_word (word),
  INDEX idx_ev_level (level),
  INDEX idx_ev_theme (theme)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS english_idioms (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  type         VARCHAR(30)  NOT NULL DEFAULT 'idiom',
  expression   VARCHAR(255) NOT NULL,
  meaning      TEXT NOT NULL,
  example      TEXT NULL,
  theme        VARCHAR(80)  NULL,
  sort_order   INT NOT NULL DEFAULT 0,
  status       VARCHAR(20)  NOT NULL DEFAULT 'active',
  created_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_ei_expr (expression),
  INDEX idx_ei_type (type),
  INDEX idx_ei_theme (theme)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS english_writing_samples (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  topic_id     INT NULL,
  topic_label  VARCHAR(120) NULL,
  category     VARCHAR(40)  NOT NULL DEFAULT 'essay',
  prompt       TEXT NOT NULL,
  model_essay  MEDIUMTEXT NOT NULL,
  band         VARCHAR(20)  NULL,
  notes        TEXT NULL,
  sort_order   INT NOT NULL DEFAULT 0,
  status       VARCHAR(20)  NOT NULL DEFAULT 'active',
  created_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_ews_topic (topic_id),
  INDEX idx_ews_cat (category)
) ENGINE=InnoDB;
