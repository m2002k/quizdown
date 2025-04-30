<?php
require_once __DIR__ . '/../db/Database.php';
require_once __DIR__ . '/../models/Quiz.php';
require_once __DIR__ . '/../models/Submission.php';

class InstructorController
{
    private $connection;

    public function __construct()
    {
        $db = new Database();
        $this->connection = $db->connect();
    }

    public function getQuizStats($quiz_code)
    {
        try {
            // Get quiz ID using Quiz model
            $quiz = new Quiz($this->connection);
            $quizData = $quiz->findByCode($quiz_code);

            if (!$quizData) {
                return ["error" => "Quiz not found", "code" => 404];
            }

            // Get statistics using Question model
            $question = new Question($this->connection);
            $stats = $question->getQuestionStats($quizData['quiz_id']);

            return [
                "quiz_code" => $quiz_code,
                "statistics" => $stats,
                "code" => 200
            ];
        } catch (PDOException $e) {
            return ["error" => "Database error: " . $e->getMessage(), "code" => 500];
        }
    }
}
