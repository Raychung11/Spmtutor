-- =====================================================================
-- Phase 15 migration: Bahasa Melayu reference banks.
--   - bm_peribahasa : peribahasa / simpulan bahasa / bidalan / pepatah /
--                     cogan kata, used by the AI Tatabahasa Trainer and
--                     karangan suggestions.
-- =====================================================================
SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS bm_peribahasa (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  type         VARCHAR(30) NOT NULL DEFAULT 'peribahasa',
  expression   VARCHAR(255) NOT NULL,
  meaning      TEXT NOT NULL,
  example      TEXT NULL,
  theme        VARCHAR(80) NULL,
  sort_order   INT NOT NULL DEFAULT 0,
  status       VARCHAR(20) NOT NULL DEFAULT 'active',
  created_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_bmp_type (type),
  INDEX idx_bmp_theme (theme),
  UNIQUE KEY uq_bmp_expr (expression)
) ENGINE=InnoDB;
