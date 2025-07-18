<?php

namespace PocketPHP\Controller;

use PocketPHP\Core\Database;
use PocketPHP\Model\Post;

class WelcomeController
{

    private $postModel;
    public function __construct() {
        $this->postModel = new Post();
    }
    
    public function index()
    {
        // Get posts from the model
        $posts = $this->postModel->getAll();
        
        // Make posts available to the view
        extract(['posts' => $posts]);
        
        // Load the view
        require __DIR__.'/../../templates/welcome.php';
    }
}

