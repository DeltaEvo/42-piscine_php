<?php

include 'middlewares/static.php';
include 'middlewares/json.php';

include 'config.php';

middleware(NULL, "/.*/", function (&$req, $res) {
    global $db_host, $db_user, $db_passwd, $db_name;
    mysqli_report(MYSQLI_REPORT_ALL ^ MYSQLI_REPORT_STRICT ^ MYSQLI_REPORT_INDEX);
    if (!($req["db"] = mysqli_connect($db_host, $db_user, $db_passwd, $db_name))) {
        die('Erreur de connexion (' . mysqli_connect_errno() . ') '
        . mysqli_connect_error());
    }
});

middleware(NULL, "/.*/", json_decode_middleware);

get("/.*/", middleware_static(__DIR__ . "/../static"));

include 'routes/auth.php';
include 'routes/categories.php';
include 'routes/files.php';
include 'routes/order.php';
include 'routes/products.php';
include 'routes/users.php';

middleware(NULL, "/.*/", function ($req, &$res) {
    if ($res["body"] === NULL) {
        $res["status"] = 404;
        $res["body"] = file_get_contents(__DIR__ . "/../static/404.html");
    }
});

middleware(NULL, "/.*/", json_encode_middleware);