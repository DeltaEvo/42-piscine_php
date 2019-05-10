<?php
session_start();

include("auth.php");

if (auth($_POST["login"], $_POST["passwd"])) {
    $_SESSION["loggued_on_user"] = $_POST["login"];
?><html><body>
    <iframe src="chat.php" height="550px" width="100%"></iframe>
    <br />
    <iframe src="speak.php" height="50px" width="100%"></iframe>
</body></html><?php
} else {
    $_SESSION["loggued_on_user"] = '';
    ?><?="ERROR\n"?><?php
}
