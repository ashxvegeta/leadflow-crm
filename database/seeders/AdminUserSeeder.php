<?php

declare(strict_types=1);

class AdminUserSeeder
{
    public function run(\PDO $pdo): void
    {
        // Pehle check karo — admin already exist karta hai?
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute(['admin@leadflow.com']);

        if ($stmt->fetch()) {
            echo "⏭️  Admin user already exists. Skipping.\n";
            return;
        }

        // Password hash karo — kabhi plain text store mat karo!
        $hashedPassword = password_hash('admin123', PASSWORD_BCRYPT);

        $stmt = $pdo->prepare("
            INSERT INTO users (name, email, password, role, is_active)
            VALUES (?, ?, ?, 'admin', 1)
        ");

        $stmt->execute(['Admin User', 'admin@leadflow.com', $hashedPassword]);

        echo "✅ Admin user created!\n";
        echo "   Email:    admin@leadflow.com\n";
        echo "   Password: admin123\n";
    }
}