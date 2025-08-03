<?php 

return 
[
    'remember_me' => 
    [
        'cookie_name' => 'app_remember_me',
        'expiry_days' => 30,
        'secure' => true,
        'httponly' => true,
        'samesite' => 'Strict',
        'expiry' => time() + 60 * 60 * 24 * 30,
    ],     
];
