-- =====================================================================
-- Phase 33 migration: Flashcard progress (spaced repetition).
--   - flashcard_progress : per-user/per-card progress with SM-2-style
--                          interval + ease_factor + next_due_at, so
--                          repeated flashcard sessions actually build
--                          long-term memory instead of resetting.
-- =====================================================================
SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS flashcard_progress (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  user_id       INT NOT NULL,
  library_slug  VARCHAR(60) NOT NULL,
  card_id       INT NOT NULL,
  status        VARCHAR(20) NOT NULL DEFAULT 'new',
  ease_factor   DECIMAL(4,2) NOT NULL DEFAULT 2.50,
  interval_days INT NOT NULL DEFAULT 1,
  review_count  INT NOT NULL DEFAULT 0,
  last_seen_at  DATETIME NULL,
  next_due_at   DATETIME NULL,
  created_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_fcp (user_id, library_slug, card_id),
  INDEX idx_fcp_due (user_id, next_due_at),
  INDEX idx_fcp_status (user_id, status)
) ENGINE=InnoDB;
