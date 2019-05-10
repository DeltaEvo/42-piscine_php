#!/usr/bin/env php
<?php

if ($argc == 2) {

    $action = $argv[1];

    if ($action !== "moyenne" && $action !== "moyenne_user" && $action !== "ecart_moulinette")
        return;

    // Skip first line
    fgetcsv(STDIN, 0, ";");

    $notes = [];
    $moulinette = [];

    while ($line = fgetcsv(STDIN, 0, ";")) {
        if ($line[1] !== "") {
            if ($line[2] === "moulinette") {
                $moulinette[$line[0]] = $line[1];
            } else {
                $notes[$line[0]][] = $line[1];
            }
        }
    }

    ksort($notes, SORT_STRING);

    switch ($action) {
        case "moyenne":
            $values = array_values($notes);
            $len = array_reduce($values, function ($curr, $el) {
                return $curr + sizeof($el);
            });

            echo array_reduce($values, function ($curr, $el) {
                return $curr += array_reduce($el, function ($curr, $el) {
                    return $curr + $el;
                }, 0);
            }, 0) / $len, "\n";
            break;
        case "moyenne_user":
            foreach ($notes as $key=>$value) {
                echo $key, ":", array_reduce($value, function ($curr, $el) {
                    return $curr + $el;
                }, 0) / sizeof($value), "\n";
            }
            break;
        case "ecart_moulinette":
            foreach ($notes as $key=>$value) {
                echo $key, ":", (array_reduce($value, function ($curr, $el) {
                    return $curr + $el;
                }, 0) - $moulinette[$key] * sizeof($value)) / sizeof($value), "\n";
            }
            break;
    }
}
