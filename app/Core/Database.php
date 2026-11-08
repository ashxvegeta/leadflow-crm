<?php

declare(strict_types=1);

namespace Core;

use PDO;
use PDOException;

class Database
{
    // ─── Singleton instance ──────────────────────────────────
    private static ?PDO $instance = null;

    // ─── Constructor private — bahar se new Database() nahi ──
    private function __construct() {}

    // ─── Ek hi connection throughout app ─────────────────────
    public static function getConnection(): PDO
    {
        if (self::$instance === null) {
            $config = require ROOT_PATH . '/config/database.php';

            $dsn = sprintf(
                'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
                $config['host'],
                $config['port'],
                $config['database']
            );

            try {
                self::$instance = new PDO(
                    $dsn,
                    $config['username'],
                    $config['password'],
                    [
                        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES   => false,
                    ]
                );
            } catch (PDOException $e) {
                // Production mein details mat dikhao
                error_log('DB Connection failed: ' . $e->getMessage());
                throw new \RuntimeException('Database connection failed.');
            }
        }

        return self::$instance;
    }
}