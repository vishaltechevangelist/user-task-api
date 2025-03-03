<?php
require __DIR__."/bootstrap.php";

use App\UserGateway;
use App\UserController;

$obj_user_gateway = new UserGateway($database);
$obj_user_controller = new UserController($obj_user_gateway);
$obj_user_controller->saveUser($_SERVER["REQUEST_METHOD"], $_POST);