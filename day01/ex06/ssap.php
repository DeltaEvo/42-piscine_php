#!/usr/bin/env php
<?php
if ($argc >= 2) {
    $array = [];
    foreach (array_slice($argv, 1) as $word)
        $array = array_merge($array, preg_split("/ +/", trim($word)));
    sort($array, SORT_STRING);
    echo implode("\n", $array), "\n";
}