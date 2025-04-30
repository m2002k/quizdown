<?php
require_once __DIR__ . '/../db/Database.php';
require_once __DIR__ . '/../models/Quiz.php';
require_once __DIR__ . '/../models/Question.php';
require_once __DIR__ . '/../models/Option.php';
require_once __DIR__ . '/../models/Submission.php';

class StudentController {
    private $db;
    private $connection;

    public function __construct() {
        $this->db = new Database();
        $this->connection = $this->db->connect();
    }

    public function getQuiz($quiz_code) {
        try {
            // Get quiz details
            $quiz = new Quiz($this->connection);
            $query = "SELECT * FROM quizzes WHERE quiz_code = :quiz_code";
            $stmt = $this->connection->prepare($query);
            $stmt->bindParam(":quiz_code", $quiz_code);
            
            if (!$stmt->execute() || $stmt->rowCount() == 0) {
                return ["error" => "Quiz not found", "code" => 404];
            }

            $quizData = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Get questions
            $question = new Question($this->connection);
            $query = "SELECT * FROM questions WHERE quiz_id = :quiz_id";
            $stmt = $this->connection->prepare($query);
            $stmt->bindParam(":quiz_id", $quizData['quiz_id']);
            $stmt->execute();
            $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Get options for each question
            $option = new Option($this->connection);
            foreach ($questions as &$q) {
                $query = "SELECT option_id, option_text FROM options WHERE question_id = :question_id";
                $stmt = $this->connection->prepare($query);
                $stmt->bindParam(":question_id", $q['question_id']);
                $stmt->execute();
                $q['options'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
            // Validate quiz exists
            $quiz = new Quiz($this->connection);
            $query = "SELECT quiz_id FROM quizzes WHERE quiz_code = :quiz_code";
            $stmt = $this->connection->prepare($query);
            $stmt->bindParam(":quiz_code", $quiz_code);
            
            if (!$stmt->execute() || $stmt->rowCount() == 0) {
                return ["error" => "Quiz not found", "code" => 404];
            }
            
            $quizData = $stmt->fetch(PDO::FETCH_ASSOC);
            $quiz_id = $quizData['quiz_id'];

            // Begin transaction
            $this->connection->beginTransaction();

            $submission = new Submission($this->connection);
            foreach ($answers as $answer) {
                // Validate answer
                if (!isset($answer['question_id']) || !isset($answer['selected_option_id'])) {
                    $this->connection->rollBack();
                    return ["error" => "Invalid answer format", "code" => 400];
                }

                // Check if selected option is correct
                $query = "SELECT is_correct FROM options WHERE option_id = :option_id AND question_id = :question_id";
                $stmt = $this->connection->prepare($query);
                $stmt->bindParam(":option_id", $answer['selected_option_id']);
                $stmt->bindParam(":question_id", $answer['question_id']);
                $stmt->execute();

                if ($stmt->rowCount() == 0) {
                    $this->connection->rollBack();
                    return ["error" => "Invalid option ID", "code" => 400];
                }

                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                
                // Record submission
                $submission->setQuizId($quiz_id);
                $submission->setQuestionId($answer['question_id']);
                $submission->setIsCorrect($result['is_correct']);
                
                if (!$submission->create()) {
                    $this->connection->rollBack();
                    return ["error" => "Failed to save submission", "code" => 500];
                }
            }

            $this->connection->commit();
            return ["message" => "Quiz submitted successfully", "code" => 200];

        } catch (PDOException $e) {
            if ($this->connection->inTransaction()) {
                $this->connection->rollBack();
            }
            return ["error" => "Database error: " . $e->getMessage(), "code" => 500];
        }
    }
}