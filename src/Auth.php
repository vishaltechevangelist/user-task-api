<?php
namespace App;

use App\UserGateway;
use App\JWTCodec;

class Auth {

    private int $user_id;

    public function __construct(private UserGateway $obj_user_gateway, private JWTCodec $obj_jwt_codec) {
    }

    public function authenticateAPIKey() : bool {

        $api_key = $_SERVER["HTTP_X_API_KEY"];

        if (empty($api_key)) {
            http_response_code(400);
            echo json_encode(["message"=>"API Key is missing"]);
            return FALSE;
        }

        $user_id = $this->obj_user_gateway->getByAPIKey($api_key);

        if ($user_id === FALSE) {
            http_response_code(401);
            echo json_encode(["message"=>"Invalid API Key"]);
            return FALSE;
        }

        $this->user_id = $user_id["id"];

        return TRUE;
    }

    public function getUserId() : int {
        return $this->user_id;
    }

    public function authenticateAccessToken() : bool {

       if (!preg_match("/^Bearer\s+(.*)/", $_SERVER["REDIRECT_HTTP_AUTHORIZATION"], $matches)) {
            http_response_code(400);
            echo json_encode(["message" => "Invalid Credentials with wrong scheme or code"]);
            return FALSE;
        }

        $token_str = base64_decode($matches[1]);
        if ($token_str === FALSE) {
            http_response_code(400);
            echo json_encode(["message" => "Invalid Credentials with wrong code"]);
            return FALSE;
        }
        
        $data = json_decode($token_str, true); 
        $this->user_id = $data["user_id"];
        return true;
    }

    public function authenticateJWTToken() : bool {

        if (!preg_match("/^Bearer\s+(.*)/", $_SERVER["REDIRECT_HTTP_AUTHORIZATION"], $matches)) {
            http_response_code(400);
            echo json_encode(["message" => "Invalid Credentials with wrong scheme or code"]);
            return FALSE;
        }
        
        try {
            $data = $this->obj_jwt_codec->decodeJWTToken($matches[1]);
        } catch (TokenExpiredException $e) {
            http_response_code(401);
            echo json_encode(["message" => "Token has expired"]);
            return FALSE;
        } catch (InvalidSignatureException $e) {    
            http_response_code(401);
            echo json_encode(["message" => "Invalid Signature"]);
            return FALSE;
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(["message" => $e->getMessage()]);
            return FALSE;
        }

        $this->user_id = $data["sub"];
        return true;
    }
}