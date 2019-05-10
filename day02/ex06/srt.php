#!/usr/bin/env php
<?php
date_default_timezone_set('Europe/Paris');
if ($argc >= 2 && ($srt = @file_get_contents($argv[1]))) {
    $subtitles = explode("\n\n", trim($srt));
    $mapped = [];
    foreach($subtitles as $subtitle) {
        $matches = [];
        if (preg_match("/^(?<id>\d+)\n(?<full>(?<hours>\d{2}):(?<minutes>\d{2}):(?<seconds>\d{2}),(?<mili>\d{3}) --> \d{2}:\d{2}:\d{2},\d{3}\n(?<text>(?:.*(?:\n|$)){1,2}))$/", $subtitle, $matches)) {
            $mapped[] = array_intersect_key($matches, array('full' => 1, 'id' => 1, 'text' => 1,
                'hours' => 1, 'minutes' => 1, 'seconds' => 1, 'mili' => 1));
        }
    }

    usort($mapped, function ($a, $b) {
        return (mktime($a["hours"], $a["minutes"], $a["seconds"]) * 1000 + $a["mili"])
                - (mktime($b["hours"], $b["minutes"], $b["seconds"]) * 1000 + $b["mili"]);
    });

    echo join("\n", array_map(function ($sub, $i) {
        return ($i + 1) . "\n" . $sub["full"] . "\n";
    }, $mapped, array_keys($mapped)));
}
