<?php

namespace PocketPHP\Controller;

class WelcomeController
{
    public function index()
    {
        require __DIR__.'/../../templates/welcome.php';
    }
}

