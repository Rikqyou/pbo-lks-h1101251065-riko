<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Request;
use App\Core\Response;
use App\Core\Router;
use App\Core\Container;
use App\Controllers\BookController;
use App\Middleware\AuthMiddleware;
use App\Contracts\BookRepositoryInterface;
use App\Repositories\MySQLBookRepository;

if (file_exists(__DIR__ . '/../.env')) {
    $lines = file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($name, $value) = explode('=', $line, 2);
        $_ENV[trim($name)] = trim($value);
    }
}

$request = new Request();
$container = new Container();

// Register Dependency Injection Binding
$container->bind(BookRepositoryInterface::class, MySQLBookRepository::class);

// Proteksi Middleware Token
$authMiddleware = new AuthMiddleware();
$authMiddleware->handle($request);

$router = new Router();
$router->get('/api/books', [BookController::class, 'index']);

$router->dispatch($request, $container);
