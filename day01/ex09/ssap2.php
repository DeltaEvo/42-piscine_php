#!/usr/bin/env php
<?php

function weigth($char) {
    if (ord($char) >= ord('A') && ord($char) <= ord('Z'))
        return ord($char);
    else if (ord($char) >= ord('0') && ord($char) <= ord('9'))
        return ord($char) + 256;
    else
        return ord($char) + 256 * 256;
}
if ($argc >= 2) {
    $array = [];
    foreach (array_slice($argv, 1) as $word)
        $array = array_merge($array, preg_split("/ +/", trim($word)));
    usort($array, function($a, $b) {
        $a_up = strtoupper($a);
        $b_up = strtoupper($b);
        $a_len = strlen($a);
        $b_len = strlen($b);

        for ($i = 0; $i < $a_len && $i < $b_len; $i++)
            if ($a_up[$i] !== $b_up[$i])
                return weigth($a_up[$i]) - weigth($b_up[$i]);
        return $a_len - $b_len;
    });
    echo implode("\n", $array), "\n";
}