<?php

class Quiz
{
    private $quiz_id;
    private $quiz_code;
    private $title;
    private $created_at;
    private $dbConnection;
    private $dbTable = 'quizzes';

    public function __construct($dbConnection)
    {
        $this->dbConnection = $dbConnection;
    }

    // Getters
    public function getQuizId()
    {
        return $this->quiz_id;
    }

    public function getQuizCode()
    {
        return $this->quiz_code;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getCreatedAt()
    {
        return $this->created_at;
    }

    // Setters
    public function setQuizId($quiz_id)
    {
        $this->quiz_id = $quiz_id;
    }

    public function setQuizCode($quiz_code)
    {
        $this->quiz_code = $quiz_code;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function setCreatedAt($created_at)
    {
        $this->created_at = $created_at;
    }

    // CRUD Operations
    public function create()
    {
        $query = "INSERT INTO " . $this->dbTable . "(quiz_code, title) VALUES(:quiz_code, :title)";
        $stmt = $this->dbConnection->prepare($query);
        $stmt->bindParam(":quiz_code", $this->quiz_code);
        $stmt->bindParam(":title", $this->title);
        if ($stmt->execute()) {
            return true;
        }
        printf("Error: %s", $stmt->error);
        return false;
    }
    // Find Quiz by ID
    public function findById()
    {
        $query = "SELECT * FROM " . $this->dbTable . " WHERE quiz_id=:quiz_id";
        $stmt = $this->dbConnection->prepare($query);
        $stmt->bindParam(":quiz_id", $this->quiz_id);
        if ($stmt->execute() && $stmt->rowCount() == 1) {
            $result = $stmt->fetch(PDO::FETCH_OBJ);
            $this->quiz_id = $result->quiz_id;
            $this->quiz_code = $result->quiz_code;
            $this->title = $result->title;
            $this->created_at = $result->created_at;
            return true;
        }
        return false;
    }

    // Find quiz by code
    public function findByCode($quiz_code)
    {
        $query = "SELECT * FROM " . $this->dbTable . " WHERE quiz_code = :quiz_code";
        $stmt = $this->dbConnection->prepare($query);
        $stmt->bindParam(":quiz_code", $quiz_code);

        if ($stmt->execute() && $stmt->rowCount() > 0) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return null;
    }

    public function findAll()
    {
        $query = "SELECT * FROM " . $this->dbTable;
        $stmt = $this->dbConnection->prepare($query);
        if ($stmt->execute() && $stmt->rowCount() > 0) {
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        return [];
    }

    public function update()
    {
        $query = "UPDATE " . $this->dbTable . " SET quiz_code=:quiz_code, title=:title WHERE quiz_id=:quiz_id";
        $stmt = $this->dbConnection->prepare($query);
        $stmt->bindParam(":quiz_code", $this->quiz_code);
        $stmt->bindParam(":title", $this->title);
        $stmt->bindParam(":quiz_id", $this->quiz_id);
        if ($stmt->execute() && $stmt->rowCount() == 1) {
            return true;
        }
        return false;
    }

    public function delete()
    {
        $query = "DELETE FROM " . $this->dbTable . " WHERE quiz_id=:quiz_id";
        $stmt = $this->dbConnection->prepare($query);
        $stmt->bindParam(":quiz_id", $this->quiz_id);
        if ($stmt->execute() && $stmt->rowCount() == 1) {
            return true;
        }
        return false;
    }
    // handle quiz submission
    public function submitQuiz($quiz_code, $answers)
    {
        try {
            // Begin transaction
            $this->dbConnection->beginTransaction();

            // Find quiz
            $quizData = $this->findByCode($quiz_code);
            if (!$quizData) {
                $this->dbConnection->rollBack();
                return ["error" => "Quiz not found", "code" => 404];
            }

            $option = new Option($this->dbConnection);
            $submission = new Submission($this->dbConnection);

            foreach ($answers as $answer) {
                if (!isset($answer['question_id']) || !isset($answer['selected_option_id'])) {
                    $this->dbConnection->rollBack();
                    return ["error" => "Invalid answer format", "code" => 400];
                }

                // Validate option
                $result = $option->validateOption($answer['selected_option_id'], $answer['question_id']);
                if ($result === null) {
                    $this->dbConnection->rollBack();
                    return ["error" => "Invalid option ID", "code" => 400];
                }

                // Record submission
                $submission->setQuizId($quizData['quiz_id']);
                $submission->setQuestionId($answer['question_id']);
                $submission->setIsCorrect($result['is_correct']);

                if (!$submission->create()) {
                    $this->dbConnection->rollBack();
                    return ["error" => "Failed to save submission", "code" => 500];
                }
            }

            $this->dbConnection->commit();
            return ["message" => "Quiz submitted successfully", "code" => 200];
        } catch (PDOException $e) {
            if ($this->dbConnection->inTransaction()) {
                $this->dbConnection->rollBack();
            }
            throw $e;
        }
    }
}
