<?php
namespace PocketPHP\Model;

use PocketPHP\Core\Database;

class User {

    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM users");
        return $stmt->fetchAll(\PDO::FETCH_OBJ);
    }

    public function find($id) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_OBJ);
    }
    
    public function findByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(\PDO::FETCH_OBJ);
    }

    public function create($name, $email, $password) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        return $stmt->execute([$name, $email, $hashedPassword]);
    }

    public function update($id, $name, $email, $password) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("UPDATE users SET name = ?, email = ?, password = ? WHERE id = ?");
        return $stmt->execute([$name, $email, $hashedPassword, $id]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    public function markEmailAsVerified($email) {
        $stmt = $this->db->prepare("UPDATE users SET email_verified_at = NOW() WHERE email = ?");
        return $stmt->execute([$email]);
    }
    
    public function isEmailVerified($email) {
        $stmt = $this->db->prepare("SELECT email_verified_at FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $result = $stmt->fetch(\PDO::FETCH_OBJ);
        return !empty($result->email_verified_at);
    }

}