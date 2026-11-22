<?php
use Controllers\HomeController;
use Controllers\AuthController;

$router->get('/', [HomeController::class, 'index']);

// ─── Auth routes ────────────────────────────────────────────
$router->get('/login',  [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'login']);
$router->get('/logout', [AuthController::class, 'logout']);

