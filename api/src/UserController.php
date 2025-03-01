<?php

class UserController {
    
    public function __construct(private UserGateway $obj_user_gateway) {
    }

    public function saveUser($method, $data) {
        if ($method == 'POST') {
            $api_key = $this->obj_user_gateway->saveUser($data);

            http_response_code(201);
            echo json_encode(["message"=>"Thank you for registering", "api_key"=>$api_key]);
        }
    }

    public function getByUsername(string $username) : array | FALSE {
        return $this->obj_user_gateway->getByUsername($username);
    }
}