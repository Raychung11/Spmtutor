-- =====================================================================
-- SkillTutor AI -- Expanded content pack (topics, skills, MCQs).
-- Run once after seed.sql (install.php does this automatically).
-- References subjects/topics by slug so it is order-independent.
-- =====================================================================
SET NAMES utf8mb4;

-- ---- Topics ----------------------------------------------------------
INSERT INTO topics (subject_id, name, slug, sort_order) VALUES
((SELECT id FROM subjects WHERE slug='add-maths' LIMIT 1), 'Functions', 'am-functions', 1),
((SELECT id FROM subjects WHERE slug='add-maths' LIMIT 1), 'Differentiation', 'am-differentiation', 2),
((SELECT id FROM subjects WHERE slug='physics' LIMIT 1), 'Forces and Motion', 'phy-forces', 1),
((SELECT id FROM subjects WHERE slug='physics' LIMIT 1), 'Electricity', 'phy-electricity', 2),
((SELECT id FROM subjects WHERE slug='chemistry' LIMIT 1), 'The Mole', 'chem-mole', 1),
((SELECT id FROM subjects WHERE slug='chemistry' LIMIT 1), 'Acids and Bases', 'chem-acids', 2),
((SELECT id FROM subjects WHERE slug='biology' LIMIT 1), 'Cell Biology', 'bio-cells', 1),
((SELECT id FROM subjects WHERE slug='biology' LIMIT 1), 'Genetics', 'bio-genetics', 2)
ON DUPLICATE KEY UPDATE name = VALUES(name);

-- ---- Skills ----------------------------------------------------------
INSERT INTO skills (topic_id, name, difficulty, description) VALUES
((SELECT id FROM topics WHERE slug='phy-forces' LIMIT 1), 'Apply F = ma', 'easy', 'Newton''s second law'),
((SELECT id FROM topics WHERE slug='phy-electricity' LIMIT 1), 'Apply V = IR', 'easy', 'Ohm''s law'),
((SELECT id FROM topics WHERE slug='chem-mole' LIMIT 1), 'Calculate moles', 'medium', 'n = m / M'),
((SELECT id FROM topics WHERE slug='am-differentiation' LIMIT 1), 'Differentiate polynomials', 'medium', 'Power rule')
ON DUPLICATE KEY UPDATE name = VALUES(name);

-- ---- Helper: insert a question + 4 options -------------------------
-- Pattern repeated per question using @q := LAST_INSERT_ID().

-- Add Maths: Functions
INSERT INTO questions (subject_id, topic_id, type, difficulty, question_text, explanation, marks, status) VALUES
((SELECT id FROM subjects WHERE slug='add-maths' LIMIT 1),
 (SELECT id FROM topics WHERE slug='am-functions' LIMIT 1),
 'mcq','easy','Given f(x) = 2x + 1, find f(3).','Substitute x = 3: f(3) = 2(3) + 1 = 7.',1,'active');
SET @q := LAST_INSERT_ID();
INSERT INTO question_options (question_id,label,option_text,is_correct,sort_order) VALUES
(@q,'A','5',0,1),(@q,'B','6',0,2),(@q,'C','7',1,3),(@q,'D','9',0,4);

-- Add Maths: Differentiation
INSERT INTO questions (subject_id, topic_id, type, difficulty, question_text, explanation, marks, status) VALUES
((SELECT id FROM subjects WHERE slug='add-maths' LIMIT 1),
 (SELECT id FROM topics WHERE slug='am-differentiation' LIMIT 1),
 'mcq','medium','Differentiate y = x^3 with respect to x.','Power rule: dy/dx = 3x^2.',1,'active');
SET @q := LAST_INSERT_ID();
INSERT INTO question_options (question_id,label,option_text,is_correct,sort_order) VALUES
(@q,'A','3x^2',1,1),(@q,'B','x^2',0,2),(@q,'C','3x',0,3),(@q,'D','x^3/3',0,4);

-- Physics: Forces
INSERT INTO questions (subject_id, topic_id, type, difficulty, question_text, explanation, marks, status) VALUES
((SELECT id FROM subjects WHERE slug='physics' LIMIT 1),
 (SELECT id FROM topics WHERE slug='phy-forces' LIMIT 1),
 'mcq','easy','A 2 kg mass accelerates at 3 m/s^2. What is the net force?','F = ma = 2 x 3 = 6 N.',1,'active');
SET @q := LAST_INSERT_ID();
INSERT INTO question_options (question_id,label,option_text,is_correct,sort_order) VALUES
(@q,'A','1.5 N',0,1),(@q,'B','5 N',0,2),(@q,'C','6 N',1,3),(@q,'D','8 N',0,4);

