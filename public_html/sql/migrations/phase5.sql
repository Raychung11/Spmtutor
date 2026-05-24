-- =====================================================================
-- Phase 5 migration for EXISTING installs.
-- =====================================================================
SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS rate_limits (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  rl_key       VARCHAR(190) NOT NULL,
  window_start DATETIME NOT NULL,
  hits         INT NOT NULL DEFAULT 0,
  UNIQUE KEY uq_rl_key (rl_key)
) ENGINE=InnoDB;
