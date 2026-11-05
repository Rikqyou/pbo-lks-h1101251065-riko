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

$router->dispatch($request, $container);
