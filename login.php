<?php
require __DIR__."/bootstrap.php";

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    http_response_code(405);
    header("Allow: POST");
} elseif (!array_key_exists("username", $_POST) || !array_key_exists("password", $_POST)) {
    http_response_code(400);
    echo json_encode(["message" => "User credential are missing"]);
} else {
    $obj_user_controller = new UserController(new UserGateway($database));
    $user = $obj_user_controller->getByUsername($_POST["username"]);

    if ($user === FALSE || (!password_verify($_POST["password"], $user["password_hash"]))) {
        http_response_code(401);
        echo json_encode(["message" => "Invalid credentials"]);
    } else {
        //$data = ["user_id" => $user["id"], "username" => $user["username"]];
        //$access_token = json_encode($data);
        //echo json_encode(["access_token" => base64_encode($access_token)]);
        $codec = new JWTCodec($_ENV["SECRET_KEY"]);
        
        require __DIR__."/tokens.php";
        
        $obj_refresh_token_gateway = new RefreshTokenGateway($database, $_ENV["SECRET_KEY"]);
        $obj_refresh_token_gateway->saveRefreshToken($refresh_token, $refresh_token_expiry);
    }
}


 
