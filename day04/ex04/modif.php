<?php
if ($_POST["submit"] === "OK") {
    $file = __DIR__ . "/../private/passwd";
    $passwd = [];
    if (($contents = @file_get_contents($file)) === FALSE
        || ($passwd = @unserialize($contents)) === FALSE) {
            ?><?="ERROR\n"?><?php
            return ;
        }

    foreach($passwd as &$entry) {
        if ($entry["login"] === $_POST["login"]) {
            if ($entry["passwd"] === hash("whirlpool", $_POST["oldpw"])) {
                $entry["passwd"] = hash("whirlpool", $_POST["newpw"]);

                if (@file_put_contents($file, serialize($passwd)) === FALSE) {
                    ?><?="ERROR\n"?><?php
                } else {
                    header("Location: index.html");
                    ?><?="OK\n"?><?php
                }
            } else {
                ?><?="ERROR\n"?><?php
            }
            return ;
        }
    }
    
}
?><?="ERROR\n"?><?php
