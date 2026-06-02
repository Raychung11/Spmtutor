-- =====================================================================
-- Phase 26 migration: PSV (Pendidikan Seni Visual) references bank.
--   - psv_references : artists, karya, teknik, istilah seni, bahan —
--                      untuk AI PSV Trainer dan flashcard apresiasi.
-- =====================================================================
SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS psv_references (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  topic_id      INT NULL,
  topic_label   VARCHAR(120) NULL,
  type          VARCHAR(30) NOT NULL DEFAULT 'istilah',
  name          VARCHAR(180) NOT NULL,
  description   TEXT NOT NULL,
  origin        VARCHAR(120) NULL,
  era           VARCHAR(80) NULL,
  example       TEXT NULL,
  category      VARCHAR(60) NULL,
  sort_order    INT NOT NULL DEFAULT 0,
  status        VARCHAR(20) NOT NULL DEFAULT 'active',
  created_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_psvr_topic (topic_id),
  INDEX idx_psvr_type (type),
  INDEX idx_psvr_category (category),
  UNIQUE KEY uq_psvr_name (name)
) ENGINE=InnoDB;
