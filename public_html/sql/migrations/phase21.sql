-- =====================================================================
-- Phase 21 migration: Prinsip Perakaunan formula / ratio reference bank.
--   - perakaunan_formulas : persamaan perakaunan, formula untung,
--                           nisbah kewangan, titik pulang modal,
--                           dll. Powers the AI Perakaunan Trainer
--                           and quick-reference card.
-- =====================================================================
SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS perakaunan_formulas (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  topic_id      INT NULL,
  topic_label   VARCHAR(120) NULL,
  formula_name  VARCHAR(180) NOT NULL,
  formula       VARCHAR(255) NOT NULL,
  description   TEXT NULL,
  category      VARCHAR(60) NULL,
  sort_order    INT NOT NULL DEFAULT 0,
  status        VARCHAR(20) NOT NULL DEFAULT 'active',
  created_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_pf_topic (topic_id),
  INDEX idx_pf_category (category),
  UNIQUE KEY uq_pf_name (formula_name)
) ENGINE=InnoDB;
