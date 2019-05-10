#!/usr/bin/env php
<?php
if ($argc >= 3) {
    $db = [];
    foreach(array_slice($argv, 2) as $arg) {
        list ($key, $value) = explode(":", $arg, 2);
        $db[$key] = $value;
    }
    if (array_key_exists($argv[1], $db))
        echo $db[$argv[1]], "\n";
}