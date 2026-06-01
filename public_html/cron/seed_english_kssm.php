<?php
/**
 * KSSM SPM English syllabus seeder. Form 4 + Form 5, 20 chapters total
 * covering listening/speaking/reading/writing/grammar/literature, plus a
 * starter vocabulary bank, idiom bank and model writing samples used by
 * the AI Essay Marker / Vocabulary Coach / Grammar Trainer.
 *
 * Idempotent: topics matched by (subject_id, name, form_level); legacy
 * same-named NULL-form topics are adopted.
 *
 *   php public_html/cron/seed_english_kssm.php
 */
declare(strict_types=1);

require_once __DIR__ . '/../inc/db.php';

function english_kssm_catalog(): array
{
    return [
        // ============= FORM 4 =============
        ['form' => 4, 'name' => 'Listening Skills', 'subtopics' => [
            'Listening for Main Ideas', 'Listening for Details', 'Listening for Inference',
            'Note-taking', 'Listening Across Text Types',
        ], 'skills' => [
            ['Identify main ideas in spoken texts', 'easy'],
            ['Extract specific information', 'medium'],
            ['Infer meaning from context', 'medium'],
            ['Take effective notes', 'medium'],
        ]],
        ['form' => 4, 'name' => 'Speaking Skills', 'subtopics' => [
            'Pronunciation and Stress', 'Asking and Answering Questions',
            'Describing People, Places, Events', 'Giving Opinions',
            'Group Discussions', 'Short Presentations',
        ], 'skills' => [
            ['Speak fluently and clearly', 'medium'],
            ['Engage in extended dialogues', 'medium'],
            ['Express and justify opinions', 'medium'],
            ['Deliver a short talk', 'medium'],
        ]],
        ['form' => 4, 'name' => 'Reading Comprehension', 'subtopics' => [
            'Skimming and Scanning', 'Main Idea and Supporting Details',
            'Vocabulary in Context', 'Making Inferences',
            'Author\'s Purpose', 'Text Types (Article / Report / Brochure)',
        ], 'skills' => [
            ['Comprehend a range of texts', 'medium'],
            ['Infer meaning from context', 'medium'],
            ['Identify purpose and audience', 'medium'],
        ]],
        ['form' => 4, 'name' => 'Short Communicative Writing', 'subtopics' => [
            'Notes and Messages', 'Informal Emails', 'Postcards', 'Captions', 'Short Reports',
        ], 'skills' => [
            ['Write focused short messages', 'easy'],
            ['Apply correct register', 'medium'],
        ]],
        ['form' => 4, 'name' => 'Directed Writing', 'subtopics' => [
            'Articles', 'Speeches', 'Reports', 'Reviews', 'Formal Letters',
        ], 'skills' => [
            ['Plan a directed response', 'medium'],
            ['Use appropriate format and tone', 'medium'],
            ['Develop ideas with examples', 'medium'],
        ]],
        ['form' => 4, 'name' => 'Grammar Foundations', 'subtopics' => [
            'Tenses (Present / Past / Future)', 'Subject-Verb Agreement', 'Articles (a / an / the)',
            'Prepositions', 'Conjunctions', 'Modal Verbs',
        ], 'skills' => [
            ['Use correct tenses', 'medium'],
            ['Apply grammar rules', 'medium'],
            ['Identify and correct errors', 'medium'],
        ]],
        ['form' => 4, 'name' => 'Vocabulary Building', 'subtopics' => [
            'Word Forms (Noun / Verb / Adj / Adv)', 'Synonyms and Antonyms',
            'Collocations', 'Phrasal Verbs', 'Idioms', 'Connotation and Register',
        ], 'skills' => [
            ['Expand active vocabulary', 'medium'],
            ['Use appropriate word forms', 'medium'],
            ['Apply idiomatic English', 'hard'],
        ]],
        ['form' => 4, 'name' => 'Literature: Poems (Form 4)', 'subtopics' => [
            'Theme and Tone', 'Imagery and Figurative Language',
            'Rhyme and Rhythm', 'Persona and Voice', 'Setting and Context',
        ], 'skills' => [
            ['Analyse poetic devices', 'medium'],
            ['Identify themes', 'medium'],
            ['Respond critically to poems', 'hard'],
        ]],
        ['form' => 4, 'name' => 'Literature: Short Stories (Form 4)', 'subtopics' => [
            'Plot Structure', 'Characters and Characterisation', 'Setting',
            'Theme and Message', 'Moral Values',
        ], 'skills' => [
            ['Summarise short stories', 'medium'],
            ['Analyse characters', 'medium'],
            ['Discuss themes and values', 'medium'],
        ]],
        ['form' => 4, 'name' => 'Literature: Drama (Form 4)', 'subtopics' => [
            'Plot and Conflict', 'Dramatic Devices', 'Characters', 'Stage Directions', 'Themes',
        ], 'skills' => [
            ['Interpret dramatic texts', 'medium'],
            ['Analyse characters in drama', 'medium'],
            ['Connect themes to real life', 'medium'],
        ]],

        // ============= FORM 5 =============
        ['form' => 5, 'name' => 'Advanced Listening', 'subtopics' => [
            'Listening for Tone and Attitude', 'Critical Listening',
            'Authentic Texts (Interviews / News)', 'Identifying Speaker\'s Purpose',
        ], 'skills' => [
            ['Listen critically', 'medium'],
            ['Distinguish fact from opinion', 'hard'],
            ['Interpret tone and attitude', 'hard'],
        ]],
        ['form' => 5, 'name' => 'Advanced Speaking', 'subtopics' => [
            'Formal Presentations', 'Persuasive Speaking',
            'Debates and Discussions', 'Interviews',
        ], 'skills' => [
            ['Speak persuasively', 'hard'],
            ['Engage in formal discussions', 'medium'],
            ['Defend a position with evidence', 'hard'],
        ]],
        ['form' => 5, 'name' => 'Critical Reading', 'subtopics' => [
            'Critical Reading', 'Analysing Arguments',
            'Reading Across Genres', 'Tone and Bias', 'Cause-and-Effect Texts',
        ], 'skills' => [
            ['Read critically', 'hard'],
            ['Analyse arguments', 'hard'],
            ['Identify bias and tone', 'hard'],
        ]],
        ['form' => 5, 'name' => 'Summary Writing', 'subtopics' => [
            'Identifying Main Points', 'Paraphrasing',
            'Word Limit and Structure', 'Linking Ideas',
        ], 'skills' => [
            ['Summarise coherently', 'medium'],
            ['Paraphrase accurately', 'hard'],
            ['Meet word count', 'medium'],
        ]],
        ['form' => 5, 'name' => 'Continuous Writing (Essays)', 'subtopics' => [
            'Narrative Essays', 'Descriptive Essays', 'Argumentative Essays',
            'Expository Essays', 'Reflective Essays',
        ], 'skills' => [
            ['Plan and structure essays', 'medium'],
            ['Develop ideas with examples', 'medium'],
            ['Use sophisticated vocabulary', 'hard'],
        ]],
        ['form' => 5, 'name' => 'Extended Writing: Reports & Reviews', 'subtopics' => [
            'Formal Reports', 'Book / Film Reviews', 'Proposals', 'Articles for Publication',
        ], 'skills' => [
            ['Write extended formal texts', 'hard'],
            ['Apply appropriate register', 'medium'],
            ['Structure long responses', 'medium'],
        ]],
        ['form' => 5, 'name' => 'Advanced Grammar', 'subtopics' => [
            'Passive Voice', 'Reported Speech',
            'Conditionals (Type 0, 1, 2, 3)', 'Relative Clauses',
            'Inversion and Emphasis', 'Advanced Tenses',
        ], 'skills' => [
            ['Apply complex grammar structures', 'hard'],
            ['Use varied sentence types', 'medium'],
            ['Edit and proofread effectively', 'medium'],
        ]],
        ['form' => 5, 'name' => 'Literature: Poems (Form 5)', 'subtopics' => [
            'Advanced Analysis', 'Comparative Study',
            'Themes and Values', 'Poetic Techniques', 'Personal Response',
        ], 'skills' => [
            ['Compare poems', 'hard'],
            ['Develop critical responses', 'hard'],
            ['Justify interpretations', 'medium'],
        ]],
        ['form' => 5, 'name' => 'Literature: Novel', 'subtopics' => [
            'Plot and Structure', 'Major and Minor Characters', 'Themes',
            'Setting and Atmosphere', 'Moral Values and Lessons', 'Narrative Techniques',
        ], 'skills' => [
            ['Analyse novel structure', 'medium'],
            ['Discuss characters in depth', 'medium'],
            ['Identify themes and messages', 'medium'],
            ['Answer essay questions on novels', 'hard'],
        ]],
        ['form' => 5, 'name' => 'Literature: Drama (Form 5)', 'subtopics' => [
            'Character Development', 'Dramatic Tension', 'Stagecraft', 'Themes and Messages',
        ], 'skills' => [
            ['Interpret drama critically', 'hard'],
            ['Analyse tension and conflict', 'medium'],
            ['Connect themes to contemporary issues', 'hard'],
        ]],
    ];
}

