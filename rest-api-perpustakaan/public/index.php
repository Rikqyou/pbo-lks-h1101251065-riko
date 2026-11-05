<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Config\Env;
use App\Contracts\BookRepositoryInterface;
use App\Controllers\BookController;
use App\Core\Container;
use App\Core\Request;
use App\Core\Router;
use App\Database\Connection;
use App\Repositories\MySQLBookRepository;

Env::load(__DIR__ . '/../.env');

$container = new Container();

$container->bind(BookRepositoryInterface::class, function () {
    return new MySQLBookRepository(Connection::get());
});

$router = new Router();

$router->get('/api/books', [BookController::class, 'index']);
$router->get('/api/books/{id}', [BookController::class, 'show']);
$router->post('/api/books', [BookController::class, 'store']);
$router->put('/api/books/{id}', [BookController::class, 'update']);
$router->delete('/api/books/{id}', [BookController::class, 'destroy']);

$router->dispatch(new Request(), $container);
