-- =====================================================================
-- Phase 6 migration for EXISTING installs: school registration portal.
-- =====================================================================
SET NAMES utf8mb4;

-- Add the school_admin role (preserves existing enum members).
ALTER TABLE users
  MODIFY role ENUM('student','parent','teacher','admin','creator','school_admin')
  NOT NULL DEFAULT 'student';

-- Add owner_user_id to schools if missing.
SET @col := (SELECT COUNT(*) FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'schools' AND COLUMN_NAME = 'owner_user_id');
SET @sql := IF(@col = 0, 'ALTER TABLE schools ADD COLUMN owner_user_id INT NULL AFTER type, ADD INDEX idx_school_owner (owner_user_id)', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

CREATE TABLE IF NOT EXISTS school_members (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  school_id   INT NOT NULL,
  user_id     INT NOT NULL,
  member_role ENUM('teacher','student') NOT NULL,
  status      VARCHAR(20) NOT NULL DEFAULT 'active',
  created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_school_member (school_id, user_id),
  INDEX idx_member_school (school_id),
  INDEX idx_member_user (user_id),
  CONSTRAINT fk_member_school FOREIGN KEY (school_id) REFERENCES schools(id) ON DELETE CASCADE,
  CONSTRAINT fk_member_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;