-- Physics: Electricity
INSERT INTO questions (subject_id, topic_id, type, difficulty, question_text, explanation, marks, status) VALUES
((SELECT id FROM subjects WHERE slug='physics' LIMIT 1),
 (SELECT id FROM topics WHERE slug='phy-electricity' LIMIT 1),
 'mcq','easy','A 12 V supply drives 3 A through a resistor. Find the resistance.','R = V / I = 12 / 3 = 4 ohm.',1,'active');
SET @q := LAST_INSERT_ID();
INSERT INTO question_options (question_id,label,option_text,is_correct,sort_order) VALUES
(@q,'A','4 ohm',1,1),(@q,'B','9 ohm',0,2),(@q,'C','15 ohm',0,3),(@q,'D','36 ohm',0,4);

-- Chemistry: The Mole
INSERT INTO questions (subject_id, topic_id, type, difficulty, question_text, explanation, marks, status) VALUES
((SELECT id FROM subjects WHERE slug='chemistry' LIMIT 1),
 (SELECT id FROM topics WHERE slug='chem-mole' LIMIT 1),
 'mcq','medium','How many moles are in 36 g of water (M = 18 g/mol)?','n = m / M = 36 / 18 = 2 mol.',1,'active');
SET @q := LAST_INSERT_ID();
INSERT INTO question_options (question_id,label,option_text,is_correct,sort_order) VALUES
(@q,'A','0.5 mol',0,1),(@q,'B','1 mol',0,2),(@q,'C','2 mol',1,3),(@q,'D','18 mol',0,4);

-- Chemistry: Acids and Bases
INSERT INTO questions (subject_id, topic_id, type, difficulty, question_text, explanation, marks, status) VALUES
((SELECT id FROM subjects WHERE slug='chemistry' LIMIT 1),
 (SELECT id FROM topics WHERE slug='chem-acids' LIMIT 1),
 'mcq','easy','What is the pH of a neutral solution at 25 C?','A neutral solution has pH 7.',1,'active');
SET @q := LAST_INSERT_ID();
INSERT INTO question_options (question_id,label,option_text,is_correct,sort_order) VALUES
(@q,'A','0',0,1),(@q,'B','7',1,2),(@q,'C','10',0,3),(@q,'D','14',0,4);

-- Biology: Cell Biology
INSERT INTO questions (subject_id, topic_id, type, difficulty, question_text, explanation, marks, status) VALUES
((SELECT id FROM subjects WHERE slug='biology' LIMIT 1),
 (SELECT id FROM topics WHERE slug='bio-cells' LIMIT 1),
 'mcq','easy','Which organelle is the site of aerobic respiration?','Mitochondria are the powerhouses of the cell.',1,'active');
SET @q := LAST_INSERT_ID();
INSERT INTO question_options (question_id,label,option_text,is_correct,sort_order) VALUES
(@q,'A','Nucleus',0,1),(@q,'B','Ribosome',0,2),(@q,'C','Mitochondrion',1,3),(@q,'D','Golgi apparatus',0,4);

-- Biology: Genetics
INSERT INTO questions (subject_id, topic_id, type, difficulty, question_text, explanation, marks, status) VALUES
((SELECT id FROM subjects WHERE slug='biology' LIMIT 1),
 (SELECT id FROM topics WHERE slug='bio-genetics' LIMIT 1),
 'mcq','medium','In a monohybrid cross Aa x Aa, what fraction shows the recessive trait?','Ratio 3:1, so 1/4 show the recessive trait.',1,'active');
SET @q := LAST_INSERT_ID();
INSERT INTO question_options (question_id,label,option_text,is_correct,sort_order) VALUES
(@q,'A','0',0,1),(@q,'B','1/4',1,2),(@q,'C','1/2',0,3),(@q,'D','3/4',0,4);

-- Extra Mathematics questions in existing Algebra topic
INSERT INTO questions (subject_id, topic_id, type, difficulty, question_text, explanation, marks, status) VALUES
((SELECT id FROM subjects WHERE slug='mathematics' LIMIT 1),
 (SELECT id FROM topics WHERE slug='algebra' AND subject_id=(SELECT id FROM subjects WHERE slug='mathematics' LIMIT 1) LIMIT 1),
 'mcq','medium','Expand (x + 2)(x + 3).','(x+2)(x+3) = x^2 + 5x + 6.',1,'active');
SET @q := LAST_INSERT_ID();
INSERT INTO question_options (question_id,label,option_text,is_correct,sort_order) VALUES
(@q,'A','x^2 + 5x + 6',1,1),(@q,'B','x^2 + 6x + 5',0,2),(@q,'C','x^2 + 6',0,3),(@q,'D','x^2 + 5x + 5',0,4);
