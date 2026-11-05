<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Accept');
header('Access-Control-Max-Age: 86400');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Router;
use App\Database\Connection;
use App\Repositories\MySQLBookRepository;
use App\Repositories\MySQLCategoryRepository;
use App\Services\BookService;
use App\Services\CategoryService;
use App\Controllers\BookController;
use App\Controllers\CategoryController;

$db = Connection::get();

// Binding Repositories & Services
$bookRepository = new MySQLBookRepository($db);
$bookService = new BookService($bookRepository);
$bookController = new BookController($bookService);

$categoryRepository = new MySQLCategoryRepository($db);
$categoryService = new CategoryService($categoryRepository);
$categoryController = new CategoryController($categoryService);

$router = new Router();

// Routes Books
$router->add('GET', '/api/books', [$bookController, 'index']);
$router->add('POST', '/api/books', [$bookController, 'store']);
$router->add('GET', '/api/books/(\d+)', [$bookController, 'show']);
$router->add('PUT', '/api/books/(\d+)', [$bookController, 'update']);
$router->add('DELETE', '/api/books/(\d+)', [$bookController, 'destroy']);

// Routes Categories
$router->add('GET', '/api/categories', [$categoryController, 'index']);
$router->add('POST', '/api/categories', [$categoryController, 'store']);
$router->add('PUT', '/api/categories/(\d+)', [$categoryController, 'update']);
$router->add('DELETE', '/api/categories/(\d+)', [$categoryController, 'destroy']);

$router->run();