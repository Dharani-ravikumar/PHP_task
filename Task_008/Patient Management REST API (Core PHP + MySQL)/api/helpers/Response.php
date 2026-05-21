<?php

class Response {

    public static function send($status, $message, $data = null) {

        echo json_encode([
            "status" => $status,
            "message" => $message,
            "data" => $data
        ]);
    }
}

?>