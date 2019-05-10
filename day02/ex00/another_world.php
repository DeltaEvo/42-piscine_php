#!/usr/bin/env php
<?php
if ($argc >= 2)
        echo implode(" ", preg_split("/[ \t]+/", trim($argv[1]))), "\n";