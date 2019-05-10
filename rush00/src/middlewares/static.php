<?php

$mime_types = json_decode(file_get_contents(__DIR__ . "/mimes.json"), TRUE);

function middleware_static($root) {
    return function ($req, &$res) use ($root) {
        global $mime_types;
        $path = $req["path"];

        // Remove /../
        $path = preg_replace("/\/\.+(\/|$)/", "", $path);

        $path = $root . DIRECTORY_SEPARATOR . $path;

        if (is_dir($path))
            $path = $path . "/index.html";

        if (file_exists($path)) {
            $res["headers"]["Content-Type"] = $mime_types[pathinfo($path, PATHINFO_EXTENSION)];
            $res["body"] = file_get_contents($path);
            return (TRUE);
        }
    };
}