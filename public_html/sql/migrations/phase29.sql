-- =====================================================================
-- Phase 29 migration: Bahasa Tamil references bank.
--   - bt_references : திருக்குறள் kural (Thirukkural couplets),
--                     பழமொழி pazhamozhi (Tamil proverbs), tokoh
--                     sasterawan, karya klasik dan moden. Powers
--                     AI Bahasa Tamil Trainer dan flashcard hafazan.
-- =====================================================================
SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS bt_references (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  topic_id      INT NULL,
  topic_label   VARCHAR(120) NULL,
  type          VARCHAR(30) NOT NULL DEFAULT 'kural',
  expression    VARCHAR(255) NOT NULL,
  transliteration VARCHAR(255) NULL,
  meaning_bm    TEXT NOT NULL,
  meaning_ta    TEXT NULL,
  example       TEXT NULL,
  origin        VARCHAR(180) NULL,
  category      VARCHAR(60) NULL,
  sort_order    INT NOT NULL DEFAULT 0,
  status        VARCHAR(20) NOT NULL DEFAULT 'active',
  created_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_btr_topic (topic_id),
  INDEX idx_btr_type (type),
  INDEX idx_btr_category (category),
  UNIQUE KEY uq_btr_expr (expression)
) ENGINE=InnoDB;
