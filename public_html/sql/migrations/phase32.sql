-- =====================================================================
-- Phase 32 migration: Teacher review of essay submissions.
--   Adds teacher review fields onto essay_submissions so a teacher
--   can override the AI score, add a comment, and mark the
--   submission as reviewed without a separate join table.
-- =====================================================================
SET NAMES utf8mb4;

SET @col := (SELECT COUNT(*) FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'essay_submissions' AND COLUMN_NAME = 'teacher_user_id');
SET @sql := IF(@col = 0,
    'ALTER TABLE essay_submissions
       ADD COLUMN teacher_user_id      INT NULL AFTER status,
       ADD COLUMN teacher_score_override INT NULL,
       ADD COLUMN teacher_comment      TEXT NULL,
       ADD COLUMN reviewed_at          DATETIME NULL,
       ADD INDEX idx_es_reviewed (reviewed_at),
       ADD INDEX idx_es_teacher (teacher_user_id)',
    'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
