<?php
post("/^\/api\/order\/?$/", function ($req, &$res) {
    $details = $req["body"]["details"];
    $cart = $req["body"]["cart"];

    if (($stmt1 = mysqli_prepare($req["db"], "INSERT INTO orders (user, details) VALUES (?,?)"))
        && ($stmt2 = mysqli_prepare($req["db"], "INSERT INTO order_product (order_id, product, quantity) VALUES (?, ?, ?)"))) {
        mysqli_stmt_bind_param($stmt1, "is", $_SESSION["user"]["id"], json_encode($details));

        mysqli_stmt_execute($stmt1);

        $order = mysqli_fetch_assoc(mysqli_query($req["db"], "SELECT LAST_INSERT_ID();"))["LAST_INSERT_ID()"];

        foreach($cart as $el) {
            mysqli_stmt_bind_param($stmt2, "iii", $order, $el["id"], $el["quantity"]);
            mysqli_stmt_execute($stmt2);
        }

        mysqli_stmt_close($stmt1);
        mysqli_stmt_close($stmt2);

        $res["body"] = array(
            "success" => true
        );
    } 
});

get("/^\/api\/orders\/?$/", function ($req, &$res) {
    if ($_SESSION["user"]["role"] === "admin") {
        if (($stmt = mysqli_prepare($req["db"], <<<EOF
    SELECT orders.id, details, products.name, products.price, products.img, products.id, order_product.quantity FROM orders
    LEFT JOIN order_product ON orders.id = order_product.order_id
    LEFT JOIN products ON order_product.product = products.id;
EOF
))) {
            mysqli_stmt_execute($stmt);

            mysqli_stmt_bind_result($stmt, $id, $details, $product_name, $product_price, $product_img, $product_id, $quantity);

            $result = [];

            while (mysqli_stmt_fetch($stmt)) {
                $product = array(
                    "id" => $product_id,
                    "name" => $product_name,
                    "img" => "/api/img/$product_img",
                    "price" => $product_price,
                    "quantity" => $quantity
                );
                if ($result[$id] === NULL) {
                    $result[$id] = array(
                        "id" => $id,
                        "details" => json_decode($details, TRUE),
                        "products" => array($product)
                    );
                } else {
                    $result[$id]["products"][] = $product;
                }
            }

            $res["body"] = array_values($result);

            mysqli_stmt_close($stmt);
        } 
    } else {
        $res["status"] = 401;
        $res["body"] = "Unauthorized";
    }
});

delete("/^\/api\/orders\/(?<id>\d+)$/", function ($req, &$res, $match) {
    if ($_SESSION["user"]["role"] === "admin") {
        if (($stmt = mysqli_prepare($req["db"], "DELETE FROM orders WHERE id = ?"))) {
            mysqli_stmt_bind_param($stmt, "i", $match["id"]);

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

post("/^\/api\/orders\/?$/", function ($req, &$res) {
    $id = $req["body"]["id"];
    $product = $req["body"]["product"];
    $quantity = $req["body"]["quantity"];

    if ($_SESSION["user"]["role"] === "admin") {
        if (($stmt = mysqli_prepare($req["db"], "UPDATE order_product SET product = ?, quantity = ? WHERE order_id = ?"))) {
            mysqli_stmt_bind_param($stmt, "iii", $product, $quantity, $id);

            $res["body"] = array(
                "success" => @mysqli_stmt_execute($stmt)
            );

            mysqli_stmt_close($stmt);
        } 
    } else {
        $res["status"] = 401;
        $res["body"] = "Unauthorized";
    }
});