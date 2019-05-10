<?php
function ft_split($str) {
    $array = preg_split("/ +/", trim($str));
    sort($array);
    return $array;
}