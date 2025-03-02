<?php
require __DIR__."/bootstrap.php";

$obj_refresh_token_gateway = new RefreshTokenGateway($database, $_ENV["SECRET_KEY"]);

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    http_response_code(405);
    header("Allow: POST");
} elseif (!array_key_exists("refresh_token", $_POST)) {
    http_response_code(400);
    echo json_encode(["message" => "Token is missing"]);
} elseif ($obj_refresh_token_gateway->isRefreshTokenValid($_POST["refresh_token"]) === FALSE) {
    http_response_code(400);
    echo json_encode(["message" => "Token is not whitelisted"]);
} else {
    $codec = new JWTCodec($_ENV["SECRET_KEY"]);

    try {
        $data = $codec->decodeJWTToken($_POST["refresh_token"]);
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode(["message" => "Invalid refresh token"]);
    }

    $obj_user_gateway = new UserGateway($database);
    $obj_user_controller = new UserController($obj_user_gateway);

    $user_data = $obj_user_controller->getById($data["sub"]);

    if ($user_data === FALSE) {
        http_response_code(400);
        echo json_encode(["message" => "Invalid authentication"]);
    }

    require __DIR__."/tokens.php";

    $obj_refresh_token_gateway->deleteOldRefreshToken($_POST["refresh_token"]);
    $obj_refresh_token_gateway->saveRefreshToken($refresh_token, $refresh_token_expiry);
} 