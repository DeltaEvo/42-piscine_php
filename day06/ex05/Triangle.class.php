<?php
class Triangle {
    public static $verbose = false;

    private $_a;
    private $_b;
    private $_c;

    function __construct($a, $b, $c) {
        $this->_a = $a;
        $this->_b = $b;
        $this->_c = $c;

        if (self::$verbose)
            echo self::class, " instance constructed\n";
    }

    function __destruct() {
        if (self::$verbose)
            echo self::class, " instance destructed\n";
    }

    function __get($name) {
        $pname = "_$name";
        return $this->$pname;
    }

    public static function doc() {
        return file_get_contents(__DIR__ . "/" . self::name . ".doc.txt");
    }
}