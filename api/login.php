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
        //$data = ["user_id" => $user["id"], "username" => $user["username"]];
        //$access_token = json_encode($data);
        //echo json_encode(["access_token" => base64_encode($access_token)]);
        $data_for_access_token = ["sub" => $user["id"], 
                                "username" => $user["username"],
                                "exp" => time() + 20];
        
        $data_for_refresh_token = ["sub"=> $user["id"],
                                    "exp" => time() + 3600];
        
        $codec = new JWTCodec($_ENV["SECRET_KEY"]);
        echo json_encode(["access_token" => $codec->getJWTToken($data_for_access_token),
                         "refresh_token" => $codec->getJWTToken($data_for_refresh_token)
                        ]);

    }
}


 
