<?php
require 'vendor/autoload.php'; //run autoloader


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

Flight::route('/*', function () {
    if (
        strpos(Flight::request()->url, '/auth/login') === 0 ||
        strpos(Flight::request()->url, '/auth/register') === 0
    ) {
        return TRUE;
    } else {
        try {
            $token = Flight::request()->getHeader("Authentication");
            if (Flight::auth_middleware()->verifyToken($token))
                return TRUE;
        } catch (\Exception $e) {
            Flight::halt(401, $e->getMessage());
        }
    }
});

Flight::start();  //start FlightPHP
?>