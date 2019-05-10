<?php
date_default_timezone_set("Europe/Paris");
$file = __DIR__ . "/../private/chat";
if (file_exists($file)) {
    if (($handle = fopen($file, "r")) === FALSE) {
        ?><?="ERROR\n"?><?php
        return ;
    }
    flock($handle, LOCK_EX);
    if (($chat = @unserialize(file_get_contents($file))) === FALSE) {
        ?><?="ERROR 2\n"?><?php
        return ;
    }
    foreach($chat as $msg) {
        ?>[<?=date("H:i", $msg["time"])?>] <b><?=$msg["login"]?></b>: <?=$msg["msg"]?><br /><?="\n"?><?php
    }
    flock($handle, LOCK_UN);
    fclose($handle);
}