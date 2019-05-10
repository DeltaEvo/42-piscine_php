<?php
get("/^\/api\/products\/?$/", function ($req, &$res) {
    if (($stmt = mysqli_prepare($req["db"], "SELECT id, name, price, img, category FROM products LEFT JOIN product_category ON id = product_category.product"))) {

        mysqli_stmt_execute($stmt);

        mysqli_stmt_bind_result($stmt, $id, $name, $price, $img, $category);

        $result = [];

        while (mysqli_stmt_fetch($stmt)) {
            if ($result[$id] === NULL) {
                $result[$id] = array(
                    "id" => $id,
                    "name" => $name,
                    "price" => $price,
                    "img" => "/api/files/$img",
                    "category" => $category === NULL ? NULL : [$category]
                );
            } else {
                $result[$id]["category"][] = $category;
            }
        }

        $res["body"] = array_values($result);

        mysqli_stmt_close($stmt);
    } 
});

post("/^\/api\/product\/?$/", function ($req, &$res) {
    $name = $req["body"]["name"];
    $price = $req["body"]["price"];
    $img = $req["body"]["img"];
    $categories = $req["body"]["categories"];

    if ($_SESSION["user"]["role"] === "admin") {
        if (($stmt1 = mysqli_prepare($req["db"], "INSERT INTO products (name, price, img) VALUES (?,?, ?)"))
            && ($stmt2 = mysqli_prepare($req["db"], "INSERT INTO product_category (product, category) VALUES (?, ?)"))) {
            mysqli_stmt_bind_param($stmt1, "sii", $name, $price, $img);

            mysqli_stmt_execute($stmt1);

            $product = mysqli_fetch_assoc(mysqli_query($req["db"], "SELECT LAST_INSERT_ID();"))["LAST_INSERT_ID()"];

            foreach($categories as $id) {
                mysqli_stmt_bind_param($stmt2, "ii", $product, $id);
                mysqli_stmt_execute($stmt2);
            }

            mysqli_stmt_close($stmt1);
            mysqli_stmt_close($stmt2);

            $res["body"] = array(
                "success" => true
            );
        } 
    } else {
        $res["status"] = 401;
        $res["body"] = "Unauthorized";
    }
});

delete("/^\/api\/product\/(?<id>.+)?$/", function ($req, &$res, $matches) {
    if (($stmt = mysqli_prepare($req["db"], "DELETE FROM products WHERE id = ?"))) {
        mysqli_stmt_bind_param($stmt, "i", $matches["id"]);

        $res["body"] = array(
            "success" => @mysqli_stmt_execute($stmt)
        );

        mysqli_stmt_close($stmt);
    } 
});