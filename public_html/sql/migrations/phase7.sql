-- =====================================================================
-- Phase 7 migration for EXISTING installs: school invitations.
-- =====================================================================
SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS school_invitations (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  school_id   INT NOT NULL,
  email       VARCHAR(190) NOT NULL,
  member_role ENUM('teacher','student') NOT NULL,
  token       CHAR(64) NOT NULL,
  status      ENUM('pending','accepted','revoked') NOT NULL DEFAULT 'pending',
  invited_by  INT NULL,
  expires_at  DATETIME NULL,
  accepted_at DATETIME NULL,
  created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_invite_token (token),
  INDEX idx_invite_school (school_id),
  INDEX idx_invite_email (email),
  CONSTRAINT fk_invite_school FOREIGN KEY (school_id) REFERENCES schools(id) ON DELETE CASCADE
) ENGINE=InnoDB;
