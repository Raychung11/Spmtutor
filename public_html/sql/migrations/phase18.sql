-- =====================================================================
-- Phase 18 migration: Geografi locations reference bank.
--   - geografi_locations : geographical features (mountains, rivers,
--                          lakes, plates, climate zones, settlements)
--                          used by the AI Geografi Trainer and the
--                          map / location-recall flashcards.
-- =====================================================================
SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS geografi_locations (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  topic_id      INT NULL,
  topic_label   VARCHAR(120) NULL,
  category      VARCHAR(40) NOT NULL DEFAULT 'feature',
  name          VARCHAR(180) NOT NULL,
  region        VARCHAR(80) NULL,
  coordinates   VARCHAR(80) NULL,
  description   TEXT NULL,
  significance  TEXT NULL,
  sort_order    INT NOT NULL DEFAULT 0,
  status        VARCHAR(20) NOT NULL DEFAULT 'active',
  created_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_gl_topic (topic_id),
  INDEX idx_gl_cat (category),
  INDEX idx_gl_region (region),
  UNIQUE KEY uq_gl_name (name)
) ENGINE=InnoDB;
