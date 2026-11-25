<?php

declare(strict_types=1);

namespace Controllers;

use Core\BaseController;
use Core\Database;

class UserController extends BaseController
{
    // ─── Users list (admin only) ────────────────────────────
    public function index(): void
    {
        $this->requireAdmin();

        $pdo   = Database::getConnection();
        $users = $pdo->query("
            SELECT id, name, email, role, is_active, created_at
            FROM users
            ORDER BY id DESC
        ")->fetchAll();

        $this->render('users/index', [
            'title' => 'Users — LeadFlow CRM',
            'users' => $users,
        ]);
    }
}