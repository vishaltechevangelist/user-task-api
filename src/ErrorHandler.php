<?php

class ErrorHandler {

    public static function handleException(Throwable $exception):void {
        http_response_code(500);
        echo json_encode([
            "Message" => $exception->getMessage(),
            "Code" => $exception->getCode(),
            "line" => $exception->getLine(),
            "file" => $exception->getFile()
        ]);
    }
}