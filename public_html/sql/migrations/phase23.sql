-- =====================================================================
-- Phase 23 migration: Ekonomi concepts (istilah + formula) bank.
--   - ekonomi_concepts : mixed istilah + formula bank
--                        (permintaan/penawaran, anjakan, elastisiti,
--                        IHP, kadar inflasi, KDNK, dll.) untuk AI
--                        Ekonomi Trainer dan flashcard konsep-recall.
-- =====================================================================
SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS ekonomi_concepts (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  topic_id      INT NULL,
  topic_label   VARCHAR(120) NULL,
  type          VARCHAR(30) NOT NULL DEFAULT 'istilah',
  name          VARCHAR(180) NOT NULL,
  definition    TEXT NOT NULL,
  formula       VARCHAR(255) NULL,
  example       TEXT NULL,
  category      VARCHAR(60) NULL,
  sort_order    INT NOT NULL DEFAULT 0,
  status        VARCHAR(20) NOT NULL DEFAULT 'active',
  created_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_ec_topic (topic_id),
  INDEX idx_ec_type (type),
  INDEX idx_ec_category (category),
  UNIQUE KEY uq_ec_name (name)
) ENGINE=InnoDB;
