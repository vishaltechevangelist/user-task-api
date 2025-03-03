<?php
require dirname(__DIR__)."/api/src/TaskController.php";

$uri_parts = explode("/", $_SERVER["REQUEST_URI"]);

echo $id = $uri_parts[3]?$uri_parts[3]:null;
echo $method = $_SERVER["REQUEST_METHOD"];

if ($uri_parts[2] != "tasks") {
    //header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
    http_response_code(404);
    exit;
}

$obj_task_controller = new TaskController();
$obj_task_controller->processRequest($method, $id);