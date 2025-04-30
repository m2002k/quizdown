-- Quiz table
CREATE TABLE IF NOT EXISTS quizzes (
    quiz_id SERIAL PRIMARY KEY,
    quiz_code VARCHAR(10) UNIQUE NOT NULL,
    title VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Questions table
CREATE TABLE IF NOT EXISTS questions (
    question_id SERIAL PRIMARY KEY,
    quiz_id INTEGER REFERENCES quizzes(quiz_id),
    question_text TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Options table
CREATE TABLE IF NOT EXISTS options (
    option_id SERIAL PRIMARY KEY,
    question_id INTEGER REFERENCES questions(question_id),
    option_text TEXT NOT NULL,
    is_correct BOOLEAN NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Submissions table
CREATE TABLE IF NOT EXISTS submissions (
    submission_id SERIAL PRIMARY KEY,
    quiz_id INTEGER REFERENCES quizzes(quiz_id),
    question_id INTEGER REFERENCES questions(question_id),
    is_correct BOOLEAN NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert quiz
INSERT INTO quizzes (quiz_code, title) 
VALUES ('PHP101', 'PHP Basics: String Concatenation and Global Variables');

-- Insert questions
INSERT INTO questions (quiz_id, question_text) 
VALUES 
    (1, 'Which operator is used to concatenate strings in PHP?'),
    (1, 'How do you correctly access a global variable inside a function in PHP?');

-- Insert options for question 1 (concatenation)
INSERT INTO options (question_id, option_text, is_correct) 
VALUES 
    (1, 'The dot (.) operator', true),
    (1, 'The plus (+) operator', false),
    (1, 'The ampersand (&) operator', false),
    (1, 'The comma (,) operator', false);

-- Insert options for question 2 (global variables)
INSERT INTO options (question_id, option_text, is_correct) 
VALUES 
    (2, 'Use the $GLOBALS array: $GLOBALS[''varname'']', true),
    (2, 'Just use the variable name directly', false),
    (2, 'Use the @global keyword', false),
    (2, 'Use the import keyword', false);

-- Insert some sample submissions
INSERT INTO submissions (quiz_id, question_id, is_correct) 
VALUES 
    (1, 1, true),
    (1, 2, false);

