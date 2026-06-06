-- =====================================================================
-- LulusAI -- Seed data
-- The admin account is created by install.php with a freshly hashed password.
-- =====================================================================
SET NAMES utf8mb4;

-- NOTE: the admin account is created by install.php with a freshly hashed
-- password so no plaintext/placeholder hash is committed to the repo.

-- Education levels
INSERT INTO education_levels (name, slug, sort_order) VALUES
('UPSR', 'upsr', 1),
('PT3',  'pt3',  2),
('SPM',  'spm',  3),
('STPM', 'stpm', 4)
ON DUPLICATE KEY UPDATE name = VALUES(name);

-- Subjects (SPM level assumed id=3)
INSERT INTO subjects (education_level_id, name, slug, icon, description, sort_order) VALUES
(3, 'Mathematics', 'mathematics', 'calculator', 'SPM Mathematics core skills', 1),
(3, 'Additional Mathematics', 'add-maths', 'function', 'SPM Add Maths', 2),
(3, 'Physics', 'physics', 'atom', 'SPM Physics', 3),
(3, 'Chemistry', 'chemistry', 'flask', 'SPM Chemistry', 4),
(3, 'Biology', 'biology', 'dna', 'SPM Biology', 5),
(3, 'English', 'english', 'book', 'SPM English', 6),
(3, 'Bahasa Melayu', 'bahasa-melayu', 'language', 'SPM Bahasa Melayu', 7)
ON DUPLICATE KEY UPDATE name = VALUES(name);

-- Topics for Mathematics (subject id=1)
INSERT INTO topics (subject_id, name, slug, sort_order) VALUES
(1, 'Algebra', 'algebra', 1),
(1, 'Geometry', 'geometry', 2),
(1, 'Trigonometry', 'trigonometry', 3),
(1, 'Statistics', 'statistics', 4)
ON DUPLICATE KEY UPDATE name = VALUES(name);

-- Skills for Algebra (topic id=1)
INSERT INTO skills (topic_id, name, difficulty, description) VALUES
(1, 'Solve linear equation', 'easy', 'Solve equations of the form ax + b = c'),
(1, 'Solve quadratic equation', 'medium', 'Solve ax^2 + bx + c = 0'),
(1, 'Simplify algebraic expressions', 'easy', 'Combine like terms')
ON DUPLICATE KEY UPDATE name = VALUES(name);

-- Sample MCQ questions (subject Mathematics=1, topic Algebra=1)
INSERT INTO questions (id, subject_id, topic_id, skill_id, type, difficulty, question_text, explanation, marks, status) VALUES
(1, 1, 1, 1, 'mcq', 'easy', 'Solve for x: 2x + 3 = 11', 'Subtract 3 from both sides to get 2x = 8, then divide by 2 so x = 4.', 1, 'active'),
(2, 1, 1, 3, 'mcq', 'easy', 'Simplify: 3x + 5x', 'Add the like terms: 3x + 5x = 8x.', 1, 'active'),
(3, 1, 1, 2, 'mcq', 'medium', 'Solve for x: x^2 - 5x + 6 = 0', 'Factorise to (x-2)(x-3)=0, so x = 2 or x = 3.', 1, 'active')
ON DUPLICATE KEY UPDATE question_text = VALUES(question_text);

INSERT INTO question_options (question_id, label, option_text, is_correct, sort_order) VALUES
(1, 'A', 'x = 3', 0, 1),
(1, 'B', 'x = 4', 1, 2),
(1, 'C', 'x = 5', 0, 3),
(1, 'D', 'x = 7', 0, 4),
(2, 'A', '8x',   1, 1),
(2, 'B', '15x',  0, 2),
(2, 'C', '8x^2', 0, 3),
(2, 'D', '2x',   0, 4),
(3, 'A', 'x = 1 or x = 6', 0, 1),
(3, 'B', 'x = 2 or x = 3', 1, 2),
(3, 'C', 'x = -2 or x = -3', 0, 3),
(3, 'D', 'x = 0 or x = 5', 0, 4);

-- Subscription plans
INSERT INTO subscription_plans (code, name, price, currency, billing_cycle, trial_days, features, sort_order) VALUES
('free_trial', 'Free Trial', 0.00, 'MYR', 'trial', 14, '14 days access|1 subject|Limited AI chat|Basic progress', 1),
('monthly', 'Monthly', 49.00, 'MYR', 'monthly', 0, 'All subjects|Full AI tutor|Snap & Check|Progress tracking|AI insight', 2),
('annual', 'Annual', 500.00, 'MYR', 'annual', 0, 'All monthly features|Priority support|Badge / certificate|2 months free', 3)
ON DUPLICATE KEY UPDATE name = VALUES(name);

