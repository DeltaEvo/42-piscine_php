<?php
get("/^\/api\/users\/?$/", function ($req, &$res) {
    if ($_session["user"]["role"] === "admin") {
        if (($stmt = mysqli_prepare($req["db"], "insert into users values (?,?,'user')"))) {
            $hash = password_hash($password, password_default);
            mysqli_stmt_bind_param($stmt, "ss", $username, $hash);

            $res["body"] = array(
                "success" => @mysqli_stmt_execute($stmt)
            );

            mysqli_stmt_close($stmt);
        } 
    }
});

post("/^\/api\/users\/?$/", function ($req, &$res) {
    $username = $req["body"]["username"];
    $password = $req["body"]["password"];
    $role = $req["body"]["role"];

    if ($_SESSION["user"]["role"] === "admin") {
        if ($username && $password && $role) {
            if (($stmt = mysqli_prepare($req["db"], "INSERT INTO users (username, password, role) VALUES (?,?,?)"))) {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                mysqli_stmt_bind_param($stmt, "sss", $username, $hash, $role);

                if (@mysqli_stmt_execute($stmt)) {
                    $res["body"] = array("success" => true);
                } else {
                    $res["body"] = array("success" => false);
                }

                mysqli_stmt_close($stmt);
            } 
        }
    } else {
        $res["status"] = 401;
        $res["body"] = "Unauthorized";
    }
});

delete("/^\/api\/users\/?$/", function ($req, &$res) {
    if (($stmt = mysqli_prepare($req["db"], "DELETE FROM users WHERE id = ?"))) {
        mysqli_stmt_bind_param($stmt, "i", $_SESSION["user"]["id"]);

        $res["body"] = array(
            "success" => @mysqli_stmt_execute($stmt)
        );

        mysqli_stmt_close($stmt);
    } 
});

delete("/^\/api\/users\/(?<username>.+)$/", function ($req, &$res, $match) {
    if ($_SESSION["user"]["role"] === "admin") {
        if (($stmt = mysqli_prepare($req["db"], "DELETE FROM users WHERE username = ?"))) {
            mysqli_stmt_bind_param($stmt, "s", $match["username"]);

            $res["body"] = array(
                "success" => @mysqli_stmt_execute($stmt) && mysqli_affected_rows($req["db"]) === 1
            );

            mysqli_stmt_close($stmt);
        } 
    } else {
        $res["status"] = 401;
        $res["body"] = "Unauthorized";
    }
});