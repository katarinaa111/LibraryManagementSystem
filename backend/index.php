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

Flight::start();  //start FlightPHP
?>