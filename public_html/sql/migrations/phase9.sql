-- =====================================================================
-- Phase 9 migration: per-subject AI generation controls.
-- Adds editable structured fields + a composed/freeform prompt per
-- subject so question generation stays within syllabus.
-- =====================================================================
SET NAMES utf8mb4;

SET @col := (SELECT COUNT(*) FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'subjects' AND COLUMN_NAME = 'ai_prompt');
SET @sql := IF(@col = 0,
    'ALTER TABLE subjects
       ADD COLUMN ai_prompt TEXT NULL AFTER description,
       ADD COLUMN ai_subject_type VARCHAR(30) NULL,
       ADD COLUMN ai_exam_board VARCHAR(60) NULL,
       ADD COLUMN ai_language VARCHAR(20) NULL,
       ADD COLUMN ai_notes TEXT NULL',
    'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
