<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Request;
use App\Core\Response;
use App\Core\Router;
use App\Core\Container;
use App\Core\ExceptionHandler;
use App\Controllers\BookController;
use App\Middleware\AuthMiddleware;
use App\Contracts\BookRepositoryInterface;
use App\Repositories\MySQLBookRepository;
use App\Contracts\CategoryRepositoryInterface;
use App\Repositories\MySQLCategoryRepository;

// Set Global Exception Handler
set_exception_handler([ExceptionHandler::class, 'handle']);

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

$container->bind(\PDO::class, function() {
    $host = $_ENV['DB_HOST'] ?? '127.0.0.1';
    $db   = $_ENV['DB_DATABASE'] ?? 'perpustakaan';
    $user = $_ENV['DB_USERNAME'] ?? 'root';
    $pass = $_ENV['DB_PASSWORD'] ?? '';
    $charset = 'utf8mb4';

    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    return new \PDO($dsn, $user, $pass, [
        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
        \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
    ]);
});

$container->bind(BookRepositoryInterface::class, MySQLBookRepository::class);

$authMiddleware = new AuthMiddleware();
$authMiddleware->handle($request);

$router = new Router();
$router->get('/api/books', [BookController::class, 'index']);
$router->post('/api/books', [BookController::class, 'store']);
$router->put('/api/books/{id}', [BookController::class, 'update']);
$router->delete('/api/books/{id}', [BookController::class, 'destroy']);


// Binding Container Kategori
$container->bind(\App\Contracts\CategoryRepositoryInterface::class, function() use ($container) {
    return new \App\Repositories\MySQLCategoryRepository($container->make(\PDO::class));
});

$container->bind(\App\Services\CategoryService::class, function() use ($container) {
    return new \App\Services\CategoryService($container->make(\App\Contracts\CategoryRepositoryInterface::class));
});

$container->bind(\App\Controllers\CategoryController::class, function() use ($container) {
    return new \App\Controllers\CategoryController($container->make(\App\Services\CategoryService::class));
});

// Route Kategori
$router->get('/api/categories', [\App\Controllers\CategoryController::class, 'index']);
$router->get('/api/categories/{id}', [\App\Controllers\CategoryController::class, 'show']);
$router->post('/api/categories', [\App\Controllers\CategoryController::class, 'store']);
$router->put('/api/categories/{id}', [\App\Controllers\CategoryController::class, 'update']);
$router->delete('/api/categories/{id}', [\App\Controllers\CategoryController::class, 'destroy']);

// Jalankan Router DI PALING BAWAH
$router->dispatch($request, $container);