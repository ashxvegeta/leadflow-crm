<?php

declare(strict_types=1);

define('ROOT_PATH', dirname(__DIR__));

spl_autoload_register(function (string $class): void {
    $file = ROOT_PATH . '/app/' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

$router = new \Core\Router();

require_once ROOT_PATH . '/routes/web.php';

// ─── Base path calculate karo ───────────────────────────────
// SCRIPT_NAME = /leadflow-crm/public/index.php
// Base path   = /leadflow-crm/public
$basePath = str_replace('/index.php', '', $_SERVER['SCRIPT_NAME']);

$router->dispatch(
    $_SERVER['REQUEST_METHOD'],
    $_SERVER['REQUEST_URI'],
    $basePath
);