/** Starter SPM-level vocabulary bank. word, part_of_speech, meaning, example, synonyms, antonyms, level, theme. */
function english_kssm_vocabulary(): array
{
    return [
        // ------- intermediate -------
        ['meticulous',     'adjective', 'Very careful and precise about small details.', 'She kept meticulous records of every expense.', 'thorough, careful, precise', 'careless, sloppy', 'intermediate', 'character'],
        ['prevalent',      'adjective', 'Widespread or common in a particular area or time.', 'Social media is prevalent among teenagers today.', 'widespread, common, rife', 'rare, scarce', 'intermediate', 'society'],
        ['lucrative',      'adjective', 'Producing a great deal of profit.', 'Tutoring SPM students has become a lucrative side business.', 'profitable, well-paid, gainful', 'unprofitable', 'intermediate', 'money'],
        ['sustainable',    'adjective', 'Able to be maintained without depleting resources.', 'We need sustainable solutions to the plastic waste problem.', 'eco-friendly, viable, lasting', 'unsustainable, harmful', 'intermediate', 'environment'],
        ['consensus',      'noun',      'General agreement among a group.', 'The committee finally reached a consensus on the new rule.', 'agreement, accord, harmony', 'disagreement, dissent', 'intermediate', 'discussion'],
        ['phenomenon',     'noun',      'A fact or situation that is observed to exist or happen.', 'Climate change is a global phenomenon affecting everyone.', 'occurrence, event', '', 'intermediate', 'science'],
        ['detrimental',    'adjective', 'Tending to cause harm or damage.', 'Smoking is detrimental to your health.', 'harmful, damaging, adverse', 'beneficial, helpful', 'intermediate', 'health'],
        ['inevitable',     'adjective', 'Certain to happen; unavoidable.', 'With this level of preparation, success seems inevitable.', 'unavoidable, certain', 'avoidable, uncertain', 'intermediate', 'general'],
        ['feasible',       'adjective', 'Possible to do easily or conveniently.', 'Is it feasible to finish the project by next week?', 'possible, viable, realistic', 'impossible, impractical', 'intermediate', 'planning'],
        ['enhance',        'verb',      'To improve the quality, value, or extent of something.', 'Reading widely enhances your vocabulary.', 'improve, boost, upgrade', 'diminish, reduce', 'intermediate', 'learning'],
        ['acquire',        'verb',      'To gain or obtain something, often through effort.', 'It takes years to acquire fluency in a new language.', 'gain, obtain, attain', 'lose, forfeit', 'intermediate', 'learning'],
        ['contemporary',   'adjective', 'Belonging to the present time; modern.', 'Contemporary art often challenges traditional ideas.', 'modern, current, present-day', 'ancient, outdated', 'intermediate', 'culture'],
        ['fundamental',    'adjective', 'Forming a necessary base or core; central.', 'Reading is fundamental to academic success.', 'essential, basic, core', 'minor, trivial', 'intermediate', 'general'],
        ['adversity',      'noun',      'Difficulties or misfortune.', 'Resilience helps us overcome adversity.', 'hardship, difficulty, trouble', 'ease, prosperity', 'intermediate', 'character'],
        ['perseverance',   'noun',      'Persistence in doing something despite difficulty.', 'Her perseverance helped her score 10 A+ in SPM.', 'persistence, determination, grit', 'laziness, indolence', 'intermediate', 'character'],
        ['compassion',     'noun',      'Sympathetic concern for the suffering of others.', 'A nurse must have compassion for her patients.', 'empathy, kindness, sympathy', 'cruelty, indifference', 'intermediate', 'character'],
        ['integrity',      'noun',      'The quality of being honest and having strong moral principles.', 'A leader of integrity earns the trust of others.', 'honesty, principle, uprightness', 'dishonesty, corruption', 'intermediate', 'character'],
        ['diligent',       'adjective', 'Showing care and conscientiousness in one\'s work.', 'Diligent students review their notes every day.', 'hard-working, conscientious, industrious', 'lazy, idle', 'intermediate', 'character'],
        ['versatile',      'adjective', 'Able to adapt to many different functions or activities.', 'A smartphone is a versatile tool for learning.', 'flexible, adaptable, all-round', 'limited, inflexible', 'intermediate', 'general'],
        ['inevitable',     'adjective', 'Certain to happen; unavoidable.', 'Mistakes are inevitable when learning something new.', 'unavoidable, certain', 'avoidable', 'intermediate', 'general'],
        ['notable',        'adjective', 'Worthy of attention or notice; remarkable.', 'A notable feature of Malaysia is its diversity.', 'remarkable, prominent, distinguished', 'unremarkable, ordinary', 'intermediate', 'general'],
        ['controversial',  'adjective', 'Giving rise or likely to give rise to public disagreement.', 'Mobile phones in schools is a controversial topic.', 'debatable, disputed, divisive', 'uncontroversial, agreed', 'intermediate', 'society'],
        ['significant',    'adjective', 'Sufficiently great or important to be worthy of attention.', 'Reading has a significant impact on academic performance.', 'important, considerable, meaningful', 'insignificant, minor', 'intermediate', 'general'],
        ['emerging',       'adjective', 'Newly created or noticed; growing.', 'AI is one of the most exciting emerging technologies.', 'developing, growing, nascent', 'declining, established', 'intermediate', 'technology'],

        // ------- advanced -------
        ['substantiate',   'verb',      'To provide evidence to support or prove the truth of.', 'You must substantiate your claims with reliable sources.', 'prove, verify, support', 'disprove, refute', 'advanced', 'argument'],
        ['exacerbate',     'verb',      'To make a problem or situation worse.', 'Cutting subsidies could exacerbate inflation.', 'worsen, aggravate, intensify', 'alleviate, ease', 'advanced', 'argument'],
        ['mitigate',       'verb',      'To make less severe, serious, or painful.', 'Planting trees can mitigate the effects of climate change.', 'lessen, alleviate, reduce', 'worsen, intensify', 'advanced', 'environment'],
        ['scrutinise',     'verb',      'To examine carefully and in detail.', 'Editors scrutinise every word of an article.', 'examine, inspect, study', 'overlook, ignore', 'advanced', 'argument'],
        ['ambiguous',      'adjective', 'Open to more than one interpretation; unclear.', 'The instructions were so ambiguous that we got lost.', 'unclear, vague, equivocal', 'clear, unambiguous', 'advanced', 'language'],
        ['plausible',      'adjective', 'Seeming reasonable or believable.', 'She gave a plausible explanation for her absence.', 'believable, credible, reasonable', 'implausible, far-fetched', 'advanced', 'argument'],
        ['ubiquitous',     'adjective', 'Present, appearing, or found everywhere.', 'Mobile phones have become ubiquitous in modern life.', 'omnipresent, widespread', 'rare, scarce', 'advanced', 'technology'],
        ['paramount',      'adjective', 'More important than anything else; supreme.', 'Safety is of paramount importance on a construction site.', 'supreme, foremost, vital', 'minor, unimportant', 'advanced', 'general'],
        ['inherent',       'adjective', 'Existing as a natural or basic part of something.', 'There are inherent risks in any extreme sport.', 'innate, intrinsic, built-in', 'extraneous, external', 'advanced', 'general'],
        ['advocate',       'verb',      'To publicly support or recommend a cause or policy.', 'The minister advocates for greater investment in education.', 'support, champion, endorse', 'oppose, denounce', 'advanced', 'argument'],
        ['perceive',       'verb',      'To become aware or conscious of; to interpret in a particular way.', 'How we perceive failure determines how we respond to it.', 'sense, recognise, view', 'overlook, ignore', 'advanced', 'general'],
        ['unprecedented',  'adjective', 'Never done or known before.', 'The pandemic caused unprecedented disruption to schools.', 'unparalleled, unmatched, historic', 'common, expected', 'advanced', 'society'],
        ['comprehensive',  'adjective', 'Complete and including everything that is necessary.', 'The book gives a comprehensive overview of Malaysian history.', 'thorough, exhaustive, complete', 'partial, incomplete', 'advanced', 'general'],
        ['profound',       'adjective', 'Very great or intense; showing deep insight.', 'Reading widely has had a profound impact on my thinking.', 'deep, intense, far-reaching', 'shallow, superficial', 'advanced', 'general'],
        ['inevitable',     'adjective', 'Certain to happen.', 'Change is inevitable in any growing economy.', 'unavoidable', 'avoidable', 'advanced', 'general'],
        ['discrepancy',    'noun',      'A lack of compatibility between two or more facts.', 'There was a discrepancy between the bill and the actual cost.', 'inconsistency, mismatch, gap', 'agreement, match', 'advanced', 'general'],
        ['scepticism',     'noun',      'A doubting or questioning attitude or state of mind.', 'Healthy scepticism helps us evaluate online news.', 'doubt, disbelief, cynicism', 'belief, trust', 'advanced', 'argument'],
        ['catalyst',       'noun',      'A person or thing that causes something to happen.', 'Social media was a catalyst for the protest movement.', 'trigger, spur, stimulus', 'deterrent, hindrance', 'advanced', 'society'],
        ['tenacity',       'noun',      'The quality of being determined; persistence.', 'Her tenacity helped her finish the marathon.', 'determination, persistence, grit', 'weakness, irresolution', 'advanced', 'character'],
        ['resilience',     'noun',      'The capacity to recover quickly from difficulties.', 'Resilience is the key to long-term success.', 'toughness, hardiness, flexibility', 'fragility, vulnerability', 'advanced', 'character'],
        ['empathy',        'noun',      'The ability to understand and share another\'s feelings.', 'Good leaders show empathy towards their team.', 'understanding, compassion', 'indifference, apathy', 'advanced', 'character'],
        ['articulate',     'verb',      'To express thoughts or feelings clearly.', 'Try to articulate your reasons in a single sentence.', 'express, communicate, voice', 'mumble, suppress', 'advanced', 'speaking'],
        ['advent',         'noun',      'The arrival of a notable person, thing, or event.', 'The advent of AI is reshaping every industry.', 'arrival, emergence, dawn', 'departure', 'advanced', 'technology'],
        ['paradigm',       'noun',      'A typical example or pattern of something; a model.', 'Remote learning has caused a paradigm shift in education.', 'model, framework, pattern', '', 'advanced', 'general'],
        ['imperative',     'adjective', 'Of vital importance; crucial.', 'It is imperative that we revise daily for SPM.', 'crucial, essential, vital', 'optional, unnecessary', 'advanced', 'argument'],
        ['salient',        'adjective', 'Most noticeable or important.', 'Let me summarise the salient points of the argument.', 'prominent, key, principal', 'minor, insignificant', 'advanced', 'argument'],

        // ------- basic -------
        ['polite',         'adjective', 'Showing good manners and consideration for others.', 'It is polite to greet your teachers in the morning.', 'courteous, respectful', 'rude, impolite', 'basic', 'character'],
        ['curious',        'adjective', 'Eager to know or learn something.', 'A curious mind is the start of every great discovery.', 'inquisitive, interested', 'indifferent, apathetic', 'basic', 'character'],
        ['confident',      'adjective', 'Feeling certain about one\'s abilities.', 'Practice makes you confident when speaking in front of others.', 'self-assured, sure', 'nervous, doubtful', 'basic', 'character'],
        ['support',        'verb',      'To give help or backing.', 'I will always support my friends through hard times.', 'help, back, aid', 'oppose, hinder', 'basic', 'general'],
        ['enjoy',          'verb',      'To take pleasure in something.', 'I enjoy reading novels on rainy afternoons.', 'like, love, relish', 'dislike, hate', 'basic', 'general'],
    ];
}

