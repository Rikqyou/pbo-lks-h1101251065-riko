<?php

namespace App\Core;

class Router
{
    private array $routes = [];

    public function get(string $path, array $handler): void
    {
        $this->addRoute('GET', $path, $handler);
    }

    public function post(string $path, array $handler): void
    {
        $this->addRoute('POST', $path, $handler);
    }

    public function put(string $path, array $handler): void
    {
        $this->addRoute('PUT', $path, $handler);
    }

    public function delete(string $path, array $handler): void
    {
        $this->addRoute('DELETE', $path, $handler);
    }

    private function addRoute(string $method, string $path, array $handler): void
    {
        $regex = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([a-zA-Z0-9_]+)', $path);
        $regex = '#^' . $regex . '$#';

        $this->routes[] = [
            'method'  => $method,
            'path'    => $path,
            'regex'   => $regex,
            'handler' => $handler
        ];
    }

    public function dispatch(Request $request, Container $container): void
    {
        $method = $request->getMethod();
        $uri    = $request->getUri();

        foreach ($this->routes as $route) {
            if ($route['method'] === $method && preg_match($route['regex'], $uri, $matches)) {
                array_shift($matches);
                [$controllerClass, $action] = $route['handler'];
                $controller = $container->make($controllerClass);
                call_user_func_array([$controller, $action], array_merge([$request], $matches));
                return;
            }
        }

        Response::json([
            'success' => false,
            'message' => "Endpoint tidak ditemukan: {$uri}"
        ], 404);
    }
}
