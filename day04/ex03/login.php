<?php
session_start();

include("auth.php");

if (auth($_GET["login"], $_GET["passwd"])) {
    $_SESSION["loggued_on_user"] = $_GET["login"];
    ?><?="OK\n"?><?php
} else {
    $_SESSION["loggued_on_user"] = '';
    ?><?="ERROR\n"?><?php
}
