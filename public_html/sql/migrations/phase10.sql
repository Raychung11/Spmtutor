-- =====================================================================
-- Phase 10 migration: AI usage logging for cost guardrails.
-- =====================================================================
SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS ai_usage_log (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  user_id     INT NULL,
  kind        VARCHAR(40) NOT NULL DEFAULT 'chat',
  created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_usage_user_day (user_id, created_at),
  INDEX idx_usage_day (created_at)
) ENGINE=InnoDB;
