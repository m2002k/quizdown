<?php

class Question {
    private $question_id;
    private $quiz_id;
    private $question_text;
    private $created_at;
    private $dbConnection;
    private $dbTable = 'questions';

    public function __construct($dbConnection) {
        $this->dbConnection = $dbConnection;
    }

    // Getters
    public function getQuestionId() {
        return $this->question_id;
    }
    
    public function getQuizId() {
        return $this->quiz_id;
    }
    
    public function getQuestionText() {
        return $this->question_text;
    }
    
    public function getCreatedAt() {
        return $this->created_at;
    }

    // Setters
    public function setQuestionId($question_id) {
        $this->question_id = $question_id;
    }
    
    public function setQuizId($quiz_id) {
        $this->quiz_id = $quiz_id;
    }
    
    public function setQuestionText($question_text) {
        $this->question_text = $question_text;
    }
    
    public function setCreatedAt($created_at) {
        $this->created_at = $created_at;
    }

    // CRUD Operations
    public function create() {
        $query = "INSERT INTO " . $this->dbTable . "(quiz_id, question_text) VALUES(:quiz_id, :question_text)";
        $stmt = $this->dbConnection->prepare($query);
        $stmt->bindParam(":quiz_id", $this->quiz_id);
        $stmt->bindParam(":question_text", $this->question_text);
        if ($stmt->execute()) {
            return true;
        }
        printf("Error: %s", $stmt->error);
        return false;
    }

    public function readOne() {
        $query = "SELECT * FROM " . $this->dbTable . " WHERE question_id=:question_id";
        $stmt = $this->dbConnection->prepare($query);
        $stmt->bindParam(":question_id", $this->question_id);
        if ($stmt->execute() && $stmt->rowCount() == 1) {
            $result = $stmt->fetch(PDO::FETCH_OBJ);
            $this->question_id = $result->question_id;
            $this->quiz_id = $result->quiz_id;
            $this->question_text = $result->question_text;
            $this->created_at = $result->created_at;
            return true;
        }
        return false;
    }

    public function readAll() {
        $query = "SELECT * FROM " . $this->dbTable;
        $stmt = $this->dbConnection->prepare($query);
        if ($stmt->execute() && $stmt->rowCount() > 0) {
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        return [];
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
        $query = "UPDATE " . $this->dbTable . " SET quiz_id=:quiz_id, question_text=:question_text WHERE question_id=:question_id";
        $stmt = $this->dbConnection->prepare($query);
        $stmt->bindParam(":quiz_id", $this->quiz_id);
        $stmt->bindParam(":question_text", $this->question_text);
        $stmt->bindParam(":question_id", $this->question_id);
        if ($stmt->execute() && $stmt->rowCount() == 1) {
            return true;
        }
        return false;
    }

    public function delete() {
        $query = "DELETE FROM " . $this->dbTable . " WHERE question_id=:question_id";
        $stmt = $this->dbConnection->prepare($query);
        $stmt->bindParam(":question_id", $this->question_id);
        if ($stmt->execute() && $stmt->rowCount() == 1) {
            return true;
        }
        return false;
    }
}