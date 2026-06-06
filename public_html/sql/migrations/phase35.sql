-- =====================================================================
-- Phase 35 migration: AI Sandbox (LLM Compare).
--   - sandbox_runs : persists side-by-side prompt comparisons so students
--                    can revisit past experiments and see how models
--                    diverge. Powers student/sandbox.php (Asas
--                    Kepintaran Buatan elective practical tool).
-- =====================================================================
SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS sandbox_runs (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  user_id       INT NOT NULL,
  subject_id    INT NULL,
  system_prompt MEDIUMTEXT NULL,
  user_prompt   MEDIUMTEXT NOT NULL,
  temperature   DECIMAL(3,2) NOT NULL DEFAULT 0.40,
  provider_a    VARCHAR(40) NOT NULL,
  model_a       VARCHAR(100) NOT NULL,
  reply_a       MEDIUMTEXT NULL,
  ms_a          INT NULL,
  error_a       TEXT NULL,
  provider_b    VARCHAR(40) NOT NULL,
  model_b       VARCHAR(100) NOT NULL,
  reply_b       MEDIUMTEXT NULL,
  ms_b          INT NULL,
  error_b       TEXT NULL,
  vote          VARCHAR(10) NULL,
  notes         TEXT NULL,
  status        VARCHAR(20) NOT NULL DEFAULT 'active',
  created_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_sb_user (user_id, created_at),
  INDEX idx_sb_subject (subject_id)
) ENGINE=InnoDB;
