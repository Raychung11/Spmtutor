-- =====================================================================
-- Phase 12 migration: Physics formulas table for visual learning cards.
-- =====================================================================
SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS physics_formulas (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  topic_id     INT NULL,
  topic_label  VARCHAR(120) NULL,
  formula_name VARCHAR(150) NOT NULL,
  formula      VARCHAR(255) NOT NULL,
  description  TEXT NULL,
  sort_order   INT NOT NULL DEFAULT 0,
  status       VARCHAR(20) NOT NULL DEFAULT 'active',
  created_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_pf_topic (topic_id)
) ENGINE=InnoDB;
