<?php
header("Content-Type: text/plain");

if (!file_exists(__DIR__ . "/list.csv"))
    @file_put_contents(__DIR__ . "/list.csv", "id;i am a todo");

$id = uniqid();
if (@file_put_contents(__DIR__ . "/list.csv", "\n" . $id . ";". $_GET["text"], FILE_APPEND))
    echo $id;