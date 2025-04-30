<?php

class Submission {
    private $submission_id;
    private $quiz_id;
    private $question_id;
    private $is_correct;
    private $created_at;
    private $dbConnection;
    private $dbTable = 'submissions';

    public function __construct($dbConnection) {
        $this->dbConnection = $dbConnection;
    }

    // Getters
    public function getSubmissionId() {
        return $this->submission_id;
    }
    
    public function getQuizId() {
        return $this->quiz_id;
    }
    
    public function getQuestionId() {
        return $this->question_id;
    }
    
    public function getIsCorrect() {
        return $this->is_correct;
    }
    
    public function getCreatedAt() {
        return $this->created_at;
    }

    // Setters
    public function setSubmissionId($submission_id) {
        $this->submission_id = $submission_id;
    }
    
    public function setQuizId($quiz_id) {
        $this->quiz_id = $quiz_id;
    }
    
    public function setQuestionId($question_id) {
        $this->question_id = $question_id;
    }
    
    public function setIsCorrect($is_correct) {
        $this->is_correct = $is_correct;
    }
    
    public function setCreatedAt($created_at) {
        $this->created_at = $created_at;
    }

    // CRUD Operations
    public function create() {
        $query = "INSERT INTO " . $this->dbTable . "(quiz_id, question_id, is_correct) VALUES(:quiz_id, :question_id, :is_correct)";
        $stmt = $this->dbConnection->prepare($query);
        $stmt->bindParam(":quiz_id", $this->quiz_id);
        $stmt->bindParam(":question_id", $this->question_id);
        $stmt->bindParam(":is_correct", $this->is_correct);
        if ($stmt->execute()) {
            return true;
        }
        printf("Error: %s", $stmt->error);
        return false;
    }

    public function readOne() {
        $query = "SELECT * FROM " . $this->dbTable . " WHERE submission_id=:submission_id";
        $stmt = $this->dbConnection->prepare($query);
        $stmt->bindParam(":submission_id", $this->submission_id);
        if ($stmt->execute() && $stmt->rowCount() == 1) {
            $result = $stmt->fetch(PDO::FETCH_OBJ);
            $this->submission_id = $result->submission_id;
            $this->quiz_id = $result->quiz_id;
            $this->question_id = $result->question_id;
            $this->is_correct = $result->is_correct;
            $this->created_at = $result->created_at;
            return true;
        }
        return false;
    }

    public function readByQuizId() {
        $query = "SELECT * FROM " . $this->dbTable . " WHERE quiz_id=:quiz_id";
        $stmt = $this->dbConnection->prepare($query);
        $stmt->bindParam(":quiz_id", $this->quiz_id);
        if ($stmt->execute() && $stmt->rowCount() > 0) {
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        return [];
    }

    public function update() {
        $query = "UPDATE " . $this->dbTable . " SET quiz_id=:quiz_id, question_id=:question_id, is_correct=:is_correct WHERE submission_id=:submission_id";
        $stmt = $this->dbConnection->prepare($query);
        $stmt->bindParam(":quiz_id", $this->quiz_id);
        $stmt->bindParam(":question_id", $this->question_id);
        $stmt->bindParam(":is_correct", $this->is_correct);
        $stmt->bindParam(":submission_id", $this->submission_id);
        if ($stmt->execute() && $stmt->rowCount() == 1) {
            return true;
        }
        return false;
    }

    public function delete() {
        $query = "DELETE FROM " . $this->dbTable . " WHERE submission_id=:submission_id";
        $stmt = $this->dbConnection->prepare($query);
        $stmt->bindParam(":submission_id", $this->submission_id);
        if ($stmt->execute() && $stmt->rowCount() == 1) {
            return true;
        }
        return false;
    }
}