/** Starter idioms + phrasal verbs bank. */
function english_kssm_idioms(): array
{
    return [
        ['idiom',         'A blessing in disguise',          'Something that seems bad but turns out to be good.', 'Losing that job was a blessing in disguise — he found a better one a month later.', 'fortune'],
        ['idiom',         'Bite the bullet',                 'To do something difficult or unpleasant that has to be done.', 'I had to bite the bullet and apologise to my friend.', 'determination'],
        ['idiom',         'Break the ice',                   'To say or do something to relieve tension or awkwardness.', 'He told a joke to break the ice during the interview.', 'social'],
        ['idiom',         'Cost an arm and a leg',           'To be very expensive.', 'That new phone costs an arm and a leg.', 'money'],
        ['idiom',         'Cut corners',                     'To do something in the easiest or cheapest way, often badly.', 'Don\'t cut corners when revising — read every chapter.', 'work'],
        ['idiom',         'Hit the nail on the head',        'To describe exactly the cause of a problem.', 'You hit the nail on the head — the issue is poor time management.', 'argument'],
        ['idiom',         'Once in a blue moon',             'Very rarely.', 'My cousin visits us once in a blue moon.', 'frequency'],
        ['idiom',         'Piece of cake',                   'Something that is very easy to do.', 'The maths test was a piece of cake after I had revised.', 'ease'],
        ['idiom',         'Spill the beans',                 'To reveal a secret.', 'Don\'t spill the beans about her surprise birthday party!', 'secrets'],
        ['idiom',         'Under the weather',               'Feeling slightly unwell.', 'I won\'t come for football today — I\'m feeling under the weather.', 'health'],
        ['idiom',         'Burn the midnight oil',           'To stay up late working or studying.', 'She had to burn the midnight oil before the SPM trial exam.', 'study'],
        ['idiom',         'Beat around the bush',            'To avoid getting to the point.', 'Stop beating around the bush — just tell me what happened.', 'communication'],
        ['idiom',         'Pull yourself together',          'To calm down and behave normally.', 'After losing the match, he had to pull himself together for the next round.', 'character'],
        ['idiom',         'A drop in the ocean',             'A very small amount compared with what is needed.', 'My donation is just a drop in the ocean, but every bit helps.', 'quantity'],
        ['idiom',         'Take with a pinch of salt',       'To not completely believe something.', 'Take what you read online with a pinch of salt.', 'argument'],
        ['idiom',         'The ball is in your court',       'It is your turn to act or make a decision.', 'I\'ve sent the proposal — now the ball is in your court.', 'decision'],
        ['idiom',         'Let the cat out of the bag',      'To reveal a secret accidentally.', 'He let the cat out of the bag about the surprise party.', 'secrets'],
        ['idiom',         'On thin ice',                     'In a risky or precarious situation.', 'After his third absence, he is on thin ice with the principal.', 'risk'],

        ['phrasal_verb',  'Bring up',                        'To raise (a topic) in conversation; to rear a child.', 'She brought up an important point in the meeting.', 'discussion'],
        ['phrasal_verb',  'Carry on',                        'To continue doing something.', 'Despite the rain, the match carried on.', 'continuation'],
        ['phrasal_verb',  'Come across',                     'To find by chance; to appear or seem.', 'I came across an old photo of us last weekend.', 'discovery'],
        ['phrasal_verb',  'Give up',                         'To stop trying; to surrender.', 'Never give up on your dreams.', 'determination'],
        ['phrasal_verb',  'Look after',                      'To take care of someone or something.', 'My brother looks after our cat while we are away.', 'care'],
        ['phrasal_verb',  'Put off',                         'To postpone or delay.', 'Don\'t put off your revision until the night before.', 'time'],
        ['phrasal_verb',  'Run out of',                      'To use up the supply of something.', 'We ran out of milk this morning.', 'supply'],
        ['phrasal_verb',  'Set up',                          'To start or establish (e.g. a business).', 'They set up a small bakery during the school holidays.', 'business'],
        ['phrasal_verb',  'Take after',                      'To resemble (a family member) in appearance or character.', 'She takes after her father — both are passionate readers.', 'family'],
        ['phrasal_verb',  'Turn down',                       'To refuse or reject.', 'He turned down the job offer because of the long commute.', 'decision'],
        ['phrasal_verb',  'Look forward to',                 'To anticipate with pleasure.', 'I am looking forward to the school holidays.', 'time'],
        ['phrasal_verb',  'Get along with',                  'To have a friendly relationship with.', 'I get along well with my classmates.', 'social'],
        ['phrasal_verb',  'Make up',                         'To invent (a story); to reconcile after an argument.', 'They had a fight but made up the next day.', 'social'],
        ['phrasal_verb',  'Find out',                        'To discover or learn information.', 'I just found out that our class trip has been cancelled.', 'discovery'],
        ['phrasal_verb',  'Go through',                      'To experience (a difficult time); to examine in detail.', 'She went through a tough year but came out stronger.', 'character'],
    ];
}

