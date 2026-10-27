<?php

declare(strict_types=1);

// ─── 1. Root path define karo ───────────────────────────────
define('ROOT_PATH', dirname(__DIR__));

// ─── 2. Autoloader (abhi simple, baad mein Composer) ────────
spl_autoload_register(function (string $class): void {
    $file = ROOT_PATH . '/app/' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// ─── 3. Abhi ke liye simple test ────────────────────────────
echo "<h1>LeadFlow CRM is running!</h1>";
echo "<p>ROOT_PATH: " . ROOT_PATH . "</p>";