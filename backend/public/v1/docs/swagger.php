<?php
ini_set('display_errors', 0);
error_reporting(E_ALL & ~E_WARNING & ~E_USER_WARNING);
require __DIR__ . '/../../../vendor/autoload.php';
if ($_SERVER['SERVER_NAME'] == 'localhost' || $_SERVER['SERVER_NAME'] == '127.0.0.1') {
    define('BASE_URL', 'http://localhost/LibraryManagementSystem/backend');
} else {
    define('BASE_URL', 'https://add-production-server-after-deployment/backend/');
}
$openapi = \OpenApi\Generator::scan([
    __DIR__ . '/doc_setup.php',
    __DIR__ . '/../../../routes'
]);
header('Content-Type: application/json');
echo $openapi->toJson();
?>