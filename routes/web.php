<?php
use Controllers\HomeController;

$router->get('/', [HomeController::class, 'index']);

