<?php
if (isset($_SERVER['HTTP_ORIGIN'])) {
    $allowed = isset($_ENV['CORS_ORIGINS']) ? array_filter(array_map('trim', explode(',', $_ENV['CORS_ORIGINS']))) : ['*'];
    $origin = $_SERVER['HTTP_ORIGIN'];
    $allowOrigin = '*';
    if (!in_array('*', $allowed, true)) {
        if (in_array($origin, $allowed, true)) {
            $allowOrigin = $origin;
        } elseif (count($allowed) > 0) {
            $allowOrigin = $allowed[0];
        }
    }
    header('Access-Control-Allow-Origin: ' . $allowOrigin);
    header('Vary: Origin');
    header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authentication, Authorization, X-Requested-With');
    header('Access-Control-Expose-Headers: Authentication, Authorization');
    header('Access-Control-Max-Age: 86400');
}

if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

require 'vendor/autoload.php';


require_once __DIR__ . '/services/BooksService.php';
Flight::register('booksService', 'BooksService');
require_once __DIR__ . '/routes/books.php';

require_once __DIR__ . '/services/AuthorsService.php';
Flight::register('authorsService', 'AuthorsService');
require_once __DIR__ . '/routes/authors.php';

require_once __DIR__ . '/services/CategoriesService.php';
Flight::register('categoriesService', 'CategoriesService');
require_once __DIR__ . '/routes/categories.php';

require_once __DIR__ . '/services/BorrowedBooksService.php';
Flight::register('borrowedBooksService', 'BorrowedBooksService');
require_once __DIR__ . '/routes/borrowedbooks.php';

require_once __DIR__ . '/services/UsersService.php';
Flight::register('usersService', 'UsersService');
require_once __DIR__ . '/routes/users.php';

require_once __DIR__ . '/services/AuthService.php';
Flight::register('authService', 'AuthService');
require_once __DIR__ . '/routes/auth.php';

require_once __DIR__ . '/middleware/AuthMiddleware.php';
Flight::register('auth_middleware', "AuthMiddleware");

Flight::before('start', function () {
    if (Flight::request()->method === 'OPTIONS') {
        return;
    }
    $url = Flight::request()->url;
    if (strpos($url, '/auth/login') === 0 || strpos($url, '/auth/register') === 0) {
        return;
    }
    $token = Flight::request()->getHeader("Authentication");
    if (!$token) {
        $token = Flight::request()->getHeader("Authorization");
        if ($token && stripos($token, 'Bearer ') === 0) {
            $token = trim(substr($token, 7));
        }
    }
    Flight::auth_middleware()->verifyToken($token);
});

Flight::start();  //start FlightPHP
