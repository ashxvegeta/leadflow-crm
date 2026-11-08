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

// migrations_log table banao agar nahi hai
$pdo->exec("
    CREATE TABLE IF NOT EXISTS migrations_log (
        id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        migration  VARCHAR(255) NOT NULL UNIQUE,
        ran_at     TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
");

// Pehle se run migrations ka record lo
$ran = $pdo->query("SELECT migration FROM migrations_log")
           ->fetchAll(PDO::FETCH_COLUMN);

// migrations/ folder se files lo
$files = glob(ROOT_PATH . '/database/migrations/*.php');
sort($files); // order maintain karo

foreach ($files as $file) {
    $name = basename($file);

    // Already run ho chuki hai?
    if (in_array($name, $ran)) {
        echo "⏭️  Skipped:  {$name}\n";
        continue;
    }

    // Migration run karo
    $migration = require $file;
    $migration->up($pdo);

    // Log karo
    $stmt = $pdo->prepare("INSERT INTO migrations_log (migration) VALUES (?)");
    $stmt->execute([$name]);

    echo "✅ Migrated: {$name}\n";
}

echo "\n✅ All migrations complete!\n";