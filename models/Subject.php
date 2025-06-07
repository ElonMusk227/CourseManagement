<?php
require_once 'config/database.php';

class Subject {
    private $conn;
    private $table_name = "subjects";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY nom";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function getByTeacher($teacher_id) {
        $query = "SELECT s.*, ts.teacher_id 
                  FROM " . $this->table_name . " s
                  INNER JOIN teacher_subjects ts ON s.id = ts.subject_id
                  WHERE ts.teacher_id = ?
                  ORDER BY s.nom";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $teacher_id);
        $stmt->execute();
        return $stmt;
    }

    public function getByStudent($student_id) {
        $query = "SELECT s.*, ss.student_id 
                  FROM " . $this->table_name . " s
                  INNER JOIN student_subjects ss ON s.id = ss.subject_id
                  WHERE ss.student_id = ?
                  ORDER BY s.nom";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $student_id);
        $stmt->execute();
        return $stmt;
    }

    public function search($term) {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE nom LIKE ? OR code LIKE ? OR departement LIKE ?
                  ORDER BY nom";
        
        $search_term = "%{$term}%";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $search_term);
        $stmt->bindParam(2, $search_term);
        $stmt->bindParam(3, $search_term);
        $stmt->execute();
        return $stmt;
    }

    public function create($nom, $code, $credits, $departement) {
        $query = "INSERT INTO " . $this->table_name . "
                  SET nom=?, code=?, credits=?, departement=?";

        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$nom, $code, $credits, $departement]);
    }

    public function update($id, $nom, $code, $credits, $departement) {
        $query = "UPDATE " . $this->table_name . "
                  SET nom=?, code=?, credits=?, departement=?
                  WHERE id=?";

        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$nom, $code, $credits, $departement, $id]);
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$id]);
    }
}
?>