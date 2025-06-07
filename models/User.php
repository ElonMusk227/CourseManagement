<?php
require_once 'config/database.php';

class User {
    private $conn;
    private $table_name = "users";

    public $id;
    public $nom;
    public $prenom;
    public $tel;
    public $mail;
    public $password;
    public $role;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function login($mail, $password) {
        $query = "SELECT u.id, u.nom, u.prenom, u.mail, u.password, u.role 
                  FROM " . $this->table_name . " u 
                  WHERE u.mail = ? LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $mail);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row && password_verify($password, $row['password'])) {
            $this->id = $row['id'];
            $this->nom = $row['nom'];
            $this->prenom = $row['prenom'];
            $this->mail = $row['mail'];
            $this->role = $row['role'];
            return true;
        }

        return false;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                  SET nom=:nom, prenom=:prenom, tel=:tel, mail=:mail, password=:password, role=:role";

        $stmt = $this->conn->prepare($query);

        $this->nom = htmlspecialchars(strip_tags($this->nom));
        $this->prenom = htmlspecialchars(strip_tags($this->prenom));
        $this->tel = htmlspecialchars(strip_tags($this->tel));
        $this->mail = htmlspecialchars(strip_tags($this->mail));
        $this->password = password_hash($this->password, PASSWORD_DEFAULT);
        $this->role = htmlspecialchars(strip_tags($this->role));

        $stmt->bindParam(":nom", $this->nom);
        $stmt->bindParam(":prenom", $this->prenom);
        $stmt->bindParam(":tel", $this->tel);
        $stmt->bindParam(":mail", $this->mail);
        $stmt->bindParam(":password", $this->password);
        $stmt->bindParam(":role", $this->role);

        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }

        return false;
    }
}
?>