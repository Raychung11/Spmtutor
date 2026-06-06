-- =====================================================================
-- Phase 30 migration: Bahasa Arab references bank.
--   - ba_references : أمثال amthal (Arabic proverbs), nahu terms,
--                     mufradat (vocabulary), tokoh sasterawan dan
--                     karya klasik. Powers AI Bahasa Arab Trainer.
-- =====================================================================
SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS ba_references (
  id              INT AUTO_INCREMENT PRIMARY KEY,
  topic_id        INT NULL,
  topic_label     VARCHAR(120) NULL,
  type            VARCHAR(30) NOT NULL DEFAULT 'mufradat',
  expression      VARCHAR(255) NOT NULL,
  transliteration VARCHAR(255) NULL,
  meaning_bm      TEXT NOT NULL,
  meaning_ar      TEXT NULL,
  example         TEXT NULL,
  origin          VARCHAR(180) NULL,
  category        VARCHAR(60) NULL,
  sort_order      INT NOT NULL DEFAULT 0,
  status          VARCHAR(20) NOT NULL DEFAULT 'active',
  created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_bar_topic (topic_id),
  INDEX idx_bar_type (type),
  INDEX idx_bar_category (category),
  UNIQUE KEY uq_bar_expr (expression)
) ENGINE=InnoDB;
