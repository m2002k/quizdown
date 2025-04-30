# QuizDown

Quickly create questions in markdown.

## Usage

- Install Docker for Desktop and run:

```shell
docker compose up
```
### Web App

This is a React web app.

```shell
cd web-app
npm i
npm run dev
```

### API Server

The API server is written in PHP and has the following API endpoints:
1. Student Endpoints:

- `GET /api/quiz/{quiz_code}`
  - Validates quiz code and returns quiz details with questions and options
  - Returns: Quiz title, questions, and answer options
- `POST /api/quiz/{quiz_code}/submit`
  - Submit answers for a quiz
  - Body: Array of {question_id, selected_option_id}

2. Instructor Endpoints:

- `GET /api/quiz/{quiz_code}/stats`
  - Get aggregated statistics for each question
  - Returns: For each question:
    - Total number of submissions
    - Number of correct answers
    - Number of incorrect answers
    - Percentage of correct answers

### Database

- Go to [http://localhost:5050](http://localhost:5050)
- Connect to the database server using the following credentials:
  - host: localhost
  - port: 5432
  - username: postgres
  - password: postgres
- Open the Query tool and enter th SQL code at `db/create_tables.sql`.


