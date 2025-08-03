<?php

// Define your routes here
$router->addRoute('GET', '/', 'PocketPHP\Controller\WelcomeController@index');

// Auth Routes
$router->addRoute('GET', '/auth/signup', 'PocketPHP\Controller\AuthController@index');
$router->addRoute('POST', '/auth/signup', 'PocketPHP\Controller\AuthController@store');
$router->addRoute('GET', '/auth/login', 'PocketPHP\Controller\AuthController@loginForm');
$router->addRoute('POST', '/auth/login', 'PocketPHP\Controller\AuthController@login');
$router->addRoute('POST', '/auth/logout', 'PocketPHP\Controller\AuthController@logout');

// Password Reset Routes
$router->addRoute('GET', '/auth/forgot-password', 'PocketPHP\Controller\ForgetPasswordController@showForgotPasswordForm');
$router->addRoute('POST', '/auth/forgot-password', 'PocketPHP\Controller\ForgetPasswordController@forgotPassword');
$router->addRoute('GET', '/auth/reset-password', 'PocketPHP\Controller\ForgetPasswordController@showResetPasswordForm');
$router->addRoute('GET', '/auth/reset-password/{token}', 'PocketPHP\Controller\ForgetPasswordController@showResetPasswordForm');
$router->addRoute('POST', '/auth/reset-password', 'PocketPHP\Controller\ForgetPasswordController@resetPassword');

// Email Verification Routes
$router->addRoute('GET', '/email/verify', 'PocketPHP\Controller\VerificationController@showVerificationNotice');
$router->addRoute('GET', '/verify-email', 'PocketPHP\Controller\VerificationController@verifyEmail');
$router->addRoute('POST', '/email/resend', 'PocketPHP\Controller\VerificationController@resendVerification');

// Post Routes
$router->addRoute('GET', '/posts', 'PocketPHP\Controller\PostController@index');
$router->addRoute('GET', '/posts/create', 'PocketPHP\Controller\PostController@create');
$router->addRoute('GET', '/posts/{id}', 'PocketPHP\Controller\PostController@show');
$router->addRoute('GET', '/posts/{id}/edit', 'PocketPHP\Controller\PostController@edit');
$router->addRoute('POST', '/posts', 'PocketPHP\Controller\PostController@store');
$router->addRoute('PUT', '/posts/{id}', 'PocketPHP\Controller\PostController@update');
$router->addRoute('DELETE', '/posts/{id}', 'PocketPHP\Controller\PostController@destroy');
