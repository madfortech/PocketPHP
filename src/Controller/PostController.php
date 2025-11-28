<?php
namespace PocketPHP\Controller;

use PocketPHP\Model\Post;
use PostOwner\Owner;

class PostController {
    
    private $postModel;

    public function __construct() {

        // SESSION MUST START
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->postModel = new Post();
    }

    // Index Page
    public function index() {
        $posts = $this->postModel->getAll();
        require __DIR__.'/../../templates/posts/index.php';
    }

    
    public function show($id) {
        $post = $this->postModel->find($id);
        if (!$post) {
            http_response_code(404);
            echo 'Post not found';
            return;
        }
        require __DIR__.'/../../templates/posts/show.php';
    }

    // Create Post
    public function create() {
        require __DIR__.'/../../templates/posts/create.php';
    }

    // Store Post
    public function store() {

        // ADD USER ID WHEN CREATING POST
        $title = $_POST['title'] ?? '';
        $body = $_POST['body'] ?? '';
        $userId = $_SESSION['user_id'] ?? null;

        if ($userId === null) {
            die("Login required.");
        }
        
        if (!empty($title) && !empty($body)) {
            $this->postModel->create($title, $body, $userId);
            header('Location: /posts');
            exit();
        }
        
        // If validation fails, redirect back to create form
        header('Location: /posts/create');
        exit();
    }

    // Edit Post
    public function edit($id) {
        $post = $this->postModel->find($id);
        if (!$post) {
            // Log error or set a flash message
            error_log("Post with ID $id not found");
            header('Location: /posts');
            exit();
        }

        // OWNER CHECK
        if (!Owner::checkOwner($post->user_id, $_SESSION['user_id'] ?? null)) {
            die("You are not allowed to edit this post.");
        }
        
        // Debug: Check if the edit.php file exists
        $editTemplate = __DIR__.'/../../templates/posts/edit.php';
        if (!file_exists($editTemplate)) {
            error_log("Edit template not found at: " . $editTemplate);
            die("Edit template not found. Please check the file path.");
        }
        
        require $editTemplate;
    }

    // Update Post
    public function update($id) {

        $post = $this->postModel->find($id);

        // If you're not the owner → stop
        if (!Owner::checkOwner($post->user_id, $_SESSION['user_id'] ?? null)) {
            die("Unauthorized action.");
        }

        $title = $_POST['title'] ?? '';
        $body = $_POST['body'] ?? '';
        
        if (!empty($title) && !empty($body)) {
            $this->postModel->update($id, $title, $body);
            header('Location: /posts/' . $id);
            exit();
        }
        
        // If validation fails, redirect back to edit form
        header('Location: /posts/' . $id . '/edit');
        exit();
    }

    // Delete Post
    public function destroy($id) {

        $post = $this->postModel->find($id);

        if (!$post) {
            die("Post not found.");
        }

        if (!Owner::checkOwner($post->user_id, $_SESSION['user_id'] ?? null)) {
            die("You cannot delete this post.");
        }
        $post = $this->postModel->delete($id);
        header('Location: /posts');
    }
}