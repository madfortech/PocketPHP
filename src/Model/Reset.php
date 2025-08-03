<?php
namespace PocketPHP\Model;

use PocketPHP\Core\Database;

class Reset {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        // Clean up expired tokens on initialization
        $this->cleanupExpiredTokens();
    }

    public function create($email, $token, $expiresAt) {
        // Clean up any existing tokens for this email first
        $this->deleteByEmail($email);
        
        $stmt = $this->db->prepare("INSERT INTO resets (email, reset_token, reset_token_expires_at) VALUES (?, ?, ?)");
        return $stmt->execute([$email, $token, $expiresAt]);
    }

    public function findByToken($token) {
        $stmt = $this->db->prepare("SELECT * FROM resets WHERE reset_token = ? AND (is_used = 0 OR is_used IS NULL) AND reset_token_expires_at > NOW()");
        $stmt->execute([$token]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
    
    public function findValidToken($token) {
        return $this->findByToken($token);
    }

    public function markAsUsed($token) {
        $stmt = $this->db->prepare("UPDATE resets SET is_used = 1 WHERE reset_token = ?");
        return $stmt->execute([$token]);
    }
    
    public function delete($token) {
        $stmt = $this->db->prepare("DELETE FROM resets WHERE reset_token = ?");
        return $stmt->execute([$token]);
    }
    
    public function deleteByEmail($email) {
        $stmt = $this->db->prepare("DELETE FROM resets WHERE email = ?");
        return $stmt->execute([$email]);
    }
    
    public function cleanupExpiredTokens() {
        // Delete tokens that are either used or expired
        $stmt = $this->db->prepare("DELETE FROM resets WHERE is_used = 1 OR reset_token_expires_at <= NOW()");
        return $stmt->execute();
    }
}
