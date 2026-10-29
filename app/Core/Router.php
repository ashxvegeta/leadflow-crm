<?php

declare(strict_types=1);

namespace Core;

class Router
{
    private array $routes = [];

    public function get(string $path, array|callable $action): void
    {
        $this->routes['GET'][$path] = $action;
    }

    public function post(string $path, array|callable $action): void
    {
        $this->routes['POST'][$path] = $action;
    }

public function dispatch(string $method, string $uri, string $basePath = ''): void
{
    // Query string hata do
    $uri = strtok($uri, '?');

    // Base path hata do
    // /leadflow-crm/public/leads → /leads
    if ($basePath !== '' && str_starts_with($uri, $basePath)) {
        $uri = substr($uri, strlen($basePath));
    }

    // Empty uri ko / banao
    if ($uri === '' || $uri === false) {
        $uri = '/';
    }

    if (isset($this->routes[$method][$uri])) {
        $action = $this->routes[$method][$uri];

        if (is_callable($action)) {
            call_user_func($action);
        } else {
            [$controllerClass, $methodName] = $action;
            $controller = new $controllerClass();
            $controller->$methodName();
        }
    } else {
        http_response_code(404);
        echo "<h1>404 — Page Not Found</h1>";
    }
}
}