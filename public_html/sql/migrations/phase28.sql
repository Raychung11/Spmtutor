-- =====================================================================
-- Phase 28 migration: Bahasa Cina references (chengyu + classical words).
--   - bc_references : 成语 chengyu, simpulan bahasa Cina, wenyan
--                     function words, author profiles — untuk AI
--                     Bahasa Cina Trainer dan flashcard hafazan.
-- =====================================================================
SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS bc_references (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  topic_id      INT NULL,
  topic_label   VARCHAR(120) NULL,
  type          VARCHAR(30) NOT NULL DEFAULT 'chengyu',
  expression    VARCHAR(120) NOT NULL,
  pinyin        VARCHAR(180) NULL,
  meaning_bm    TEXT NOT NULL,
  meaning_zh    TEXT NULL,
  example       TEXT NULL,
  origin        VARCHAR(150) NULL,
  category      VARCHAR(60) NULL,
  sort_order    INT NOT NULL DEFAULT 0,
  status        VARCHAR(20) NOT NULL DEFAULT 'active',
  created_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_bcr_topic (topic_id),
  INDEX idx_bcr_type (type),
  INDEX idx_bcr_category (category),
  UNIQUE KEY uq_bcr_expr (expression)
) ENGINE=InnoDB;
