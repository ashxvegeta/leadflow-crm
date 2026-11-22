<?php

declare(strict_types=1);

namespace Core;

class Auth
{
    // ─── Session start karo ─────────────────────────────────
    public static function startSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    // ─── Login karo — session mein user save karo ───────────
    public static function login(array $user): void
    {
        self::startSession();

        // Session fixation attack se bachao
        session_regenerate_id(true);

        $_SESSION['user_id']   = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_role'] = $user['role'];
    }

    // ─── Logged in hai? ─────────────────────────────────────
    public static function check(): bool
    {
        self::startSession();
        return isset($_SESSION['user_id']);
    }

    // ─── Current user ka data ───────────────────────────────
    public static function user(): ?array
    {
        self::startSession();

        if (!self::check()) return null;

        return [
            'id'   => $_SESSION['user_id'],
            'name' => $_SESSION['user_name'],
            'role' => $_SESSION['user_role'],
        ];
    }

    // ─── Logout ─────────────────────────────────────────────
    public static function logout(): void
    {
        self::startSession();
        $_SESSION = [];
        session_destroy();
    }

    // ─── Admin hai? ─────────────────────────────────────────
    public static function isAdmin(): bool
    {
        return self::check() && $_SESSION['user_role'] === 'admin';
    }
}