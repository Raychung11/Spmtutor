-- =====================================================================
-- Phase 24 migration: Sains Komputer concepts bank (istilah + code).
--   - cs_concepts : campuran istilah + pseudokod + code snippets
--                   (Python, SQL, HTML, CSS, JavaScript, pseudocode)
--                   untuk AI Sains Komputer Trainer dan flashcard
--                   konsep-recall + syntax-recall.
-- =====================================================================
SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS cs_concepts (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  topic_id      INT NULL,
  topic_label   VARCHAR(120) NULL,
  type          VARCHAR(30) NOT NULL DEFAULT 'istilah',
  name          VARCHAR(180) NOT NULL,
  definition    TEXT NOT NULL,
  code_snippet  MEDIUMTEXT NULL,
  language      VARCHAR(30) NULL,
  example       TEXT NULL,
  category      VARCHAR(60) NULL,
  sort_order    INT NOT NULL DEFAULT 0,
  status        VARCHAR(20) NOT NULL DEFAULT 'active',
  created_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_cs_topic (topic_id),
  INDEX idx_cs_type (type),
  INDEX idx_cs_lang (language),
  UNIQUE KEY uq_cs_name (name)
) ENGINE=InnoDB;
