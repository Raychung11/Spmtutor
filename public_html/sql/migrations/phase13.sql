-- =====================================================================
-- Phase 13 migration: Chemistry formulas table for visual learning cards.
-- =====================================================================
SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS chemistry_formulas (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  topic_id     INT NULL,
  topic_label  VARCHAR(120) NULL,
  formula_name VARCHAR(150) NOT NULL,
  formula      VARCHAR(255) NOT NULL,
  description  TEXT NULL,
  sort_order   INT NOT NULL DEFAULT 0,
  status       VARCHAR(20) NOT NULL DEFAULT 'active',
  created_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_cf_topic (topic_id)
) ENGINE=InnoDB;
