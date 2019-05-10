#!/usr/bin/env php
<?php
    if ($argc == 2)
        echo implode(" ", preg_split("/ +/", trim($argv[1]))), "\n";