-- =====================================================================
-- Phase 27 migration: Sains (integrated science) concepts bank.
--   - sains_concepts : campuran istilah, formula, konsep merentas
--                      biologi/kimia/fizik untuk AI Sains Trainer.
-- =====================================================================
SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS sains_concepts (
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
  INDEX idx_sc_topic (topic_id),
  INDEX idx_sc_type (type),
  INDEX idx_sc_category (category),
  UNIQUE KEY uq_sc_name (name)
) ENGINE=InnoDB;
