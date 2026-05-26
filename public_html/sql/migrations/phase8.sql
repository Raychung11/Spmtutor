-- =====================================================================
-- Phase 8 migration for EXISTING installs: landing leads / contact form.
-- =====================================================================
SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS leads (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  name        VARCHAR(150) NOT NULL,
  email       VARCHAR(190) NOT NULL,
  phone       VARCHAR(30) NULL,
  message     TEXT NULL,
  source      VARCHAR(60) NULL,
  status      ENUM('new','contacted','closed') NOT NULL DEFAULT 'new',
  created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_leads_status (status)
) ENGINE=InnoDB;
