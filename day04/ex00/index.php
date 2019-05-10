<?php
session_start();

if ($_GET["submit"] === "OK") {
    if ($_GET["login"])
        $_SESSION["login"] = $_GET["login"];
    if ($_GET["passwd"])
        $_SESSION["passwd"] = $_GET["passwd"];
}
?>
<html><body>
<form method="GET">
    Identifiant: <input type="text" name="login" value="<?=$_SESSION["login"]?>"/>
    <br />
    Mot de passe: <input type="password" name="passwd" value="<?=$_SESSION["passwd"]?>" />
    <br />
    <input type="submit" name="submit" value="OK" />
</form>
</body></html>