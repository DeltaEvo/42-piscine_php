<?php
require_once("Color.class.php");

class Vertex {
    public static $verbose = false;

    private $_x;
    private $_y;
    private $_z;
    private $_w;
    private $_color;

    function __construct($array) {
        $this->_x = floatval($array["x"]);
        $this->_y = floatval($array["y"]);
        $this->_z = floatval($array["z"]);
        if (array_key_exists("w", $array))
            $this->_w = floatval($array["w"]);
        else
            $this->_w = 1.0;
        if (array_key_exists("color", $array))
            $this->_color = $array["color"];
        else
            $this->_color = new Color(array("red" => 255, "green" => 255, "blue" => 255));

        if (self::$verbose)
            echo $this, " constructed\n";
    }

    function __destruct() {
        if (self::$verbose)
            echo $this, " destructed\n";
    }

    public function __toString() {
        if (self::$verbose)
            return sprintf("Vertex( x: %.2f, y: %.2f, z:%.2f, w:%.2f, %s )", $this->x, $this->y, $this->z, $this->w, $this->color);
        else
            return sprintf("Vertex( x: %.2f, y: %.2f, z:%.2f, w:%.2f )", $this->x, $this->y, $this->z, $this->w);
    }

    public static function doc() {
        return file_get_contents(__DIR__ . "/Vertex.doc.txt");
    }

    function __set($name, $value) {
        $pname = "_$name";
        $this->$pname = $value;
    }

    function __get($name) {
        $pname = "_$name";
        return $this->$pname;
    }
}