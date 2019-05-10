<?php
require_once('Vertex.class.php');

class Matrix {
    public static $verbose = false;

    const IDENTITY = 0;
    const SCALE = 1;
    const RX = 2;
    const RY = 3;
    const RZ = 4;
    const TRANSLATION = 5;
    const PROJECTION = 6;
    const RAW = 7;

    private $inner;

    function __construct($array) {
        $scale = $array["scale"];
        $angle = $array["angle"];
        $vtc = $array["vtc"];
        $cos = cos($angle);
        $sin = sin($angle);

        switch ($array["preset"]) {
            case self::IDENTITY:
                $this->inner = array(
                    array(1, 0, 0, 0),
                    array(0, 1, 0, 0),
                    array(0, 0, 1, 0),
                    array(0, 0, 0, 1)
                );
                break;
            case self::SCALE:
                $this->inner = array(
                    array($scale, 0, 0, 0),
                    array(0, $scale, 0, 0),
                    array(0, 0, $scale, 0),
                    array(0, 0, 0, 1)
                );
                break;
            case self::RX:
                $this->inner = array(
                    array(1, 0, 0, 0),
                    array(0, $cos, -$sin, 0),
                    array(0, $sin, $cos, 0),
                    array(0, 0, 0, 1)
                );
                break;
            case self::RY:
                $this->inner = array(
                    array($cos, 0, $sin, 0),
                    array(0, 1, 0, 0),
                    array(-$sin, 0, $cos, 0),
                    array(0, 0, 0, 1)
                );
                break;
            case self::RZ:
                $this->inner = array(
                    array($cos, -$sin, 0, 0),
                    array($sin, $cos, 0, 0),
                    array(0, 0, 1, 0),
                    array(0, 0, 0, 1)
                );
                break;
            case self::TRANSLATION:
                $this->inner = array(
                    array(1, 0, 0, $vtc->x),
                    array(0, 1, 0, $vtc->y),
                    array(0, 0, 1, $vtc->z),
                    array(0, 0, 0, 1)
                );
                break;
            case self::PROJECTION:
                $fov = deg2rad($array["fov"]) * 0.5;
                $ratio = $array["ratio"];
                $near = $array["near"];
                $far = $array["far"];

                $this->inner = array(
                    array(1 / ($ratio * tan($fov)), 0, 0, 0),
                    array(0, 1 / tan($fov), 0, 0),
                    array(0, 0, - ($far + $near) / ($far - $near), - (2 * $far * $near) / ($far - $near)),
                    array(0, 0, -1, 0)
                );
                break;
            case self::RAW:
                $this->inner = $array["data"];
                break;
        }

        if (self::$verbose && $array["preset"] !== self::RAW) {
            $types = array("", "SCALE", "Ox ROTATION", "Oy ROTATION", "Oz ROTATION", "TRANSLATION", "PROJECTION", "RAW");

            if ($array["preset"] === self::IDENTITY)
                echo "Matrix IDENTITY instance constructed\n";
            else
                echo "Matrix ", $types[$array["preset"]], " preset instance constructed\n";
        }
    }

    function __destruct() {
        if (self::$verbose)
            echo "Matrix instance destructed\n";
    }

    function __toString() {
        $head = "M | vtcX | vtcY | vtcZ | vtxO\n-----------------------------\n";
        return $head . join(array_map(function ($array, $col) {
            return sprintf("%s | % .2f | %.2f | %.2f | %.2f", $col, $array[0], $array[1], $array[2], $array[3]);
        }, $this->inner, ['x', 'y', 'z', 'w']), "\n");
    }

    public function mult(self $rhs) {
        $data = [];
        foreach (range(0, 3) as $y) {
            foreach (range(0, 3) as $x) {
                $data[$y][$x] = $this->inner[$y][0] * $rhs->inner[0][$x]
                              + $this->inner[$y][1] * $rhs->inner[1][$x]
                              + $this->inner[$y][2] * $rhs->inner[2][$x]
                              + $this->inner[$y][3] * $rhs->inner[3][$x];
            }
        }
        return new self(array("preset" => self::RAW, "data" => $data));
    }

    public function symetric() {
        $data = [];
        foreach (range(0, 3) as $y) {
            foreach (range(0, 3) as $x) {
                $data[$x][$y] = $this->inner[$y][$x];
            }
        }
        return new self(array("preset" => self::RAW, "data" => $data));
    }

    public function transformVertex(Vertex $rhs) {
        $vec = [];
        foreach (range(0, 3) as $i) {
            $vec[$i] = $rhs->x * $this->inner[$i][0]
                     + $rhs->y * $this->inner[$i][1]
                     + $rhs->z * $this->inner[$i][2]
                     + $rhs->w * $this->inner[$i][3];
        }
        $vec[] = $rhs->color;
        return new Vertex(array_combine(['x', 'y', 'z', 'w', 'color'], $vec));
    }

    public static function doc() {
        return file_get_contents(__DIR__ . "/Vector.doc.txt");
    }
}