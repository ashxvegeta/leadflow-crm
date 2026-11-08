<?php

declare(strict_types=1);

define('ROOT_PATH', dirname(__DIR__));

// ─── .env file load karo ────────────────────────────────────
$envFile = ROOT_PATH . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (str_starts_with(trim($line), '#')) continue; // comments skip
        if (!str_contains($line, '=')) continue;
        [$key, $value] = explode('=', $line, 2);
        $_ENV[trim($key)] = trim($value);
    }
}

// ─── Autoloader ─────────────────────────────────────────────
spl_autoload_register(function (string $class): void {
    $file = ROOT_PATH . '/app/' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// ─── Router ─────────────────────────────────────────────────
$router = new \Core\Router();
require_once ROOT_PATH . '/routes/web.php';

$basePath = str_replace('/index.php', '', $_SERVER['SCRIPT_NAME']);

$router->dispatch(
    $_SERVER['REQUEST_METHOD'],
    $_SERVER['REQUEST_URI'],
    $basePath
);