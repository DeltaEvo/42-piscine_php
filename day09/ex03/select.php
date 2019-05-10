<?php
header("Content-Type: application/json");

if ($csv = @fopen(__DIR__ . "/list.csv", "r")) {
    fgetcsv($csv, 0, ";");

    $data = [];

    while (($line = fgetcsv($csv, 0, ";")))
        if (sizeof($line) == 2)
            $data[] = array_combine(["id", "value"], $line);

    echo json_encode($data, JSON_PRETTY_PRINT);
} else
    echo "[]";
