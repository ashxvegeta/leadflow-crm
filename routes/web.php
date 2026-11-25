<?php
use Controllers\HomeController;
use Controllers\AuthController;
use Controllers\UserController;

$router->get('/', [HomeController::class, 'index']);
$router->get('/users', [UserController::class, 'index']);

// ─── Auth routes ────────────────────────────────────────────
$router->get('/login',  [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'login']);
$router->post('/logout', [AuthController::class, 'logout']);