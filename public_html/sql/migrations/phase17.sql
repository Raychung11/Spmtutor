-- =====================================================================
-- Phase 17 migration: Sejarah timeline reference bank.
--   - sejarah_timeline : tarikh-tarikh penting + peristiwa untuk
--                        AI Sejarah Trainer dan flashcard tarikh.
-- =====================================================================
SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS sejarah_timeline (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  topic_id     INT NULL,
  topic_label  VARCHAR(120) NULL,
  era          VARCHAR(60) NULL,
  year_label   VARCHAR(40) NOT NULL,
  event_date   VARCHAR(60) NULL,
  title        VARCHAR(200) NOT NULL,
  description  TEXT NULL,
  importance   VARCHAR(20) NOT NULL DEFAULT 'medium',
  sort_order   INT NOT NULL DEFAULT 0,
  status       VARCHAR(20) NOT NULL DEFAULT 'active',
  created_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_st_topic (topic_id),
  INDEX idx_st_era (era),
  UNIQUE KEY uq_st_title (title)
) ENGINE=InnoDB;
