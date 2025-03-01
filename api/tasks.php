<?php
require __DIR__."/bootstrap.php";

$obj_auth = new Auth(new UserGateway($database));
// if ($obj_auth->authenticateAPIKey()) {
if ($obj_auth->authenticateAccessToken()) {

    $method = $_SERVER["REQUEST_METHOD"];

    $uri_parts = explode("/", $_SERVER["REQUEST_URI"]);
    $id = NULL;
    if (isset($uri_parts[3])) {
        $task_id = $uri_parts[3];
    }
    //echo $task_id = $uri_parts[3]?$uri_parts[3]:null;

    if ($uri_parts[2] != "tasks") {
        //header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
        http_response_code(404);
        exit;
    }

    $obj_task_controller = new TaskController(new TaskGateway($database), $obj_auth->getUserId());
    $obj_task_controller->processRequest($method, $task_id);
}