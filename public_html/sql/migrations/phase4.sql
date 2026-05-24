-- =====================================================================
-- Phase 4 migration for EXISTING installs (schema.sql already covers
-- fresh installs). Safe to run once on a database created before Phase 4.
-- =====================================================================
SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS schools (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  name        VARCHAR(190) NOT NULL,
  type        ENUM('school','learning_center') NOT NULL DEFAULT 'learning_center',
  contact_email VARCHAR(190) NULL,
  phone       VARCHAR(30) NULL,
  status      VARCHAR(20) NOT NULL DEFAULT 'active',
  created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS api_tokens (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  user_id      INT NOT NULL,
  token_hash   CHAR(64) NOT NULL,
  name         VARCHAR(80) NULL,
  last_used_at DATETIME NULL,
  expires_at   DATETIME NULL,
  created_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_token (token_hash),
  INDEX idx_token_user (user_id),
  CONSTRAINT fk_token_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Add school_id to teacher_classes if it does not already exist.
SET @col := (SELECT COUNT(*) FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'teacher_classes' AND COLUMN_NAME = 'school_id');
SET @sql := IF(@col = 0, 'ALTER TABLE teacher_classes ADD COLUMN school_id INT NULL AFTER teacher_user_id', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
