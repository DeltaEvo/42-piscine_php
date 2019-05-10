<?php
require_once("Vertex.class.php");

class Vector {
    public static $verbose = false;

    private $_x;
    private $_y;
    private $_z;
    private $_w;

    function __construct($array) {
        $dest = $array["dest"];
        if (array_key_exists("orig", $array))
            $orig = $array["orig"];
        else
            $orig = new Vertex(array("x" => 0, "y" => 0, "z" => 0));

        $this->_x = $dest->x - $orig->x;
        $this->_y = $dest->y - $orig->y;
        $this->_z = $dest->z - $orig->z;
        $this->_w = 0.0;

        if (self::$verbose)
            echo $this, " constructed\n";
    }

    function __destruct() {
        if (self::$verbose)
            echo $this, " destructed\n";
    }

    public function __toString() {
        return sprintf("Vector( x:% .2f, y:% .2f, z:% .2f, w:% .2f )", $this->x, $this->y, $this->z, $this->w);
    }

    public function magnitude() {
        return sqrt(pow($this->x, 2) + pow($this->y, 2) + pow($this->z, 2));
    }

    public function normalize() {
        $magnitude = $this->magnitude();

        return new self(array("dest" => new Vertex(array(
            "x" => $this->x / $magnitude,
            "y" => $this->y / $magnitude,
            "z" => $this->z / $magnitude
        ))));
    }

    public function add(self $rhs) {
        return new self(array("dest" => new Vertex(array(
            "x" => $this->x + $rhs->x,
            "y" => $this->y + $rhs->y,
            "z" => $this->z + $rhs->z
        ))));
    }

    public function sub(self $rhs) {
        return new self(array("dest" => new Vertex(array(
            "x" => $this->x - $rhs->x,
            "y" => $this->y - $rhs->y,
            "z" => $this->z - $rhs->z
        ))));
    }

    public function opposite() {
        return new self(array("dest" => new Vertex(array(
            "x" => -$this->x,
            "y" => -$this->y,
            "z" => -$this->z
        ))));
    }

    public function scalarProduct($k) {
        return new self(array("dest" => new Vertex(array(
            "x" => $this->x * $k,
            "y" => $this->y * $k,
            "z" => $this->z * $k
        ))));
    }

    public function dotProduct(self $rhs) {
        return ($this->x * $rhs->x +
                $this->y * $rhs->y +
                $this->z * $rhs->z);
    }

    public function cos(self $rhs) {
        return $this->dotProduct($rhs) / ($this->magnitude() * $rhs->magnitude());
    }

    public function crossProduct(self $rhs) {
        return new self(array("dest" => new Vertex(array(
            "x" => $this->y * $rhs->z - $this->z * $rhs->y,
            "y" => $this->z * $rhs->x - $this->x * $rhs->z,
            "z" => $this->x * $rhs->y - $this->y * $rhs->x
        ))));
    }

    public static function doc() {
        return file_get_contents(__DIR__ . "/Vector.doc.txt");
    }

    function __get($name) {
        $pname = "_$name";
        return $this->$pname;
    }
}