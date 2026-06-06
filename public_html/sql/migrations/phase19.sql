-- =====================================================================
-- Phase 19 migration: Pendidikan Islam ayat / hadis / doa bank.
--   - islam_ayat_hadis : Quranic verses + hadith + doa with Arabic
--                        text, transliteration, translation and theme.
--                        Powers the AI Pendidikan Islam Trainer +
--                        memorisation / tafsir flashcards.
-- =====================================================================
SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS islam_ayat_hadis (
  id              INT AUTO_INCREMENT PRIMARY KEY,
  topic_id        INT NULL,
  topic_label     VARCHAR(120) NULL,
  type            VARCHAR(30) NOT NULL DEFAULT 'ayat',
  reference       VARCHAR(180) NOT NULL,
  arabic_text     TEXT NULL,
  transliteration TEXT NULL,
  translation     TEXT NOT NULL,
  theme           VARCHAR(120) NULL,
  explanation     TEXT NULL,
  sort_order      INT NOT NULL DEFAULT 0,
  status          VARCHAR(20) NOT NULL DEFAULT 'active',
  created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_iah_topic (topic_id),
  INDEX idx_iah_type (type),
  INDEX idx_iah_theme (theme),
  UNIQUE KEY uq_iah_ref (reference)
) ENGINE=InnoDB;
