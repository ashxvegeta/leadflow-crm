<?php

declare(strict_types=1);

namespace Controllers;

use Core\Auth;
use Core\BaseController;
use Core\Database;

class AuthController extends BaseController
{
    // ─── Login page dikhao ──────────────────────────────────
    public function showLogin(): void
    {
        // Pehle se logged in hai toh dashboard pe bhejo
        if (Auth::check()) {
            $this->redirect('/');
            exit;
        }

        Auth::startSession();

        $this->render('auth/login', [
            'error'      => $_SESSION['flash_error'] ?? null,
            'old_email'  => $_SESSION['old_email']   ?? null,
            'csrf_token' => '',
        ]);

        // Flash messages clear karo
        unset($_SESSION['flash_error'], $_SESSION['old_email']);
    }

    // ─── Login form submit ──────────────────────────────────
    public function login(): void
    {
        Auth::startSession();

        $email    = trim($_POST['email']    ?? '');
        $password = $_POST['password'] ?? '';


        // ─── Basic validation ────────────────────────────────
        if (empty($email) || empty($password)) {
            $_SESSION['flash_error'] = 'Email aur password dono required hain.';
            $_SESSION['old_email']   = $email;
            $this->redirect('/login');

        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['flash_error'] = 'Valid email address enter karo.';
            $_SESSION['old_email']   = $email;
           $this->redirect('/login');

        }

        // ─── Database se user dhundho ────────────────────────
        $pdo  = Database::getConnection();
        $stmt = $pdo->prepare("
            SELECT id, name, email, password, role, is_active
            FROM users
            WHERE email = ?
            LIMIT 1
        ");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        // ─── User exist karta hai? Password sahi hai? ────────
        if (!$user || !password_verify($password, $user['password'])) {
            $_SESSION['flash_error'] = 'Email ya password galat hai.';
            $_SESSION['old_email']   = $email;
         $this->redirect('/login');

        }

        // ─── Account active hai? ─────────────────────────────
        if (!$user['is_active']) {
            $_SESSION['flash_error'] = 'Tumhara account deactivate hai.';
           $this->redirect('/login');

        }

        // ─── Login karo ──────────────────────────────────────
        Auth::login($user);
        $this->redirect('/login');
        exit;
    }

    // ─── Logout ─────────────────────────────────────────────
    public function logout(): void
    {
        Auth::logout();
        $this->redirect('/login');

    }
}