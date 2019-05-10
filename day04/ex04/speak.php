<?php

session_start();
if ($_SESSION["loggued_on_user"] != '') {
    if ($_POST["submit"] === "Send") {
        $file = __DIR__ . "/../private/chat";
        @mkdir(__DIR__ . "/../private");
        $exist = file_exists($file);
        if (($handle = fopen($file, "a")) === FALSE) {
            ?><?="ERROR\n"?><?php
            return ;
        }
        flock($handle, LOCK_EX);
        $chat = [];
        if ($exist)
            if (($chat = @unserialize(file_get_contents($file))) === FALSE) {
                ?><?="ERROR 2\n"?><?php
                return ;
            }

        $chat[] = array(
            "login" => $_SESSION["loggued_on_user"],
            "time" => time(),
            "msg" => $_POST["msg"]
        );

        if (@file_put_contents($file, serialize($chat)) === FALSE) {
            ?><?="ERROR 3\n"?><?php
        }
        flock($handle, LOCK_UN);
        fclose($handle);
    }
?><html><body>
    <form method="POST">
        <input type="text" name="msg" value="" />
        <input type="submit" name="submit" value="Send" />
    </form>
    <script>top.frames[0].location = 'chat.php';</script>
</body></html><?php
} else {
    ?><?="ERROR\n"?><?php
}