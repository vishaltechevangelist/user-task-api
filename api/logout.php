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
    $obj_refresh_token_gateway->deleteOldRefreshToken($_POST["refresh_token"]);
    http_response_code(204);
} 