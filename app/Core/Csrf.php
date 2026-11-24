<?php

declare(strict_types=1);

namespace Core;

class Csrf
{
    // Generate the token once per session
    public static function token(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    // Hidden input to put in every form
    public static function field(): string
    {
        return '<input type="hidden" name="csrf_token" value="' . self::token() . '">';
    }

    // Check the token sent with the form
    public static function verify(): void
    {
        $sent = $_POST['csrf_token'] ?? '';

        if (!is_string($sent) || !hash_equals(self::token(), $sent)) {
            http_response_code(403);
            echo '<h1>403 — Forbidden. Go back and try again.</h1>';
            exit;
        }
    }
}