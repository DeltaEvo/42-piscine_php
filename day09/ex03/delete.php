<?php
if ($csv = @fopen(__DIR__ . "/list.csv", "r")) {
    $data = trim(fgets($csv));
    $id = $_GET["id"];

    while (($line = fgetcsv($csv, 0, ";")))
        if ($line[0] !== $id)
            $data .= "\n" . $line[0] . ";" . $line[1];
    if ($w = @fopen(__DIR__ . "/list.csv", "w+"))
        fwrite($w, $data);
}
