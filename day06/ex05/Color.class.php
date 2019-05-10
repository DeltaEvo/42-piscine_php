<?php
class Color {
    public static $verbose = false;

    public $red;
    public $green;
    public $blue;

    function __construct($array) {
        if (array_key_exists("rgb", $array)) {
            $rgb = intval($array["rgb"]);

            $this->red = ($rgb >> 16) & 0xFF;
            $this->green = ($rgb >> 8) & 0xFF;
            $this->blue = $rgb & 0xFF;
        } else {
            $this->red = intval($array["red"]);
            $this->green = intval($array["green"]);
            $this->blue = intval($array["blue"]);
        }

        if (self::$verbose)
            echo $this, " constructed.\n";
    }

    function __destruct() {
        if (self::$verbose)
            echo $this, " destructed.\n";
    }

    public function add(self $color) {
        return new self(array(
            "red" => $this->red + $color->red,
            "green" => $this->green + $color->green,
            "blue" => $this->blue + $color->blue
        ));
    }

    public function sub(self $color) {
        return new self(array(
            "red" => $this->red - $color->red,
            "green" => $this->green - $color->green,
            "blue" => $this->blue - $color->blue
        ));
    }

    public function mult($factor) {
        return new self(array(
            "red" => $this->red * $factor,
            "green" => $this->green * $factor,
            "blue" => $this->blue * $factor
        ));
    }

    public function __toString() {
        return sprintf("Color( red: %3d, green: %3d, blue: %3d )", $this->red, $this->green, $this->blue);
    }

    public static function doc() {
        return file_get_contents(__DIR__ . "/Color.doc.txt");
    }
}