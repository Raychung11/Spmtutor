-- =====================================================================
-- Phase 25 migration: Reka Cipta (RBT SPM) concepts + design principles.
--   - rbt_concepts : prinsip reka bentuk, elemen reka bentuk, jenis
--                    lukisan, bahan, harta intelek — untuk AI RBT /
--                    Reka Cipta Trainer dan flashcard konsep.
-- =====================================================================
SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS rbt_concepts (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  topic_id      INT NULL,
  topic_label   VARCHAR(120) NULL,
  type          VARCHAR(30) NOT NULL DEFAULT 'istilah',
  name          VARCHAR(180) NOT NULL,
  definition    TEXT NOT NULL,
  example       TEXT NULL,
  category      VARCHAR(60) NULL,
  sort_order    INT NOT NULL DEFAULT 0,
  status        VARCHAR(20) NOT NULL DEFAULT 'active',
  created_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_rbtc_topic (topic_id),
  INDEX idx_rbtc_type (type),
  INDEX idx_rbtc_category (category),
  UNIQUE KEY uq_rbtc_name (name)
) ENGINE=InnoDB;
