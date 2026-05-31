-- =====================================================================
-- LulusAI -- Education OS
-- Full MySQL schema (all phases). Engine: InnoDB, charset utf8mb4.
-- =====================================================================
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- =====================================================================
-- MODULE 1: Authentication & User Management
-- =====================================================================
CREATE TABLE IF NOT EXISTS users (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  name          VARCHAR(150) NOT NULL,
  email         VARCHAR(190) NOT NULL UNIQUE,
  phone         VARCHAR(30)  NULL,
  password_hash VARCHAR(255) NOT NULL,
  role          ENUM('student','parent','teacher','admin','creator','school_admin') NOT NULL DEFAULT 'student',
  status        ENUM('active','suspended','pending') NOT NULL DEFAULT 'active',
  remember_token VARCHAR(64) NULL,
  last_login_at DATETIME NULL,
  created_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_users_role (role),
  INDEX idx_users_status (status)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS student_profiles (
  id              INT AUTO_INCREMENT PRIMARY KEY,
  user_id         INT NOT NULL,
  education_level_id INT NULL,
  school          VARCHAR(190) NULL,
  date_of_birth   DATE NULL,
  exam_target_date DATE NULL,
  xp              INT NOT NULL DEFAULT 0,
  level           INT NOT NULL DEFAULT 1,
  created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_student_user (user_id),
  INDEX idx_student_level (education_level_id),
  CONSTRAINT fk_student_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS parent_profiles (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  user_id     INT NOT NULL,
  created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_parent_user (user_id),
  CONSTRAINT fk_parent_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS teacher_profiles (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  user_id     INT NOT NULL,
  bio         TEXT NULL,
  subjects    VARCHAR(255) NULL,
  created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_teacher_user (user_id),
  CONSTRAINT fk_teacher_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS parent_student_links (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  parent_user_id  INT NOT NULL,
  student_user_id INT NOT NULL,
  relationship VARCHAR(50) NULL,
  status      ENUM('active','pending','revoked') NOT NULL DEFAULT 'active',
  created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_parent_student (parent_user_id, student_user_id),
  INDEX idx_link_student (student_user_id),
  CONSTRAINT fk_link_parent  FOREIGN KEY (parent_user_id)  REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_link_student FOREIGN KEY (student_user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS login_logs (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  user_id     INT NULL,
  email       VARCHAR(190) NULL,
  ip_address  VARCHAR(45) NULL,
  user_agent  VARCHAR(255) NULL,
  result      ENUM('success','failed') NOT NULL,
  created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_login_user (user_id),
  INDEX idx_login_result (result)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS password_resets (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  email       VARCHAR(190) NOT NULL,
  token       VARCHAR(64) NOT NULL,
  expires_at  DATETIME NOT NULL,
  created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_reset_email (email),
  INDEX idx_reset_token (token)
) ENGINE=InnoDB;

-- =====================================================================
-- MODULE 2: Subject & Skill Management
-- =====================================================================
CREATE TABLE IF NOT EXISTS education_levels (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  name        VARCHAR(120) NOT NULL,
  slug        VARCHAR(120) NOT NULL UNIQUE,
  sort_order  INT NOT NULL DEFAULT 0,
  status      VARCHAR(20) NOT NULL DEFAULT 'active',
  created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS subjects (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  education_level_id INT NULL,
  name        VARCHAR(150) NOT NULL,
  slug        VARCHAR(150) NOT NULL,
  icon        VARCHAR(80) NULL,
  description TEXT NULL,
  ai_prompt   TEXT NULL,
  ai_subject_type VARCHAR(30) NULL,
  ai_exam_board   VARCHAR(60) NULL,
  ai_language     VARCHAR(20) NULL,
  ai_notes        TEXT NULL,
  sort_order  INT NOT NULL DEFAULT 0,
  status      VARCHAR(20) NOT NULL DEFAULT 'active',
  created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_subject_level (education_level_id),
  CONSTRAINT fk_subject_level FOREIGN KEY (education_level_id) REFERENCES education_levels(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS topics (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  subject_id  INT NOT NULL,
  form_level  TINYINT NULL,
  name        VARCHAR(190) NOT NULL,
  slug        VARCHAR(190) NOT NULL,
  description TEXT NULL,
  sort_order  INT NOT NULL DEFAULT 0,
  status      VARCHAR(20) NOT NULL DEFAULT 'active',
  created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_topic_subject (subject_id),
  INDEX idx_topic_form (form_level),
  CONSTRAINT fk_topic_subject FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS subtopics (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  topic_id    INT NOT NULL,
  name        VARCHAR(190) NOT NULL,
  sort_order  INT NOT NULL DEFAULT 0,
  status      VARCHAR(20) NOT NULL DEFAULT 'active',
  created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_subtopic_topic (topic_id),
  CONSTRAINT fk_subtopic_topic FOREIGN KEY (topic_id) REFERENCES topics(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS skills (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  topic_id    INT NOT NULL,
  subtopic_id INT NULL,
  name        VARCHAR(190) NOT NULL,
  difficulty  ENUM('easy','medium','hard') NOT NULL DEFAULT 'medium',
  description TEXT NULL,
  status      VARCHAR(20) NOT NULL DEFAULT 'active',
  created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_skill_topic (topic_id),
  INDEX idx_skill_subtopic (subtopic_id),
  CONSTRAINT fk_skill_topic FOREIGN KEY (topic_id) REFERENCES topics(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS learning_outcomes (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  skill_id    INT NOT NULL,
  outcome     VARCHAR(255) NOT NULL,
  created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_outcome_skill (skill_id),
  CONSTRAINT fk_outcome_skill FOREIGN KEY (skill_id) REFERENCES skills(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- =====================================================================
-- MODULE 3: AI Tutor Chat
-- =====================================================================
CREATE TABLE IF NOT EXISTS ai_prompt_templates (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  code        VARCHAR(60) NOT NULL UNIQUE,   -- tutor, diagnostic, marking, parent_report, learning_path, weakness
  name        VARCHAR(150) NOT NULL,
  system_prompt TEXT NOT NULL,
  model       VARCHAR(80) NULL,
  temperature DECIMAL(3,2) NOT NULL DEFAULT 0.40,
  status      VARCHAR(20) NOT NULL DEFAULT 'active',
  created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS ai_chat_sessions (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  user_id     INT NOT NULL,
  subject_id  INT NULL,
  topic_id    INT NULL,
  title       VARCHAR(190) NULL,
  status      VARCHAR(20) NOT NULL DEFAULT 'active',
  created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_chat_user (user_id),
  INDEX idx_chat_subject (subject_id),
  CONSTRAINT fk_chat_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS ai_chat_messages (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  session_id  INT NOT NULL,
  role        ENUM('user','assistant','system') NOT NULL,
  content     MEDIUMTEXT NOT NULL,
  tokens      INT NULL,
  created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_msg_session (session_id),
  CONSTRAINT fk_msg_session FOREIGN KEY (session_id) REFERENCES ai_chat_sessions(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS ai_feedback (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  message_id  INT NOT NULL,
  user_id     INT NOT NULL,
  rating      TINYINT NOT NULL,   -- 1 thumbs up, -1 thumbs down
  comment     VARCHAR(255) NULL,
  created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_feedback_msg (message_id),
  CONSTRAINT fk_feedback_msg FOREIGN KEY (message_id) REFERENCES ai_chat_messages(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- =====================================================================
-- MODULE 4: Diagnostic Engine
-- =====================================================================
CREATE TABLE IF NOT EXISTS diagnostic_tests (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  subject_id  INT NOT NULL,
  title       VARCHAR(190) NOT NULL,
  description TEXT NULL,
  status      VARCHAR(20) NOT NULL DEFAULT 'active',
  created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_diag_subject (subject_id),
  CONSTRAINT fk_diag_subject FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS diagnostic_attempts (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  test_id     INT NOT NULL,
  user_id     INT NOT NULL,
  score       DECIMAL(5,2) NULL,
  status      ENUM('in_progress','completed') NOT NULL DEFAULT 'in_progress',
  started_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  completed_at DATETIME NULL,
  INDEX idx_diag_attempt_user (user_id),
  CONSTRAINT fk_diag_attempt_test FOREIGN KEY (test_id) REFERENCES diagnostic_tests(id) ON DELETE CASCADE,
  CONSTRAINT fk_diag_attempt_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS diagnostic_answers (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  attempt_id  INT NOT NULL,
  question_id INT NOT NULL,
  answer_text TEXT NULL,
  is_correct  TINYINT(1) NULL,
  created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_diag_ans_attempt (attempt_id),
  CONSTRAINT fk_diag_ans_attempt FOREIGN KEY (attempt_id) REFERENCES diagnostic_attempts(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS diagnostic_results (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  attempt_id  INT NOT NULL,
  strong_topics  TEXT NULL,
  weak_topics    TEXT NULL,
  recommendation TEXT NULL,
  created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_diag_result_attempt (attempt_id),
  CONSTRAINT fk_diag_result_attempt FOREIGN KEY (attempt_id) REFERENCES diagnostic_attempts(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS student_skill_scores (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  user_id     INT NOT NULL,
  skill_id    INT NOT NULL,
  score       DECIMAL(5,2) NOT NULL DEFAULT 0,
  attempts    INT NOT NULL DEFAULT 0,
  updated_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_user_skill (user_id, skill_id),
  INDEX idx_skillscore_user (user_id),
  CONSTRAINT fk_skillscore_user  FOREIGN KEY (user_id)  REFERENCES users(id)  ON DELETE CASCADE,
  CONSTRAINT fk_skillscore_skill FOREIGN KEY (skill_id) REFERENCES skills(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- =====================================================================
-- MODULE 5: Practice Question Bank
-- =====================================================================
CREATE TABLE IF NOT EXISTS questions (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  subject_id  INT NOT NULL,
  topic_id    INT NULL,
  skill_id    INT NULL,
  type        ENUM('mcq','short','essay','calculation','structured','image') NOT NULL DEFAULT 'mcq',
  difficulty  ENUM('easy','medium','hard') NOT NULL DEFAULT 'medium',
  question_text TEXT NOT NULL,
  image_path  VARCHAR(255) NULL,
  explanation TEXT NULL,
  exam_format VARCHAR(80) NULL,
  marks       INT NOT NULL DEFAULT 1,
  created_by  INT NULL,
  status      ENUM('active','draft','pending') NOT NULL DEFAULT 'active',
  created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_q_subject (subject_id),
  INDEX idx_q_topic (topic_id),
  INDEX idx_q_skill (skill_id),
  CONSTRAINT fk_q_subject FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS question_options (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  question_id INT NOT NULL,
  label       VARCHAR(5) NULL,
  option_text TEXT NOT NULL,
  is_correct  TINYINT(1) NOT NULL DEFAULT 0,
  sort_order  INT NOT NULL DEFAULT 0,
  INDEX idx_opt_question (question_id),
  CONSTRAINT fk_opt_question FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS question_answers (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  question_id INT NOT NULL,
  answer_text TEXT NOT NULL,
  INDEX idx_ans_question (question_id),
  CONSTRAINT fk_ans_question FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS question_marking_schemes (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  question_id INT NOT NULL,
  scheme_text TEXT NOT NULL,
  INDEX idx_scheme_question (question_id),
  CONSTRAINT fk_scheme_question FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS question_attempts (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  user_id     INT NOT NULL,
  question_id INT NOT NULL,
  answer_text TEXT NULL,
  selected_option_id INT NULL,
  is_correct  TINYINT(1) NULL,
  score       DECIMAL(5,2) NULL,
  time_spent  INT NULL,
  created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_qa_user (user_id),
  INDEX idx_qa_question (question_id),
  CONSTRAINT fk_qa_user     FOREIGN KEY (user_id)     REFERENCES users(id)     ON DELETE CASCADE,
  CONSTRAINT fk_qa_question FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- =====================================================================
-- MODULE 6: Snap & Check / AI Marking
-- =====================================================================
CREATE TABLE IF NOT EXISTS answer_uploads (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  user_id     INT NOT NULL,
  question_id INT NULL,
  file_path   VARCHAR(255) NOT NULL,
  ocr_text    MEDIUMTEXT NULL,
  status      VARCHAR(20) NOT NULL DEFAULT 'uploaded',
  created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_upload_user (user_id),
  CONSTRAINT fk_upload_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS ai_marking_results (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  upload_id   INT NOT NULL,
  score       DECIMAL(5,2) NULL,
  correct_parts TEXT NULL,
  mistakes    TEXT NULL,
  correction  TEXT NULL,
  topic_weakness VARCHAR(255) NULL,
  next_recommendation TEXT NULL,
  confidence  DECIMAL(4,3) NULL,
  created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_marking_upload (upload_id),
  CONSTRAINT fk_marking_upload FOREIGN KEY (upload_id) REFERENCES answer_uploads(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS teacher_review_results (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  upload_id   INT NOT NULL,
  teacher_user_id INT NOT NULL,
  override_score DECIMAL(5,2) NULL,
  comment     TEXT NULL,
  created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_review_upload (upload_id),
  CONSTRAINT fk_review_upload FOREIGN KEY (upload_id) REFERENCES answer_uploads(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- =====================================================================
-- MODULE 7: Learning Path Engine
-- =====================================================================
CREATE TABLE IF NOT EXISTS learning_paths (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  user_id     INT NOT NULL,
  subject_id  INT NULL,
  title       VARCHAR(190) NOT NULL,
  target_date DATE NULL,
  status      VARCHAR(20) NOT NULL DEFAULT 'active',
  created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_path_user (user_id),
  CONSTRAINT fk_path_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS learning_path_items (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  path_id     INT NOT NULL,
  topic_id    INT NULL,
  skill_id    INT NULL,
  title       VARCHAR(190) NOT NULL,
  sort_order  INT NOT NULL DEFAULT 0,
  status      ENUM('pending','in_progress','done') NOT NULL DEFAULT 'pending',
  INDEX idx_pathitem_path (path_id),
  CONSTRAINT fk_pathitem_path FOREIGN KEY (path_id) REFERENCES learning_paths(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS student_learning_progress (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  user_id     INT NOT NULL,
  topic_id    INT NOT NULL,
  mastery     DECIMAL(5,2) NOT NULL DEFAULT 0,
  updated_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_progress_user_topic (user_id, topic_id),
  INDEX idx_progress_user (user_id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS daily_missions (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  user_id     INT NOT NULL,
  mission_date DATE NOT NULL,
  description VARCHAR(255) NOT NULL,
  target      INT NOT NULL DEFAULT 1,
  progress    INT NOT NULL DEFAULT 0,
  completed   TINYINT(1) NOT NULL DEFAULT 0,
  UNIQUE KEY uq_mission_user_date (user_id, mission_date),
  INDEX idx_mission_user (user_id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS weekly_goals (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  user_id     INT NOT NULL,
  week_start  DATE NOT NULL,
  description VARCHAR(255) NOT NULL,
  target      INT NOT NULL DEFAULT 1,
  progress    INT NOT NULL DEFAULT 0,
  INDEX idx_goal_user (user_id)
) ENGINE=InnoDB;

-- =====================================================================
-- MODULE 8: Progress Tracking
-- =====================================================================
CREATE TABLE IF NOT EXISTS student_progress_summary (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  user_id     INT NOT NULL,
  total_questions INT NOT NULL DEFAULT 0,
  total_correct   INT NOT NULL DEFAULT 0,
  avg_score   DECIMAL(5,2) NOT NULL DEFAULT 0,
  updated_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_summary_user (user_id),
  CONSTRAINT fk_summary_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS student_subject_stats (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  user_id     INT NOT NULL,
  subject_id  INT NOT NULL,
  total_attempts INT NOT NULL DEFAULT 0,
  total_correct  INT NOT NULL DEFAULT 0,
  avg_score   DECIMAL(5,2) NOT NULL DEFAULT 0,
  updated_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_substat (user_id, subject_id),
  INDEX idx_substat_user (user_id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS student_topic_stats (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  user_id     INT NOT NULL,
  topic_id    INT NOT NULL,
  total_attempts INT NOT NULL DEFAULT 0,
  total_correct  INT NOT NULL DEFAULT 0,
  mastery     DECIMAL(5,2) NOT NULL DEFAULT 0,
  updated_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_topicstat (user_id, topic_id),
  INDEX idx_topicstat_user (user_id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS student_streaks (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  user_id     INT NOT NULL,
  current_streak INT NOT NULL DEFAULT 0,
  longest_streak INT NOT NULL DEFAULT 0,
  last_active_date DATE NULL,
  updated_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_streak_user (user_id),
  CONSTRAINT fk_streak_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- =====================================================================
-- MODULE 9: Parent reports / notifications
-- =====================================================================
CREATE TABLE IF NOT EXISTS parent_reports (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  parent_user_id  INT NOT NULL,
  student_user_id INT NOT NULL,
  content     TEXT NOT NULL,
  created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_preport_parent (parent_user_id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS weekly_ai_reports (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  student_user_id INT NOT NULL,
  week_start  DATE NOT NULL,
  summary     TEXT NOT NULL,
  recommendation TEXT NULL,
  created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_wreport_student (student_user_id)
) ENGINE=InnoDB;

-- =====================================================================
-- MODULE 10: Teacher / Class management
-- =====================================================================
CREATE TABLE IF NOT EXISTS teacher_classes (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  teacher_user_id INT NOT NULL,
  school_id   INT NULL,
  name        VARCHAR(150) NOT NULL,
  subject_id  INT NULL,
  created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_class_teacher (teacher_user_id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS class_students (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  class_id    INT NOT NULL,
  student_user_id INT NOT NULL,
  created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_class_student (class_id, student_user_id),
  CONSTRAINT fk_cs_class FOREIGN KEY (class_id) REFERENCES teacher_classes(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS assignments (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  class_id    INT NOT NULL,
  title       VARCHAR(190) NOT NULL,
  description TEXT NULL,
  due_date    DATE NULL,
  created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_assign_class (class_id),
  CONSTRAINT fk_assign_class FOREIGN KEY (class_id) REFERENCES teacher_classes(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS assignment_submissions (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  assignment_id INT NOT NULL,
  student_user_id INT NOT NULL,
  content     TEXT NULL,
  file_path   VARCHAR(255) NULL,
  score       DECIMAL(5,2) NULL,
  status      VARCHAR(20) NOT NULL DEFAULT 'submitted',
  created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_sub_assign (assignment_id),
  CONSTRAINT fk_sub_assign FOREIGN KEY (assignment_id) REFERENCES assignments(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS teacher_comments (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  teacher_user_id INT NOT NULL,
  student_user_id INT NOT NULL,
  comment     TEXT NOT NULL,
  created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_tcomment_student (student_user_id)
) ENGINE=InnoDB;

-- =====================================================================
-- MODULE 11: Gamification
-- =====================================================================
CREATE TABLE IF NOT EXISTS badges (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  code        VARCHAR(60) NOT NULL UNIQUE,
  name        VARCHAR(120) NOT NULL,
  description VARCHAR(255) NULL,
  icon        VARCHAR(80) NULL,
  created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS student_badges (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  user_id     INT NOT NULL,
  badge_id    INT NOT NULL,
  earned_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_student_badge (user_id, badge_id),
  CONSTRAINT fk_sb_user  FOREIGN KEY (user_id)  REFERENCES users(id)  ON DELETE CASCADE,
  CONSTRAINT fk_sb_badge FOREIGN KEY (badge_id) REFERENCES badges(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS xp_logs (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  user_id     INT NOT NULL,
  amount      INT NOT NULL,
  reason      VARCHAR(120) NULL,
  created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_xp_user (user_id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS streak_logs (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  user_id     INT NOT NULL,
  log_date    DATE NOT NULL,
  created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_streaklog (user_id, log_date)
) ENGINE=InnoDB;

-- =====================================================================
-- MODULE 12: Subscription & Payment
-- =====================================================================
CREATE TABLE IF NOT EXISTS subscription_plans (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  code        VARCHAR(40) NOT NULL UNIQUE,
  name        VARCHAR(120) NOT NULL,
  price       DECIMAL(10,2) NOT NULL DEFAULT 0,
  currency    VARCHAR(8) NOT NULL DEFAULT 'MYR',
  billing_cycle ENUM('trial','monthly','annual') NOT NULL DEFAULT 'monthly',
  trial_days  INT NOT NULL DEFAULT 0,
  features    TEXT NULL,
  sort_order  INT NOT NULL DEFAULT 0,
  status      VARCHAR(20) NOT NULL DEFAULT 'active',
  created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS user_subscriptions (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  user_id     INT NOT NULL,
  plan_id     INT NOT NULL,
  status      ENUM('active','expired','cancelled','trialing') NOT NULL DEFAULT 'trialing',
  starts_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  ends_at     DATETIME NULL,
  created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_usersub_user (user_id),
  CONSTRAINT fk_usersub_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_usersub_plan FOREIGN KEY (plan_id) REFERENCES subscription_plans(id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS payment_transactions (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  user_id     INT NOT NULL,
  subscription_id INT NULL,
  gateway     VARCHAR(40) NOT NULL DEFAULT 'billplz',
  gateway_ref VARCHAR(120) NULL,
  amount      DECIMAL(10,2) NOT NULL,
  currency    VARCHAR(8) NOT NULL DEFAULT 'MYR',
  status      ENUM('pending','paid','failed','refunded') NOT NULL DEFAULT 'pending',
  created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_pay_user (user_id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS invoices (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  user_id     INT NOT NULL,
  transaction_id INT NULL,
  invoice_no  VARCHAR(60) NOT NULL UNIQUE,
  amount      DECIMAL(10,2) NOT NULL,
  status      VARCHAR(20) NOT NULL DEFAULT 'issued',
  created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_invoice_user (user_id)
) ENGINE=InnoDB;

-- =====================================================================
-- MODULE 13: Notifications
-- =====================================================================
CREATE TABLE IF NOT EXISTS notification_templates (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  code        VARCHAR(60) NOT NULL UNIQUE,
  title       VARCHAR(150) NOT NULL,
  body        TEXT NOT NULL,
  channel     ENUM('in_app','email','whatsapp') NOT NULL DEFAULT 'in_app',
  created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS notifications (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  user_id     INT NOT NULL,
  title       VARCHAR(150) NOT NULL,
  body        TEXT NULL,
  type        VARCHAR(60) NULL,
  is_read     TINYINT(1) NOT NULL DEFAULT 0,
  created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_notif_user (user_id),
  CONSTRAINT fk_notif_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS notification_logs (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  notification_id INT NULL,
  channel     VARCHAR(20) NOT NULL,
  status      VARCHAR(20) NOT NULL DEFAULT 'sent',
  created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- =====================================================================
-- MODULE 15: Landing Page CMS
-- =====================================================================
CREATE TABLE IF NOT EXISTS site_settings (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  setting_key VARCHAR(80) NOT NULL UNIQUE,
  setting_value TEXT NULL,
  updated_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS landing_sections (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  section_key VARCHAR(60) NOT NULL,
  title       VARCHAR(190) NULL,
  subtitle    VARCHAR(255) NULL,
  body        TEXT NULL,
  image_path  VARCHAR(255) NULL,
  sort_order  INT NOT NULL DEFAULT 0,
  status      VARCHAR(20) NOT NULL DEFAULT 'active',
  updated_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_landing_key (section_key)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS testimonials (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  name        VARCHAR(120) NOT NULL,
  role        VARCHAR(120) NULL,
  quote       TEXT NOT NULL,
  avatar      VARCHAR(255) NULL,
  sort_order  INT NOT NULL DEFAULT 0,
  status      VARCHAR(20) NOT NULL DEFAULT 'active'
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS faqs (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  question    VARCHAR(255) NOT NULL,
  answer      TEXT NOT NULL,
  sort_order  INT NOT NULL DEFAULT 0,
  status      VARCHAR(20) NOT NULL DEFAULT 'active'
) ENGINE=InnoDB;

-- Content creator lessons / notes
CREATE TABLE IF NOT EXISTS lessons (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  subject_id  INT NULL,
  topic_id    INT NULL,
  skill_id    INT NULL,
  title       VARCHAR(190) NOT NULL,
  body        MEDIUMTEXT NULL,
  file_path   VARCHAR(255) NULL,
  created_by  INT NULL,
  status      ENUM('active','draft','pending') NOT NULL DEFAULT 'pending',
  created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_lesson_subject (subject_id)
) ENGINE=InnoDB;

-- =====================================================================
-- PHASE 4: Schools, API tokens
-- =====================================================================
CREATE TABLE IF NOT EXISTS schools (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  name        VARCHAR(190) NOT NULL,
  type        ENUM('school','learning_center') NOT NULL DEFAULT 'learning_center',
  owner_user_id INT NULL,
  contact_email VARCHAR(190) NULL,
  phone       VARCHAR(30) NULL,
  status      VARCHAR(20) NOT NULL DEFAULT 'active',
  created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_school_owner (owner_user_id)
) ENGINE=InnoDB;

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

-- =====================================================================
-- PHASE 5: Rate limiting
-- =====================================================================
CREATE TABLE IF NOT EXISTS rate_limits (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  rl_key       VARCHAR(190) NOT NULL,
  window_start DATETIME NOT NULL,
  hits         INT NOT NULL DEFAULT 0,
  UNIQUE KEY uq_rl_key (rl_key)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS ai_usage_log (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  user_id     INT NULL,
  kind        VARCHAR(40) NOT NULL DEFAULT 'chat',
  created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_usage_user_day (user_id, created_at),
  INDEX idx_usage_day (created_at)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS physics_formulas (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  topic_id     INT NULL,
  topic_label  VARCHAR(120) NULL,
  formula_name VARCHAR(150) NOT NULL,
  formula      VARCHAR(255) NOT NULL,
  description  TEXT NULL,
  sort_order   INT NOT NULL DEFAULT 0,
  status       VARCHAR(20) NOT NULL DEFAULT 'active',
  created_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_pf_topic (topic_id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS chemistry_formulas (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  topic_id     INT NULL,
  topic_label  VARCHAR(120) NULL,
  formula_name VARCHAR(150) NOT NULL,
  formula      VARCHAR(255) NOT NULL,
  description  TEXT NULL,
  sort_order   INT NOT NULL DEFAULT 0,
  status       VARCHAR(20) NOT NULL DEFAULT 'active',
  created_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_cf_topic (topic_id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS biology_diagrams (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  topic_id     INT NULL,
  topic_label  VARCHAR(120) NULL,
  title        VARCHAR(150) NOT NULL,
  description  TEXT NULL,
  image_url    VARCHAR(500) NULL,
  labels       TEXT NULL,
  sort_order   INT NOT NULL DEFAULT 0,
  status       VARCHAR(20) NOT NULL DEFAULT 'active',
  created_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_bd_topic (topic_id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS biology_flashcards (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  topic_id     INT NULL,
  topic_label  VARCHAR(120) NULL,
  question     TEXT NOT NULL,
  answer       TEXT NOT NULL,
  hint         TEXT NULL,
  difficulty   VARCHAR(20) NOT NULL DEFAULT 'medium',
  sort_order   INT NOT NULL DEFAULT 0,
  status       VARCHAR(20) NOT NULL DEFAULT 'active',
  created_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_bf_topic (topic_id)
) ENGINE=InnoDB;

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

-- =====================================================================
-- Landing page leads / contact form
-- =====================================================================
CREATE TABLE IF NOT EXISTS leads (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  name        VARCHAR(150) NOT NULL,
  email       VARCHAR(190) NOT NULL,
  phone       VARCHAR(30) NULL,
  message     TEXT NULL,
  source      VARCHAR(60) NULL,
  status      ENUM('new','contacted','closed') NOT NULL DEFAULT 'new',
  created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_leads_status (status)
) ENGINE=InnoDB;

SET FOREIGN_KEY_CHECKS = 1;
