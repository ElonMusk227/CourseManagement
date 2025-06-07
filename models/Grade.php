<?php
require_once 'config/database.php';

class Grade {
    private $conn;
    private $table_name = "grades";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getByStudent($student_id) {
        $query = "SELECT g.*, s.nom as subject_name, s.code as subject_code, s.credits,
                         u.nom as teacher_nom, u.prenom as teacher_prenom
                  FROM " . $this->table_name . " g
                  INNER JOIN subjects s ON g.subject_id = s.id
                  INNER JOIN teachers t ON g.teacher_id = t.id
                  INNER JOIN users u ON t.user_id = u.id
                  WHERE g.student_id = ?
                  ORDER BY g.date_evaluation DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $student_id);
        $stmt->execute();
        return $stmt;
    }

    public function getStudentAverage($student_id) {
        $query = "SELECT AVG(g.note) as moyenne
                  FROM " . $this->table_name . " g
                  WHERE g.student_id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $student_id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['moyenne'] ? round($row['moyenne'], 2) : 0;
    }

    public function getSubjectAverage($subject_id) {
        $query = "SELECT AVG(g.note) as moyenne
                  FROM " . $this->table_name . " g
                  WHERE g.subject_id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $subject_id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['moyenne'] ? round($row['moyenne'], 2) : 0;
    }

    public function getUngradedSubjects($student_id) {
        $query = "SELECT s.*
                  FROM subjects s
                  INNER JOIN student_subjects ss ON s.id = ss.subject_id
                  LEFT JOIN grades g ON s.id = g.subject_id AND g.student_id = ?
                  WHERE ss.student_id = ? AND g.id IS NULL
                  ORDER BY s.nom";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $student_id);
        $stmt->bindParam(2, $student_id);
        $stmt->execute();
        return $stmt;
    }

    public function create($student_id, $subject_id, $teacher_id, $note, $date_evaluation) {
        $query = "INSERT INTO " . $this->table_name . "
                  SET student_id=?, subject_id=?, teacher_id=?, note=?, date_evaluation=?";

        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$student_id, $subject_id, $teacher_id, $note, $date_evaluation]);
    }
}
?>