/** Model writing samples used as exemplars by the AI Essay Marker. */
function english_kssm_writing_samples(): array
{
    return [
        [
            'topic'    => 'Continuous Writing (Essays)',
            'category' => 'argumentative_essay',
            'prompt'   => 'Some people believe that students should be allowed to use mobile phones in school. Do you agree? Give reasons to support your view.',
            'band'     => 'Band 5 (A-grade)',
            'notes'    => 'Strong opening hook, balanced two-sided argument, refutation paragraph, varied sentence structures, advanced vocabulary, clear concluding stance.',
            'essay'    => "In an era when nearly every teenager owns a smartphone, the question of whether students should be allowed to use mobile phones in school has become a heated debate. While critics fear distractions and academic decline, I firmly believe that the controlled use of mobile phones in schools brings far more benefits than drawbacks.\n\nFirstly, mobile phones are powerful learning tools that put a world of information at students' fingertips. Apps such as Google Translate, Photomath and LulusAI provide instant assistance, allowing learners to clarify doubts the moment they arise instead of waiting for the next lesson. In a 21st-century classroom, banning such tools is like asking a carpenter to build a house without a hammer.\n\nSecondly, mobile phones improve safety and communication. Emergencies — from sudden floods to a parent's late pick-up — can be handled swiftly when students have a way to contact their families. Schools that prohibit phones outright often force students to leave parents in the dark, which can be both inconvenient and unsafe.\n\nAdmittedly, critics argue that phones encourage distraction and cheating. These concerns are legitimate; however, the solution is not a blanket ban but clear policies. Schools in Finland, for example, allow phones only during designated tasks and have seen no fall in academic standards. The problem lies not with the device itself but with how we choose to manage it.\n\nIn conclusion, mobile phones, when used responsibly, can transform classrooms into more dynamic and inclusive learning spaces. Rather than viewing them as enemies of education, schools should embrace them as allies. After all, the goal of education is to prepare students for the real world — and that world is unmistakably digital.",
        ],
        [
            'topic'    => 'Continuous Writing (Essays)',
            'category' => 'narrative_essay',
            'prompt'   => 'Write a story beginning with: "It was the day that changed my life forever..."',
            'band'     => 'Band 5 (A-grade)',
            'notes'    => 'Strong narrative hook, vivid setting, character emotion, clear climax and resolution, varied sentence structure, dialogue used sparingly but effectively.',
            'essay'    => "It was the day that changed my life forever. The morning sun spilled gently through the curtains of my small room in Taman Melati, painting golden stripes across the worn-out textbook open on my bed. I had stayed up until two in the morning revising for the SPM trial, but the numbers and equations seemed to dance on the page, refusing to settle in my mind.\n\nAs I walked to school that day, my stomach was a tight knot of nerves. Aiman, my best friend since Form One, met me at the school gate. \"You'll do fine,\" he said, squeezing my shoulder. I forced a smile, but inside, I was certain I would fail.\n\nWhen Cikgu Aishah handed out the question papers, my hands trembled. Then, just as panic threatened to take over, I remembered something my late grandmother used to say: \"Sayang, every great mountain is climbed one small step at a time.\" I closed my eyes, breathed deeply, and began.\n\nTwo hours later, I emerged from the examination hall blinking in the bright Malaysian sunshine. I had not finished every question, but for the first time in months, I felt a quiet pride bloom in my chest. I had not given up. That was the day I learned that courage is not the absence of fear, but the willingness to begin anyway.\n\nLooking back now, that ordinary Tuesday morning was the moment everything changed. It was not the mark I eventually received that mattered most; it was the lesson — that the toughest battles are the ones we fight inside ourselves, and that even the smallest act of perseverance can shape the rest of our lives.",
        ],
        [
            'topic'    => 'Directed Writing',
            'category' => 'speech',
            'prompt'   => 'You have been invited to give a speech to your school on the importance of a healthy lifestyle. Write out the speech.',
            'band'     => 'Band 5 (A-grade)',
            'notes'    => 'Correct speech format (opening, body, close), salutations, rhetorical questions, statistics, call to action.',
            'essay'    => "A very good morning to the respected principal, dedicated teachers, and my fellow students. Today, I stand before you to talk about a topic that affects every single one of us — the importance of a healthy lifestyle.\n\nLadies and gentlemen, did you know that according to the Ministry of Health, one in two Malaysian adults is overweight or obese? More worryingly, our children and teenagers are following in their footsteps. If we do not act now, we are walking — or rather, lazing — towards a future of diabetes, heart disease and a poorer quality of life.\n\nFirstly, a healthy lifestyle improves academic performance. Sleep, exercise and balanced nutrition fuel the brain in the same way petrol fuels a car. A student who eats a balanced breakfast and sleeps seven hours will outperform one who skips meals and stays glued to a screen until midnight.\n\nSecondly, healthy habits build mental strength. Exercise releases endorphins, the natural chemicals that lift our mood and reduce stress. In a generation where mental health issues are on the rise, a simple thirty-minute walk a day can be more powerful than any motivational quote.\n\nMy dear friends, let us start small. Choose water over sugary drinks. Take the stairs instead of the lift. Sleep before midnight, at least on school nights. These tiny choices, multiplied across days and weeks, will reshape who we become.\n\nIn conclusion, our health is the greatest gift we can give ourselves. Let us not wait for the wake-up call of an illness to start making changes. Let us choose, today, to live well so that tomorrow we can live fully. Thank you.",
        ],
        [
            'topic'    => 'Summary Writing',
            'category' => 'summary',
            'prompt'   => 'Read the passage on the dangers of social media addiction (provided in the question booklet) and write a summary (about 110-120 words) on the effects and ways to overcome it.',
            'band'     => 'Band 5 (A-grade)',
            'notes'    => 'Hits the word count, paraphrases rather than copies, opens with a clear topic sentence, uses linking phrases, ends with a concluding remark.',
            'essay'    => "Social media addiction has become an alarming issue among teenagers today. According to recent studies, excessive use disrupts sleep, lowers academic performance and triggers anxiety. Constant comparison with the curated lives of others damages self-esteem, while doom-scrolling steals away time that could be spent on hobbies, exercise or face-to-face relationships. To overcome this addiction, experts recommend setting a daily screen-time limit and switching off notifications at night. Practising offline hobbies such as reading, sport or volunteer work also helps to redirect attention. Equally important, parents and teachers should engage in open conversations rather than impose harsh bans. Through self-discipline and a balanced lifestyle, we can transform social media from a master into a useful tool. (119 words)",
        ],
        [
            'topic'    => 'Short Communicative Writing',
            'category' => 'informal_email',
            'prompt'   => 'Write an informal email to your friend telling them about your recent school trip to Penang.',
            'band'     => 'Band 4 (B-grade)',
            'notes'    => 'Informal greeting + closing, friendly tone, three short paragraphs, specific details, simple but accurate grammar.',
            'essay'    => "Subject: Our amazing Penang trip!\n\nHi Aliya,\n\nHow have you been? I just got back from our Form 4 school trip to Penang and I had to tell you about it. We spent three whole days exploring the island, and I can honestly say it was one of the best experiences of the year.\n\nOn the first day, we visited George Town's heritage streets and took photos of the famous street art. The 'Little Children on a Bicycle' mural was even cuter in real life! Of course, we had to try the food too — I am still dreaming about that bowl of Penang laksa. Day two was the most exciting because we hiked up Penang Hill at sunrise and the view at the top was unbelievable.\n\nI wish you had come along — it just wasn't the same without you. Let's plan a trip together during the school holidays. Reply soon and let me know your dates!\n\nMiss you lots,\nNurul",
        ],
    ];
}