-- AI prompt templates
INSERT INTO ai_prompt_templates (code, name, system_prompt, temperature) VALUES
('tutor', 'AI Tutor', 'You are LulusAI, a friendly, encouraging Malaysian exam tutor. Teach step-by-step using simple language. Never just give the final answer without explaining the method. Adapt to the student education level and subject provided. You may answer in Bahasa Melayu or English to match the student. Ask short follow-up diagnostic questions when helpful. Admit uncertainty rather than giving false certainty. Do not predict exam outcomes. Keep students motivated.', 0.40),
('diagnostic', 'AI Diagnostic', 'You generate and interpret diagnostic quizzes mapped to syllabus topics and skills. Identify strong and weak topics from the student answers and produce a clear, encouraging summary with a prioritised practice plan.', 0.30),
('marking', 'AI Marking', 'You mark a student answer against the marking scheme. Return: score, correct parts, mistakes, suggested correction, the topic weakness, and a recommended next question. Be fair, specific, and encouraging. State a confidence level and never claim certainty you do not have.', 0.20),
('parent_report', 'AI Parent Report', 'You write a short, warm weekly progress report for a parent about their child. Mention improvements, remaining weak areas, and one concrete recommended action. Keep it positive and practical.', 0.50),
('learning_path', 'AI Learning Path', 'You build an adaptive learning path. Given diagnostic results, weak topics, difficulty, streak, and exam target date, output an ordered list of topics/skills to study with daily missions and weekly goals.', 0.40),
('weakness', 'AI Weakness Analysis', 'You analyse a student attempt history and summarise recurring weaknesses by topic and skill, with the likely root cause and a focused remedy.', 0.30)
ON DUPLICATE KEY UPDATE name = VALUES(name);

-- Badges
INSERT INTO badges (code, name, description, icon) VALUES
('streak_3', '3-Day Streak', 'Studied 3 days in a row', 'flame'),
('streak_7', '7-Day Streak', 'Studied 7 days in a row', 'fire'),
('math_warrior', 'Math Warrior', 'Answered 50 maths questions', 'sword'),
('science_explorer', 'Science Explorer', 'Completed a science diagnostic', 'microscope'),
('spm_champion', 'SPM Champion', 'Reached 80% average across SPM subjects', 'trophy')
ON DUPLICATE KEY UPDATE name = VALUES(name);

-- Landing sections
INSERT INTO landing_sections (section_key, title, subtitle, body, sort_order) VALUES
('hero', 'Not just an app. Your personal AI tutor.', 'LulusAI helps SPM students learn, practise and improve with AI guidance across 22 KSSM subjects — plus Malaysia''s first SPM-aligned AI literacy elective.', 'Start your 14-day free trial today.', 1),
('features', 'Everything an SPM student needs to improve', 'An AI Skill Education Operating System', 'AI Tutor Chat (BM + EN)|AI Writing Marker (Karangan, Rumusan, Prompt Engineering)|40,000+ KSSM-aligned questions|Library + Flashcards with spaced repetition|AI Sandbox — compare LLMs side-by-side|Snap & Check photo marking|Adaptive diagnostic + learning path|Streaks, mastery badges, XP|Parent + teacher dashboards', 2),
('parents', 'Built for parents too', 'See real progress, not just screen time', 'Weekly AI reports, weak-area alerts, and recommended action plans keep you in the loop.', 3),
('cta', 'Ready to start learning smarter?', 'Join thousands of students improving with LulusAI', 'Create your free account in under a minute.', 4)
ON DUPLICATE KEY UPDATE title = VALUES(title), subtitle = VALUES(subtitle), body = VALUES(body);

INSERT INTO testimonials (name, role, quote, sort_order) VALUES
('Nurul',         'Parent of SPM student',         'The weekly report finally tells me where my son actually struggles. Game changer.', 1),
('Wei Jie',       'SPM Student, SMK Damansara',    'The AI tutor explains step-by-step until I get it. My Add Maths went from C to A-.', 2),
('Aisyah',        'SPM Student, Penang',           'I love that I can paste my karangan and get a band-5 mark with peribahasa suggestions in seconds.', 3),
('Cikgu Faridah', 'Bahasa Melayu teacher',         'I assign Snap & Check homework and the AI marks it overnight. My weekend just opened up.', 4),
('Daniel',        'SPM Student, KL',               'Tried the AI Sandbox to compare Claude vs GPT for my prompt — finally understand what makes a good prompt.', 5),
('Mr Raj',        'Parent of two SPM students',    'One subscription, both kids covered. The progress dashboard is the first thing I check every morning.', 6)
ON DUPLICATE KEY UPDATE quote = VALUES(quote), role = VALUES(role);

INSERT INTO faqs (question, answer, sort_order) VALUES
('Is there a free trial?', 'Yes, every new student gets a 14-day free trial with one subject.', 1),
('Which syllabus do you support?', 'We focus on the Malaysian SPM syllabus — KSSM-aligned across all 22 SPM subjects from sciences (Math, Add Maths, Physics, Chemistry, Biology, Sains) to languages (BM, English, Bahasa Cina, Tamil, Arab), humanities (Sejarah, Geografi, Pendidikan Islam, Moral), commerce (Perakaunan, Perniagaan, Ekonomi), technology (Sains Komputer, RBT, PSV) and our Pioneer AI elective.', 2),
('Can parents track progress?', 'Yes. Link your parent account to your child to see weekly AI reports and weak areas.', 3),
('How does AI marking work?', 'Two ways: Snap & Check lets you upload a photo of handwritten work for instant AI marking, and the AI Writing Marker grades Karangan, Rumusan, tatabahasa, vocabulary upgrades and Prompt Engineering submissions against SPM rubrics. A teacher can review any submission.', 4),
('What is the Asas Kepintaran Buatan (AI) elective?', 'A Pioneer Track elective we created for students who want to be future-ready. 20 bab covering AI fundamentals, machine learning, prompt engineering, ethics, and how AI is changing the Malaysian economy. Comes with hands-on tools (Prompt Engineering Marker + AI Sandbox) so students learn by doing, not just reading.', 5)
ON DUPLICATE KEY UPDATE answer = VALUES(answer);

INSERT INTO site_settings (setting_key, setting_value) VALUES
('site_name', 'LulusAI'),
('tagline', 'Not just an app. Your personal AI tutor.'),
('support_email', 'hello@lulusai.my')
ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value);
