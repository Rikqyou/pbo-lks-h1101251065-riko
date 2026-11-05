<?php

namespace App\Core;

class Router
{
    private array $routes = [];

    public function get(string $path, callable|array $handler): void { $this->addRoute('GET', $path, $handler); }
    public function post(string $path, callable|array $handler): void { $this->addRoute('POST', $path, $handler); }
    public function put(string $path, callable|array $handler): void { $this->addRoute('PUT', $path, $handler); }
    public function delete(string $path, callable|array $handler): void { $this->addRoute('DELETE', $path, $handler); }

    private function addRoute(string $method, string $path, callable|array $handler): void
    {
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([a-zA-Z0-9_]+)', $path);
        $this->routes[] = [
            'method'  => $method,
            'path'    => $path,
            'pattern' => '#^' . $pattern . '$#',
            'handler' => $handler
        ];
    }

    public function dispatch(Request $request, Container $container): void
    {
        $method = $request->getMethod();
        $uri = $request->getUri();

        foreach ($this->routes as $route) {
            if ($route['method'] === $method && preg_match($route['pattern'], $uri, $matches)) {
                array_shift($matches);
                $handler = $route['handler'];

                if (is_array($handler)) {
                    [$controllerClass, $action] = $handler;
                    $controller = $container->make($controllerClass);
                    call_user_func_array([$controller, $action], array_merge([$request], $matches));
                } else {
                    call_user_func_array($handler, array_merge([$request], $matches));
                }
                return;
            }
        }

        Response::json([
            'success' => false,
            'message' => "Endpoint tidak ditemukan: {$uri}"
        ], 404);
    }
}
