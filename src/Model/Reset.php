<?php
namespace PocketPHP\Model;

use PocketPHP\Core\Database;

class Reset {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function create($email, $token, $expiresAt) {
        $stmt = $this->db->prepare("INSERT INTO resets (email, reset_token, reset_token_expires_at) VALUES (?, ?, ?)");
        return $stmt->execute([$email, $token, $expiresAt]);
    }

    public function findValidToken($token) {
        $stmt = $this->db->prepare("SELECT * FROM resets WHERE reset_token = ? AND reset_token_is_used = 0 AND reset_token_expires_at > NOW()");
        $stmt->execute([$token]);
        return $stmt->fetch(\PDO::FETCH_OBJ);
    }

    public function markAsUsed($token) {
        $stmt = $this->db->prepare("UPDATE resets SET reset_token_is_used = 1 WHERE reset_token = ?");
        return $stmt->execute([$token]);
    }
}
