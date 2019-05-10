<?php

post("/^\/api\/auth\/signup\/?$/", function ($req, &$res) {
    $username = $req["body"]["username"];
    $password = $req["body"]["password"];

    if ($username && $password) {
        if (($stmt = mysqli_prepare($req["db"], "INSERT INTO users (username, password) VALUES (?,?)"))) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            mysqli_stmt_bind_param($stmt, "ss", $username, $hash);


            if (@mysqli_stmt_execute($stmt)) {
                $_SESSION["user"] = array(
                    "username" => $username,
                    "role" => 'user',
                    "img" => "/api/files/1"
                );
                $res["body"] = array("success" => true, "user" => $_SESSION["user"]);
            } else {
                $res["body"] = array("success" => false);
            }

            mysqli_stmt_close($stmt);
        } 
    }
});

post("/^\/api\/auth\/login\/?$/", function ($req, &$res) {
    $username = $req["body"]["username"];
    $password = $req["body"]["password"];

    if ($username && $password) {
        if (($stmt = mysqli_prepare($req["db"], "SELECT id, password, role, img FROM users WHERE username = ?"))) {
            mysqli_stmt_bind_param($stmt, "s", $username);

            mysqli_stmt_execute($stmt);

            mysqli_stmt_bind_result($stmt, $id, $hash, $role, $img);

            mysqli_stmt_fetch($stmt);

            mysqli_stmt_close($stmt);

            if (password_verify($password, $hash)) {
                $_SESSION["user"] = array(
                    "id" => $id,
                    "username" => $username,
                    "role" => $role,
                    "img" => "/api/files/$img"
                );
                $res["body"] = array("success" => true, "user" => $_SESSION["user"]);
            } else {
                $res["body"] = array("success" => false);
            }
        } 
    }
});

get("/^\/api\/auth\/logout\/?$/", function ($req, &$res) {
    $_SESSION["user"] = NULL;
    $res["body"] = "";
});
get("/^\/api\/auth\/who\/?$/", function ($req, &$res) {
    if ($_SESSION["user"] !== NULL)
        $res["body"] = $_SESSION["user"];
    else
        $res["body"] = FALSE;
});