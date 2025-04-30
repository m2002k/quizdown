<?php
require_once __DIR__ . '/../db/Database.php';
require_once __DIR__ . '/../models/Quiz.php';
require_once __DIR__ . '/../models/Question.php';
require_once __DIR__ . '/../models/Option.php';
require_once __DIR__ . '/../models/Submission.php';

class StudentController {
    private $connection;

    public function __construct() {
        $db = new Database();
        $this->connection = $db->connect();
    }

    public function getQuiz($quiz_code) {
        try {
            // Get quiz details
            $quiz = new Quiz($this->connection);
            $quizData = $quiz->findByCode($quiz_code);
            
            if (!$quizData) {
                return ["error" => "Quiz not found", "code" => 404];
            }

            // Get questions
            $question = new Question($this->connection);
            $questions = $question->getQuestionsByQuizId($quizData['quiz_id']);

            // Get options for each question
            $option = new Option($this->connection);
            foreach ($questions as &$q) {
                $q['options'] = $option->getOptionsByQuestionId($q['question_id']);
            }

            return [
                "quiz_id" => $quizData['quiz_id'],
                "title" => $quizData['title'],
                "questions" => $questions
            ];

        } catch (PDOException $e) {
            return ["error" => "Database error: " . $e->getMessage(), "code" => 500];
        }
    }

    public function submitQuiz($quiz_code, $answers) {
        try {
            $quiz = new Quiz($this->connection);
            return $quiz->submitQuiz($quiz_code, $answers);
        } catch (PDOException $e) {
            return ["error" => "Database error: " . $e->getMessage(), "code" => 500];
        }
    }
}