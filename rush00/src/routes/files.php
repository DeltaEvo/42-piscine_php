<?php
get("/^\/api\/files\/(?<id>\d+)\/?$/", function ($req, &$res, $matches) {
    if (($stmt = mysqli_prepare($req["db"],
            "SELECT type, data FROM files WHERE id = ?"))) {
        mysqli_stmt_bind_param($stmt, "i", $matches["id"]);

        mysqli_stmt_execute($stmt);

        mysqli_stmt_bind_result($stmt, $type, $data);

        mysqli_stmt_fetch($stmt);

        mysqli_stmt_close($stmt);

        $res["headers"]["Cache-Control"] =  "max-age=86400";
        $res["headers"]["Content-Type"] = $type;
        $res["body"] = $data;
    } 
});