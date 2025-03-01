<?php
require __DIR__."/bootstrap.php";

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    http_response_code(405);
    header("Allow: POST");
    exit(0);
}

if (!array_key_exists("username", $_POST) || !array_key_exists("password", $_POST)) {
    http_response_code(400);
    echo json_encode(["message" => "User credential are missing"]);
} else {
    $obj_user_controller = new UserController(new UserGateway($database));
    $user = $obj_user_controller->getByUsername($_POST["username"]);

    if ($user === FALSE || (!password_verify($_POST["password"], $user["password_hash"]))) {
        http_response_code(401);
        echo json_encode(["message" => "Invalid credentials"]);
    } else {
        $access_token = json_encode(["user_id" => $user["id"], "username" => $user["username"]]);
        echo json_encode(["access_token" => base64_encode($access_token)]);
    }
}


 
