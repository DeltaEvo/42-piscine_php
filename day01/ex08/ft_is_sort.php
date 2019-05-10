<?php

function ft_is_sort($array) {
    $sorted = $array;
    sort($sorted, SORT_STRING);

    return sizeof(array_diff_assoc($array, $sorted)) == 0;
}