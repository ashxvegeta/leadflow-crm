<?php
use Controllers\HomeController;

$router->get('/', [HomeController::class, 'index']);

// ─── Temporary DB test ───────────────────────────────────────
$router->get('/db-test', function() {
    try {
        $pdo = \Core\Database::getConnection();
        echo "<h2>✅ Database connected!</h2>";
        echo "<p>PDO instance: " . get_class($pdo) . "</p>";
    } catch (\RuntimeException $e) {
        echo "<h2>❌ Connection failed</h2>";
        echo "<p>" . $e->getMessage() . "</p>";
    }
});