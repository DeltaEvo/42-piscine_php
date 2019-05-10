<?php

function auth($login, $passwd) {
    $file = __DIR__ . "/../private/passwd";
    if (!file_exists($file))
        return (FALSE);
    if (($entries = @unserialize(file_get_contents($file))) === FALSE)
        return (FALSE);

    foreach($entries as $entry) {
        if ($entry["login"] === $login) {
            return $entry["passwd"] === hash("whirlpool", $passwd);
        }
    }
    return (FALSE);
}