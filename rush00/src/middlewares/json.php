<?php

function json_decode_middleware(&$req, &$res) {
    if ($req["headers"]["content-type"] === "application/json") {
        $req["body"] = json_decode($req["body"], TRUE);
        if (($error = json_last_error()) !== JSON_ERROR_NONE) {
            $res["status"] = 400;
            switch ($error) {
                case JSON_ERROR_DEPTH:
                    $res["body"] = "Invalid JSON: The maximum stack depth has been exceeded";
            }
            return (TRUE);
        }
    }
}

function json_encode_middleware($res, &$res) {
    if (!is_string($res["body"]) || $res["json"]) {
        $res["headers"]["Content-Type"] = "application/json";
        $res["body"] = json_encode($res["body"], JSON_PRETTY_PRINT);
    }
}