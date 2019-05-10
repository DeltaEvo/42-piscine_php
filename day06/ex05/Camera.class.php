<?php

require_once('Matrix.class.php');

class Camera {
    public static $verbose = false;

    private $_origin;
    private $_tT;
    private $_tR;
    private $_proj;

    function __construct($array) {
        $this->_origin = $array["origin"];
        $vec = new Vector(array("dest" => $this->_origin));
        $this->_tT = new Matrix(array(
            "preset" => Matrix::TRANSLATION,
            "vtc" => $vec->opposite()
        ));
        $this->_tR = $array["orientation"]->symetric();
        $this->_proj = new Matrix(array(
            "preset" => Matrix::PROJECTION,
            "fov" => $array["fov"],
            "ratio" => array_key_exists("ratio", $array) ? $array["ratio"] : $array["width"] / $array["height"],
            "near" => $array["near"],
            "far" => $array["far"]
        ));
        if (self::$verbose)
            echo self::class, " instance constructed\n";
    }

    function __destruct() {
        if (self::$verbose)
            echo self::class, " instance destructed\n";
    }

    function __toString() {
        return sprintf("Camera( \n+ Origine: %s\n+ tT:\n%s\n+ tR:\n%s\n+ tR->mult( tT ):\n%s\n+ Proj:\n%s\n)",
            $this->_origin, $this->_tT, $this->_tR, $this->_tR->mult($this->_tT), $this->_proj);
    }

    public function watchVertex(Vertex $worldVertex) {
        return $this->_proj->mult($this->_tR->mult($this->_tT))->transformVertex($worldVertex);
    }

    public function watchMesh($mesh) {
        return array_map(function ($triangle) {
           return new Triangle(
               $this->watchVertex($triangle->a),
               $this->watchVertex($triangle->b),
               $this->watchVertex($triangle->c)
            );
        }, $mesh);
    }

    public static function doc() {
        return file_get_contents(__DIR__ . "/Camera.doc.txt");
    }
}