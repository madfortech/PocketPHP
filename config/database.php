<?php
return [
    // 'driver'    => 'mysql',
    // 'host'      => 'localhost',
    // 'database'  => 'pocketphp',  // Your database name
    // 'username'  => 'root',          // Default XAMPP username
    // 'password'  => '',              // Default XAMPP password is empty
    // 'charset'   => 'utf8mb4',
    // 'collation' => 'utf8mb4_unicode_ci',
    // 'prefix'    => '',

     
 
    'driver'    => $_ENV['DB_CONNECTION'] ?? '',
    'host'      => $_ENV['DB_HOST'] ?? '',
    'database'  => $_ENV['DB_DATABASE'] ?? '',
    'username'  => $_ENV['DB_USERNAME'] ?? '',
    'password'  => $_ENV['DB_PASSWORD'] ?? '',
    'charset'   => $_ENV['DB_CHARSET'] ?? '',
    'collation' => $_ENV['DB_COLLATION'] ?? '',
    'prefix'    => $_ENV['DB_PREFIX'] ?? '',
];