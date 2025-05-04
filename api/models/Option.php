<?php

class Option {
    private $option_id;
    private $question_id;
    private $option_text;
    private $is_correct;
    private $created_at;
    private $dbConnection;
    private $dbTable = 'options';

    public function __construct($dbConnection) {
        $this->dbConnection = $dbConnection;
    }

    // Getters
    public function getOptionId() {
        return $this->option_id;
    }
    
    public function getQuestionId() {
        return $this->question_id;
    }
    
    public function getOptionText() {
        return $this->option_text;
    }
    
    public function getIsCorrect() {
        return $this->is_correct;
    }
    
    public function getCreatedAt() {
        return $this->created_at;
    }

    // Setters
    public function setOptionId($option_id) {
        $this->option_id = $option_id;
    }
    
    public function setQuestionId($question_id) {
        $this->question_id = $question_id;
    }
    
    public function setOptionText($option_text) {
        $this->option_text = $option_text;
    }
    
    public function setIsCorrect($is_correct) {
        $this->is_correct = $is_correct;
    }
    
    public function setCreatedAt($created_at) {
        $this->created_at = $created_at;
    }

    // CRUD Operations
    public function create() {
        $query = "INSERT INTO " . $this->dbTable . "(question_id, option_text, is_correct) VALUES(:question_id, :option_text, :is_correct)";
        $stmt = $this->dbConnection->prepare($query);
        $stmt->bindParam(":question_id", $this->question_id);
        $stmt->bindParam(":option_text", $this->option_text);
        $stmt->bindParam(":is_correct", $this->is_correct);
        if ($stmt->execute()) {
            return true;
        }
        printf("Error: %s", $stmt->error);
        return false;
    }

    public function readOne() {
        $query = "SELECT * FROM " . $this->dbTable . " WHERE option_id=:option_id";
        $stmt = $this->dbConnection->prepare($query);
        $stmt->bindParam(":option_id", $this->option_id);
        if ($stmt->execute() && $stmt->rowCount() == 1) {
            $result = $stmt->fetch(PDO::FETCH_OBJ);
            $this->option_id = $result->option_id;
            $this->question_id = $result->question_id;
            $this->option_text = $result->option_text;
            $this->is_correct = $result->is_correct;
            $this->created_at = $result->created_at;
            return true;
        }
        return false;
    }

    public function getOptionsByQuestionId($question_id) {
        $query = "SELECT * FROM " . $this->dbTable . " WHERE question_id=:question_id";
        $stmt = $this->dbConnection->prepare($query);
        $stmt->bindParam(":question_id", $question_id);
        if ($stmt->execute() && $stmt->rowCount() > 0) {
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        return [];
    }

    public function update() {
        $query = "UPDATE " . $this->dbTable . " SET question_id=:question_id, option_text=:option_text, is_correct=:is_correct WHERE option_id=:option_id";
        $stmt = $this->dbConnection->prepare($query);
        $stmt->bindParam(":question_id", $this->question_id);
        $stmt->bindParam(":option_text", $this->option_text);
        $stmt->bindParam(":is_correct", $this->is_correct);
        $stmt->bindParam(":option_id", $this->option_id);
        if ($stmt->execute() && $stmt->rowCount() == 1) {
            return true;
        }
        return false;
    }

    public function delete() {
        $query = "DELETE FROM " . $this->dbTable . " WHERE option_id=:option_id";
        $stmt = $this->dbConnection->prepare($query);
        $stmt->bindParam(":option_id", $this->option_id);
        if ($stmt->execute() && $stmt->rowCount() == 1) {
            return true;
        }
        return false;
    }

    public function validateOption($option_id, $question_id) {
        $query = "SELECT is_correct FROM " . $this->dbTable . " WHERE option_id = :option_id AND question_id = :question_id";
        $stmt = $this->dbConnection->prepare($query);
        $stmt->bindParam(":option_id", $option_id);
        $stmt->bindParam(":question_id", $question_id);
        $stmt->execute();
        
        if ($stmt->rowCount() == 0) {
            return null;
        }
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}