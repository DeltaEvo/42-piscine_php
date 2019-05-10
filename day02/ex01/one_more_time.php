#!/usr/bin/env php
<?php
date_default_timezone_set('Europe/Paris');
if ($argc == 2) {
    $matches = [];
    if (!preg_match("/^([Ll]undi|[Mm]ardi|[Mm]ercredi|[Jj]eudi|[Vv]endredi|[Ss]amedi|[Dd]imanche) (\d{1,2}) ([Jj]anvier|[Ff][ée]vrier|[Mm]ars|[Aa]vril|[Mm]ai|[Jj]uin|[Jj]uillet|[Aa]o[ûu]t|[Ss]eptembre|[Oo]ctobre|[Nn]ovembre|[Dd][ée]cembre) (\d{4}) (\d{2}):(\d{2}):(\d{2})$/", $argv[1], $matches))
        echo "Wrong Syntax\n";
    else {
        list (,$day_name, $day, $month_name, $year, $hour, $minutes, $seconds) = $matches;

        $month = array_search(strtolower(strtr($month_name, "éû", "eu")), ["janvier", "fevrier", "mars", "avril", "mai", "juin", "juillet", "aout", "septembre", "octobre", "novembre", "decembre"]) + 1;
        $time = mktime($hour, $minutes, $seconds, $month, $day, $year);

        if (date('w', $time) != array_search(strtolower($day_name), ["lundi", "mardi", "mercredi", "jeudi", "vendredi", "samedi", "dimanche"]) + 1)
            echo "Wrong Syntax\n";
        else
            echo $time, "\n";
    }
}