function english_kssm_run(): array
{
    $subject = db_one("SELECT id FROM subjects WHERE slug = 'english' LIMIT 1");
    if (!$subject) {
        return ['status' => 'no_subject', 'message' => 'English subject not found. Run install / seed_subjects first.'];
    }
    $sid = (int) $subject['id'];

    $vocabTable  = (bool) db_one("SELECT 1 AS x FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'english_vocabulary'");
    $idiomsTable = (bool) db_one("SELECT 1 AS x FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'english_idioms'");
    $samplesTable = (bool) db_one("SELECT 1 AS x FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'english_writing_samples'");

    $catalog = english_kssm_catalog();
    $topicsCreated = $topicsKept = $topicsAdopted = 0;
    $subCreated = $subKept = 0;
    $skillsCreated = $skillsKept = 0;
    $vocabCreated = $vocabKept = 0;
    $idiomsCreated = $idiomsKept = 0;
    $samplesCreated = $samplesKept = 0;

    $topicIds = []; // [name => id]

    foreach ($catalog as $i => $t) {
        $name = $t['name'];
        $form = (int) $t['form'];
        $sortOrder = ($form === 4 ? 0 : 100) + $i;

        $existing = db_one(
            'SELECT id FROM topics WHERE subject_id = ? AND name = ? AND form_level <=> ? LIMIT 1',
            [$sid, $name, $form]
        );
        if ($existing) {
            $tid = (int) $existing['id'];
            $topicsKept++;
        } else {
            $legacy = db_one(
                'SELECT id FROM topics WHERE subject_id = ? AND name = ? AND form_level IS NULL LIMIT 1',
                [$sid, $name]
            );
            if ($legacy) {
                $tid = (int) $legacy['id'];
                db_exec('UPDATE topics SET form_level = ? WHERE id = ?', [$form, $tid]);
                $topicsAdopted++;
            } else {
                $slug = preg_replace('/[^a-z0-9]+/', '-', strtolower($name . ' f' . $form));
                $tid  = db_exec(
                    'INSERT INTO topics (subject_id, form_level, name, slug, sort_order) VALUES (?, ?, ?, ?, ?)',
                    [$sid, $form, $name, $slug, $sortOrder]
                );
                $topicsCreated++;
            }
        }
        if (!isset($topicIds[$name])) {
            $topicIds[$name] = $tid;
        }

        foreach ($t['subtopics'] as $idx => $subName) {
            $sub = db_one('SELECT id FROM subtopics WHERE topic_id = ? AND name = ? LIMIT 1', [$tid, $subName]);
            if ($sub) {
                $subKept++;
            } else {
                db_exec('INSERT INTO subtopics (topic_id, name, sort_order) VALUES (?, ?, ?)', [$tid, $subName, $idx + 1]);
                $subCreated++;
            }
        }

        foreach ($t['skills'] as [$skillName, $diff]) {
            $existing = db_one('SELECT id FROM skills WHERE topic_id = ? AND name = ? LIMIT 1', [$tid, $skillName]);
            if ($existing) {
                $skillsKept++;
            } else {
                db_exec('INSERT INTO skills (topic_id, name, difficulty) VALUES (?, ?, ?)', [$tid, $skillName, $diff]);
                $skillsCreated++;
            }
        }
    }

    if ($vocabTable) {
        foreach (english_kssm_vocabulary() as $idx => [$word, $pos, $meaning, $example, $syn, $ant, $level, $theme]) {
            $existing = db_one('SELECT id FROM english_vocabulary WHERE word = ? LIMIT 1', [$word]);
            if ($existing) {
                $vocabKept++;
            } else {
                db_exec(
                    'INSERT INTO english_vocabulary (word, part_of_speech, meaning, example, synonyms, antonyms, level, theme, sort_order)
                     VALUES (?,?,?,?,?,?,?,?,?)',
                    [$word, $pos, $meaning, $example, $syn, $ant, $level, $theme, $idx + 1]
                );
                $vocabCreated++;
            }
        }
    }

    if ($idiomsTable) {
        foreach (english_kssm_idioms() as $idx => [$type, $expr, $meaning, $example, $theme]) {
            $existing = db_one('SELECT id FROM english_idioms WHERE expression = ? LIMIT 1', [$expr]);
            if ($existing) {
                $idiomsKept++;
            } else {
                db_exec(
                    'INSERT INTO english_idioms (type, expression, meaning, example, theme, sort_order) VALUES (?,?,?,?,?,?)',
                    [$type, $expr, $meaning, $example, $theme, $idx + 1]
                );
                $idiomsCreated++;
            }
        }
    }

    if ($samplesTable) {
        foreach (english_kssm_writing_samples() as $idx => $s) {
            $tid = $topicIds[$s['topic']] ?? null;
            $existing = db_one(
                'SELECT id FROM english_writing_samples WHERE topic_id <=> ? AND prompt = ? LIMIT 1',
                [$tid, $s['prompt']]
            );
            if ($existing) {
                $samplesKept++;
            } else {
                db_exec(
                    'INSERT INTO english_writing_samples (topic_id, topic_label, category, prompt, model_essay, band, notes, sort_order)
                     VALUES (?,?,?,?,?,?,?,?)',
                    [$tid, $s['topic'], $s['category'], $s['prompt'], $s['essay'], $s['band'], $s['notes'], $idx + 1]
                );
                $samplesCreated++;
            }
        }
    }

    return [
        'status'             => 'ok',
        'topics_created'     => $topicsCreated,
        'topics_kept'        => $topicsKept,
        'topics_adopted'     => $topicsAdopted,
        'subtopics_created'  => $subCreated,
        'subtopics_kept'     => $subKept,
        'skills_created'     => $skillsCreated,
        'skills_kept'        => $skillsKept,
        'vocab_created'      => $vocabCreated,
        'vocab_kept'         => $vocabKept,
        'idioms_created'     => $idiomsCreated,
        'idioms_kept'        => $idiomsKept,
        'samples_created'    => $samplesCreated,
        'samples_kept'       => $samplesKept,
        'catalog_topics'     => count($catalog),
        'vocab_table'        => $vocabTable,
        'idioms_table'       => $idiomsTable,
        'samples_table'      => $samplesTable,
    ];
}

