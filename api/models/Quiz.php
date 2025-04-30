<?php

class Quiz {
    private $quiz_id;
    private $quiz_code;
    private $title;
    private $created_at;
    private $dbConnection;
    private $dbTable = 'quizzes';

    public function __construct($dbConnection) {
        $this->dbConnection = $dbConnection;
    }

    // Getters
    public function getQuizId() {
        return $this->quiz_id;
    }
    
    public function getQuizCode() {
        return $this->quiz_code;
    }
    
    public function getTitle() {
        return $this->title;
    }
    
    public function getCreatedAt() {
        return $this->created_at;
    }

    // Setters
    public function setQuizId($quiz_id) {
        $this->quiz_id = $quiz_id;
    }
    
    public function setQuizCode($quiz_code) {
        $this->quiz_code = $quiz_code;
    }
    
    public function setTitle($title) {
        $this->title = $title;
    }
    
    public function setCreatedAt($created_at) {
        $this->created_at = $created_at;
    }

    // CRUD Operations
    public function create() {
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

    public function readOne() {
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

    public function readAll() {
        $query = "SELECT * FROM " . $this->dbTable;
        $stmt = $this->dbConnection->prepare($query);
        if ($stmt->execute() && $stmt->rowCount() > 0) {
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        return [];
    }

    public function update() {
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

    public function delete() {
        $query = "DELETE FROM " . $this->dbTable . " WHERE quiz_id=:quiz_id";
        $stmt = $this->dbConnection->prepare($query);
        $stmt->bindParam(":quiz_id", $this->quiz_id);
        if ($stmt->execute() && $stmt->rowCount() == 1) {
            return true;
        }
        return false;
    }
}