<?php
if ($_POST["submit"] === "OK" && $_POST["login"] != '' && $_POST["passwd"] != '') {
    $file = __DIR__ . "/../private/passwd";
    @mkdir(__DIR__ . "/../private");
    $passwd = [];
    if (file_exists($file))
        if (($passwd = @unserialize(file_get_contents($file))) === FALSE) {
            ?><?="ERROR\n"?><?php
            return ;
        }

    foreach($passwd as $entry) {
        if ($entry["login"] === $_POST["login"]) {
            ?><?="ERROR\n"?><?php
            return ;
        }
    }

    $passwd[] = array(
        "login" => $_POST["login"],
        "passwd" => hash("whirlpool", $_POST["passwd"])
    );

    if (@file_put_contents($file, serialize($passwd)) === FALSE) {
        ?><?="ERROR\n"?><?php
    } else {
        header("Location: index.html");
        ?><?="OK\n"?><?php
    }
} else {
    ?><?="ERROR\n"?><?php
}
