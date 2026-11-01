<?php

declare(strict_types=1);

namespace Core;

class BaseController
{
    // ─── View render karo ───────────────────────────────────
    protected function render(string $view, array $data = []): void
    {
        // Array ko variables mein convert karo
        // ['title' => 'Home'] → $title = 'Home'
        extract($data);

        $viewPath = ROOT_PATH . '/resources/views/' . $view . '.php';

        if (!file_exists($viewPath)) {
            throw new \RuntimeException("View not found: {$view}");
        }

        require_once $viewPath;
    }
}