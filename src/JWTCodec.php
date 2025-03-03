<?php

class JWTCodec {

    public function __construct(private string $key) {
    }

    public function getJWTToken($payload) : string {
        $header = $this->base64URLEncode(json_encode(["typ" => "JWT", "alg" => "HS256"]));
        
        $payload = $this->base64URLEncode(json_encode($payload));

        $signature = $this->base64URLEncode(hash_hmac("sha256", $header . "." . $payload, 
        $this->key, true));

        return $header . "." . $payload . "." . $signature;
    }

    private function base64URLEncode(string $stringToEncode) : string {
        return str_replace( ["+", "/", "="], 
                            ["-", "_", ""], 
                            base64_encode($stringToEncode));
    }

    public function decodeJWTToken(string $token) : array {
                        
        $data = explode(".", $token);

        if (count($data) != 3 || empty($data[0]) || empty($data[1]) || empty($data[2])) {
                throw new Exception("Invalid JWT Token");
        }

        $header_from_token = $data[0];
        $payload_from_token = $data[1];



        $signature = hash_hmac("sha256", $header_from_token . "." . $payload_from_token, 
        $this->key, true);

        $signature_from_token = $this->base64URLDecode($data[2]);

        if (!hash_equals($signature, $signature_from_token)) {
            throw new InvalidSignatureException;
        }
        
        $payload = json_decode($this->base64URLDecode($data[1]), true);

        if ($payload["exp"] < time()) {
            throw new TokenExpiredException;
        }
        return $payload;
    }

    private function base64URLDecode(string $stringToDecode) : string {
        return base64_decode(str_replace(["-", "_"], 
                            ["+", "/",],
                            $stringToDecode));
    }
}       