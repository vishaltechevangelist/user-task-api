<?php
declare(strict_types=1);
ini_set("display_errors", "Off");

require __DIR__."/vendor/autoload.php";

use App\Database;
use App\ErrorHandler;

//set_exception_handler('ErrorHandler::handleException');

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

$database = new Database($_ENV["DB_HOST"], $_ENV["DB_NAME"], $_ENV["DB_USER"], $_ENV["DB_PASS"]);

header("Content-type: application/json; charset=UTF-8");