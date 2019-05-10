#!/usr/bin/env php
<?php
include "config.php";

echo "Welcome\n";
mysqli_report(MYSQLI_REPORT_ALL ^ MYSQLI_REPORT_STRICT);
if (!($db = mysqli_connect($db_host, $db_user, $db_passwd, $db_name))) {
    die('Erreur de connexion (' . mysqli_connect_errno() . ') '
    . mysqli_connect_error());
}

mysqli_query($db, <<<EOF
CREATE TABLE users (
    id       INT AUTO_INCREMENT NOT NULL,
    username VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role     ENUM('user', 'admin') DEFAULT 'user' NOT NULL,
    img      INT DEFAULT 1 NOT NULL,

    PRIMARY KEY (id),
    UNIQUE KEY (username)
)
EOF
);

$root = password_hash("root", PASSWORD_DEFAULT);
mysqli_query($db, "INSERT INTO users (username, password, role) VALUES ('gru', '$root', 'admin')");

mysqli_query($db, <<<EOF
CREATE TABLE products (
    id       INT NOT NULL AUTO_INCREMENT,
    name     VARCHAR(255) NOT NULL,
    price    BIGINT NOT NULL,
    img      INT NOT NULL,

    PRIMARY KEY (id)
)
EOF
);

mysqli_query($db, <<<EOF
CREATE TABLE categories (
    id       INT NOT NULL AUTO_INCREMENT,
    name     VARCHAR(255) NOT NULL,

    PRIMARY KEY (id)
)
EOF
);

function addCategory($name) {
    global $db;
    mysqli_query($db, "INSERT INTO categories (name) VALUES ('$name')");
    return mysqli_fetch_assoc(mysqli_query($db, "SELECT LAST_INSERT_ID();"))["LAST_INSERT_ID()"];
}

$vehicule = addCategory("Vehicule");
$derive = addCategory("Derivé");

mysqli_query($db, <<<EOF
CREATE TABLE product_category (
    product       INT NOT NULL,
    category      INT NOT NULL
)
EOF
);

function linkProduct($product, $category) {
    global $db;
    mysqli_query($db, "INSERT INTO product_category (product, category) VALUES ($product, $category)");
}

mysqli_query($db, <<<EOF
CREATE TABLE files (
    id       INT NOT NULL AUTO_INCREMENT,
    type     VARCHAR(25) NOT NULL,
    data     MEDIUMBLOB,

    PRIMARY KEY (id)
)
EOF
);


function addImage($file) {
    global $db;
    $type = exif_imagetype($file);
    $data = addslashes(file_get_contents($file));
    $mime = image_type_to_mime_type($type);
    mysqli_query($db, "INSERT INTO files (type, data) VALUES('$mime', '{$data}')");
    return mysqli_fetch_assoc(mysqli_query($db, "SELECT LAST_INSERT_ID();"))["LAST_INSERT_ID()"];
}

// Image one
if (addImage(__DIR__ . "/static/resources/minion.jpeg") != "1") {
    echo "Warning: Default image don't have id one";
}

echo "bob: ", $bob = addImage(__DIR__ . "/static/resources/bob.jpg"), "\n";
echo "bobo: ", $bobo = addImage(__DIR__ . "/static/resources/Bobo.jpg"), "\n";
echo "Broum Broum: ", $broum_broum = addImage(__DIR__ . "/static/resources/Broum_Broum.jpg"), "\n";
echo "Debilus: ", $debilus = addImage(__DIR__ . "/static/resources/Debilus.jpg"), "\n";
echo "Lennon: ", $lenon = addImage(__DIR__ . "/static/resources/Lennon.jpg"), "\n";
echo "Super Minion: ", $super_minion = addImage(__DIR__ . "/static/resources/Super minion.jpg"), "\n";
echo "Crominion: ", $crominion = addImage(__DIR__ . "/static/resources/Crominion.jpeg"), "\n";

function addProduct($name, $price, $img) {
    global $db;

    mysqli_query($db, "INSERT INTO products (name, price, img) VALUES('$name', $price, $img)");
    return mysqli_fetch_assoc(mysqli_query($db, "SELECT LAST_INSERT_ID();"))["LAST_INSERT_ID()"];
}



$product = addProduct("Crominion", 3000, $crominion);
linkProduct($product, $derive);
linkProduct($product, $vehicule);

$product = addProduct("Super Minion", 100000, $super_minion);
linkProduct($product, $derive);

addProduct("Debilus", 1, $debilus);

$product = addProduct("Broum Broum", 50000, $broum_broum);
linkProduct($product, $vehicule);

mysqli_query($db, <<<EOF
CREATE TABLE orders (
    id            INT NOT NULL AUTO_INCREMENT,
    user          INT,
    details       JSON NOT NULL,

    PRIMARY KEY (id)
)
EOF
);

mysqli_query($db, <<<EOF
CREATE TABLE order_product (
    order_id  INT NOT NULL,
    product   INT NOT NULL,
    quantity  INT NOT NULL
)
EOF
);