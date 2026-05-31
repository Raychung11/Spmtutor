-- =====================================================================
-- Phase 11 migration: form_level on topics + subtopic linkage on skills.
-- =====================================================================
SET NAMES utf8mb4;

-- Add topics.form_level (e.g. 4 or 5 for SPM Form 4 / Form 5).
SET @col := (SELECT COUNT(*) FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'topics' AND COLUMN_NAME = 'form_level');
SET @sql := IF(@col = 0,
    'ALTER TABLE topics ADD COLUMN form_level TINYINT NULL AFTER subject_id, ADD INDEX idx_topic_form (form_level)',
    'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Add skills.subtopic_id so skills can hang off a subtopic, not just a topic.
SET @col := (SELECT COUNT(*) FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'skills' AND COLUMN_NAME = 'subtopic_id');
SET @sql := IF(@col = 0,
    'ALTER TABLE skills ADD COLUMN subtopic_id INT NULL AFTER topic_id, ADD INDEX idx_skill_subtopic (subtopic_id)',
    'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
