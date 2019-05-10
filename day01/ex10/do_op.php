#!/usr/bin/env php
<?php
if ($argc === 4) {
    list ($nb1, $op, $nb2) = array_map(trim, array_slice($argv, 1));

    switch ($op) {
        case "+":
            echo $nb1 + $nb2, "\n";
            break;
        case "-":
            echo $nb1 - $nb2, "\n";
            break;
        case "*":
            echo $nb1 * $nb2, "\n";
            break;
        case "/":
            echo $nb1 / $nb2, "\n";
            break;
        case "%":
            echo $nb1 % $nb2, "\n";
            break;
    }
} else
    echo "Incorrect Parameters\n";
    