-- =====================================================================
-- Phase 20 migration: Pendidikan Moral nilai utama reference bank.
--   - moral_values : 18 nilai utama KSSM (kepercayaan kepada Tuhan,
--                    baik hati, bertanggungjawab, hormat, kasih
--                    sayang, keadilan, dll.) used by the AI Moral
--                    Trainer and flashcard nilai-recall.
-- =====================================================================
SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS moral_values (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  topic_id      INT NULL,
  topic_label   VARCHAR(120) NULL,
  value_name    VARCHAR(120) NOT NULL,
  definition    TEXT NOT NULL,
  example       TEXT NULL,
  keywords      VARCHAR(255) NULL,
  category      VARCHAR(60) NULL,
  sort_order    INT NOT NULL DEFAULT 0,
  status        VARCHAR(20) NOT NULL DEFAULT 'active',
  created_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_mv_category (category),
  UNIQUE KEY uq_mv_name (value_name)
) ENGINE=InnoDB;
