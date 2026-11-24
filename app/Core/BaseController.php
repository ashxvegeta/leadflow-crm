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

    protected function redirect(string $path): void
    {
    header('Location: ' . BASE_URL . $path);
    exit;
    }

    protected function requireAuth(): void
    {
        if (!Auth::check()) {
            $this->redirect('/login');
        }
    }


    
    protected function requireAdmin(): void
    {
        $this->requireAuth();

        if (!Auth::isAdmin()) {
            http_response_code(403);
            echo '<h1>403 — Access denied</h1>';
            exit;
        }
    }

}