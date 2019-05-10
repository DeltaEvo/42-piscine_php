#!/usr/bin/env php
<?php
if ($argc >= 3 && ($csv = @fopen($argv[1], "r"))) {
    $key_name = $argv[2];

    $header = fgetcsv($csv, 0, ";");

    if (($key = array_search($key_name, $header)) !== FALSE) {

        $table = [];

        while (($line = fgetcsv($csv, 0, ";"))) {
            $index = $line[$key];
            $data = array_combine($header, array_slice($line, 0, sizeof($header)));
            $table[$index] = $data; 
        }

        $rtable = [];

        foreach ($header as $key)
            $rtable[$key] = array_combine(array_keys($table), array_column($table, $key));

        while (true) {
            echo "Entrez votre commande: ";
            if (!($input = fgets(STDIN)))
                break ;
            extract($rtable, EXTR_SKIP);
            if (@eval($input) === FALSE) {
                $error = error_get_last();
                echo "PHP Parse error : ". $error["message"], " in ", $error["file"], "\n";
            }
        }
        echo "\n";
    }
}