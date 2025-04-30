# QuizDown

Quickly create questions in markdown. This project is made up of the following components:
 - PostgreSQL as a database to store all related quiz data (questions, answers, and submissions,etc.).
 - PHP for the backend API server.
 - React for the front end web app.

## Usage


### Step 1: Run the database server and the database client/GUI
- Install Docker for Desktop and run:
  - Alternatively, you may skip Docker and download and run PostgreSQL standalone server and PgAdmin4.

```shell
docker compose up
```
### Step 2: Create the database and populate the tables with data
- Go to [http://localhost:5050](http://localhost:5050), connect to the database server using the credeintals at the `.env` file.
- Select the database and open the Query tool
- Execute the SQL code at `db/create_tables.sql`.

1. Create a database, a table, and populate it with data:

```sql
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


```
2. Set the database server credentials up by setting environment variables. You should never store sensitive database credentials in your source code. Instead, declare the following environment variables:

```shell
POSTGRES_HOST=postgres
POSTGRES_USER=postgres
POSTGRES_PASSWORD=postgres
POSTGRES_DB=quizDB
POSTGRES_PORT=5432
```

For example to set these environment variables on windows run:
    
```powershell
$env:POSTGRES_HOST=postgres
$env:POSTGRES_PORT=5432
$env:POSTGRES_DB=quizDB
$env:POSTGRES_USER=postgres
$env:POSTGRES_PASSWORD=postgres
```

while on macOs and Linux run:

```shell
export POSTGRES_HOST=postgres
export POSTGRES_PORT=5432
export POSTGRES_DB=quizDB
export POSTGRES_USER=postgres
export POSTGRES_PASSWORD=postgres
```

3. Run the API server:
```shell
  php -S localhost:8000
```



### API Server

The API server is written in PHP and has the following API endpoints:
1. Student Endpoints:

- `GET /api/quiz/{quiz_code}`
  - Returns: Quiz title, questions, and answer options

- Open an HTTP client like Postman and send an HTTP GET request to `http://localhost:8000/api/quiz/PHP101`

- You should receive the response as JSON.



