<?php
session_start();

function getallheaders_lower() {
    $headers = [];

    foreach (getallheaders() as $key => $val) {
        $headers[strtolower($key)] = $val;
    }
    return $headers;
}

$req = array_merge(
    parse_url($_SERVER["REQUEST_URI"]),
    array(
        "headers" => getallheaders_lower(),
        "method" => $_SERVER["REQUEST_METHOD"],
        "query" => $_GET,
        "body" => $_POST
    )
);

if (sizeof($req["body"]) === 0) {
    $req["body"] = file_get_contents('php://input');
}

$res = array(
    "status" => 200,
    "headers" => array()
);

function router_end() {
    global $res;
    header("HTTP/1.1 " . $res["status"]);
    foreach ($res["headers"] as $key => $value) {
        header($key . ": " . $value);
    };
    echo $res["body"];
    exit(0);
}

function middleware($method, $path, $callback) {
    global $req, $res;
    if ($method === NULL || $method === $req["method"]) {
        if (preg_match($path, $req["path"], $matches)) {
            if ($callback($req, $res, $matches) === TRUE)
                router_end();
        }
    }
}

function get($path, $callback) {
    middleware("GET", $path, $callback);
}

function post($path, $callback) {
    middleware("POST", $path, $callback);
}

function put($path, $callback) {
    middleware("PUT", $path, $callback);
}

function delete($path, $callback) {
    middleware("DELETE", $path, $callback);
}

include "app.php";

router_end();