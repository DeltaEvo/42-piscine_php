<?php
header('Content-Type: text/html');
if ($_SERVER["PHP_AUTH_USER"] === "zaz" || $_SERVER["PHP_AUTH_PW"] === "jaimelespetitsponeys") {
?><html><body>
Bonjour Zaz<br />
<?php
    echo "<img src='data:image/png;base64,";
    echo base64_encode(file_get_contents(__DIR__ . "/../img/42.png"));
    echo "'>";
?>
</body></html>
<?php
} else {
    header("HTTP/1.0 401 Unauthorized");
    header("WWW-Authenticate: Basic realm=\"Espace membres\"");
?>
<html><body>Cette zone est accessible uniquement aux membres du site</body></html>
<?php
}
?>