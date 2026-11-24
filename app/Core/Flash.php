<?php

declare(strict_types=1);

namespace Core;

class Flash
{
    // $type: success, danger, warning, info (Bootstrap alert colours)
    public static function set(string $type, string $message): void
    {
        $_SESSION['flash'][$type] = $message;
    }

    // Get all messages once, then clear them
    public static function pull(): array
    {
        $messages = $_SESSION['flash'] ?? [];
        unset($_SESSION['flash']);
        return $messages;
    }
}