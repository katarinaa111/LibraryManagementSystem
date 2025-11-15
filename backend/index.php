<?php
require 'vendor/autoload.php'; //run autoloader


require_once __DIR__ . '/services/BooksService.php';
Flight::register('booksService', 'BooksService');
require_once __DIR__ . '/routes/books.php';

require_once __DIR__ . '/services/AuthorsService.php';
Flight::register('authorsService', 'AuthorsService');
require_once __DIR__ . '/routes/authors.php';


Flight::start();  //start FlightPHP
?>