-- =====================================================================
-- Phase 14 migration: Biology-specific visual + recall modules.
--   - biology_diagrams   : labelled illustrations (Animal Cell, Heart …)
--   - biology_flashcards : Q/A recall cards for spaced practice
-- =====================================================================
SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS biology_diagrams (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  topic_id     INT NULL,
  topic_label  VARCHAR(120) NULL,
  title        VARCHAR(150) NOT NULL,
  description  TEXT NULL,
  image_url    VARCHAR(500) NULL,
  labels       TEXT NULL,
  sort_order   INT NOT NULL DEFAULT 0,
  status       VARCHAR(20) NOT NULL DEFAULT 'active',
  created_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_bd_topic (topic_id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS biology_flashcards (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  topic_id     INT NULL,
  topic_label  VARCHAR(120) NULL,
  question     TEXT NOT NULL,
  answer       TEXT NOT NULL,
  hint         TEXT NULL,
  difficulty   VARCHAR(20) NOT NULL DEFAULT 'medium',
  sort_order   INT NOT NULL DEFAULT 0,
  status       VARCHAR(20) NOT NULL DEFAULT 'active',
  created_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_bf_topic (topic_id)
) ENGINE=InnoDB;
