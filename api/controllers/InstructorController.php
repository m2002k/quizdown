<?php
require_once __DIR__ . '/../db/Database.php';
require_once __DIR__ . '/../models/Quiz.php';
require_once __DIR__ . '/../models/Submission.php';

class InstructorController {
    private $db;
    private $connection;

    public function __construct() {
        $this->db = new Database();
        $this->connection = $this->db->connect();
    }

    public function getQuizStats($quiz_code) {
        try {
            // Get quiz ID
            $quiz = new Quiz($this->connection);
            $query = "SELECT quiz_id FROM quizzes WHERE quiz_code = :quiz_code";
            $stmt = $this->connection->prepare($query);
            $stmt->bindParam(":quiz_code", $quiz_code);
            
            if (!$stmt->execute() || $stmt->rowCount() == 0) {
                return ["error" => "Quiz not found", "code" => 404];
            }

            $quizData = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Get statistics for each question
            $query = "
                SELECT 
                    q.question_id,
                    q.question_text,
                    COUNT(s.submission_id) as total_submissions,
                    SUM(CASE WHEN s.is_correct THEN 1 ELSE 0 END) as correct_answers,
                    SUM(CASE WHEN NOT s.is_correct THEN 1 ELSE 0 END) as incorrect_answers,
                    ROUND(CAST(SUM(CASE WHEN s.is_correct THEN 1 ELSE 0 END) AS FLOAT) / 
                          CAST(COUNT(s.submission_id) AS FLOAT) * 100, 2) as correct_percentage
                FROM questions q
                LEFT JOIN submissions s ON q.question_id = s.question_id
                WHERE q.quiz_id = :quiz_id
                GROUP BY q.question_id, q.question_text
                ORDER BY q.question_id
            ";
            
            $stmt = $this->connection->prepare($query);
            $stmt->bindParam(":quiz_id", $quizData['quiz_id']);
            $stmt->execute();
            
            $stats = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
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