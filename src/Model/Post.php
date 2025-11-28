<?php
namespace PocketPHP\Model;

use PocketPHP\Core\Database;

class Post {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // SELECT * FROM posts ORDER BY created_at DESC
    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM posts ORDER BY created_at DESC");
        return $stmt->fetchAll(\PDO::FETCH_OBJ);
    }

    // INSERT INTO posts (title, body) VALUES (?, ?)
    public function create($title, $body, $userId) {
        $stmt = $this->db->prepare("INSERT INTO posts (title, body, user_id) VALUES (?, ?,?)");
        return $stmt->execute([$title, $body, $userId]);
    }

    // SELECT * FROM posts WHERE id = ?
    public function find($id) {
        $stmt = $this->db->prepare("SELECT * FROM posts WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_OBJ);
    }

    // UPDATE posts SET title = ?, body = ? WHERE id = ?
    public function update($id, $title, $body) {
        $stmt = $this->db->prepare("UPDATE posts SET title = ?, body = ? WHERE id = ?");
        return $stmt->execute([$title, $body, $id]);
    }

    // DELETE FROM posts WHERE id = ?
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM posts WHERE id = ?");
        return $stmt->execute([$id]);
    }
}