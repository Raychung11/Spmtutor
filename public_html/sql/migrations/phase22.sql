-- =====================================================================
-- Phase 22 migration: Perniagaan terminology / istilah bank.
--   - perniagaan_terms : istilah perniagaan (pemilikan, organisasi,
--                        sumber manusia, pemasaran, kewangan,
--                        usahawan) untuk AI Perniagaan Trainer
--                        dan flashcard istilah-recall.
-- =====================================================================
SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS perniagaan_terms (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  topic_id      INT NULL,
  topic_label   VARCHAR(120) NULL,
  term          VARCHAR(180) NOT NULL,
  definition    TEXT NOT NULL,
  example       TEXT NULL,
  category      VARCHAR(60) NULL,
  sort_order    INT NOT NULL DEFAULT 0,
  status        VARCHAR(20) NOT NULL DEFAULT 'active',
  created_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_pt_topic (topic_id),
  INDEX idx_pt_category (category),
  UNIQUE KEY uq_pt_term (term)
) ENGINE=InnoDB;