if (PHP_SAPI === 'cli') {
    $r = english_kssm_run();
    if (($r['status'] ?? '') !== 'ok') {
        fwrite(STDERR, ($r['message'] ?? 'unknown error') . "\n");
        exit(1);
    }
    echo "KSSM English seeded —\n";
    echo "  Topics:     created {$r['topics_created']}, adopted {$r['topics_adopted']}, kept {$r['topics_kept']} (catalog {$r['catalog_topics']})\n";
    echo "  Subtopics:  created {$r['subtopics_created']}, kept {$r['subtopics_kept']}\n";
    echo "  Skills:     created {$r['skills_created']}, kept {$r['skills_kept']}\n";
    if ($r['vocab_table']) {
        echo "  Vocabulary: created {$r['vocab_created']}, kept {$r['vocab_kept']}\n";
    } else {
        echo "  Vocabulary: table missing — run database migrations to enable.\n";
    }
    if ($r['idioms_table']) {
        echo "  Idioms:     created {$r['idioms_created']}, kept {$r['idioms_kept']}\n";
    } else {
        echo "  Idioms:     table missing — run database migrations to enable.\n";
    }
    if ($r['samples_table']) {
        echo "  Samples:    created {$r['samples_created']}, kept {$r['samples_kept']}\n";
    } else {
        echo "  Samples:    table missing — run database migrations to enable.\n";
    }
}
