#!/usr/bin/env php
<?php
if ($argc >= 2) {
    $array = preg_split("/ +/", trim($argv[1]));
    $array[] = array_shift($array);
    echo implode(" ", $array), "\n";
}