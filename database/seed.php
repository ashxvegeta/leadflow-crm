<?php

declare(strict_types=1);

define('ROOT_PATH', dirname(__DIR__));

// .env load karo
$envFile = ROOT_PATH . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (str_starts_with(trim($line), '#')) continue;
        if (!str_contains($line, '=')) continue;
        [$key, $value] = explode('=', $line, 2);
        $_ENV[trim($key)] = trim($value);
    }
}

// Database connect karo
$config = require ROOT_PATH . '/config/database.php';
$dsn    = sprintf(
    'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
    $config['host'],
    $config['port'],
    $config['database']
);

$pdo = new PDO($dsn, $config['username'], $config['password'], [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);

// Seeders run karo
require_once ROOT_PATH . '/database/seeders/AdminUserSeeder.php';

$seeders = [
    new AdminUserSeeder(),
];

foreach ($seeders as $seeder) {
    $seeder->run($pdo);
}

echo "\n✅ Seeding complete!\n";