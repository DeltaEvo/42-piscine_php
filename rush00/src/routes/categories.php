<?php
get("/^\/api\/categories\/?$/", function ($req, &$res) {
    if (($stmt = mysqli_prepare($req["db"], "SELECT id, name FROM categories"))) {

        mysqli_stmt_execute($stmt);

        mysqli_stmt_bind_result($stmt, $id, $name);

        $result = [];

        while (mysqli_stmt_fetch($stmt)) {
            $result[$id] = $name;
        }

        $res["body"] = $result;

        mysqli_stmt_close($stmt);
    } 
});