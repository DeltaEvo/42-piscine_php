#!/usr/bin/env php
<?php
    date_default_timezone_set("Europe/Paris");
    $utmpx = fopen("/var/run/utmpx", "r");

    fread($utmpx, 1256);

    $entries = [];
    while ($data = fread($utmpx, 628)) {
        $entry = unpack("a256user/a4id/a32line/Lpid/Ltype/Lsec/Lusec/a256host", $data);

        if ($entry["type"] === 7)
            $entries[] = $entry;
    }

    usort($entries, function ($a, $b) {
        return $a["line"] > $b["line"];
    });

    foreach ($entries as $entry) {
        $user = $entry["user"];
        $line = $entry["line"];

        if ($pos = strpos($user, 0))
            $user = substr($user, 0, $pos);
        if ($pos = strpos($line, 0))
            $line = substr($line, 0, $pos);

        echo $user, " ", $line, "  ", date("M d H:i ", $entry["sec"]), "\n";
    }

    fclose($utmpx);