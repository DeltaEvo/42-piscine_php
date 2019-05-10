#!/usr/bin/env php
<?php
if ($argc === 2) {
    $matches = [];
    if (!preg_match("/^([+-]?\d+) *([\+\-\*\/\%]) *([+-]?\d+)$/", trim($argv[1]), $matches))
        echo "Syntax Error\n";
    else {
        list (, $nb1, $op, $nb2) = $matches;

        if (is_numeric($nb1) && is_numeric($nb2)) {
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
                    if ($nb2 == 0)
                        echo "Syntax Error\n";
                    else
                        echo $nb1 / $nb2, "\n";
                    break;
                case "%":
                    if ($nb2 == 0)
                        echo "Syntax Error\n";
                    else
                        echo $nb1 % $nb2, "\n";
                    break;
                default:
                    echo "Syntax Error\n";
                    break;
            }
        } else
            echo "Syntax Error\n";
    }
} else
    echo "Incorrect Parameters\n";
    