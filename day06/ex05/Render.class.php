<?php

class Render {
    const VERTEX = 0;
    const EDGE = 1;
    const RASTERIZE = 2;

    public static $verbose = false;

    private $_filename;
    private $_width;
    private $_height;
    private $_img;
    
    function __construct($width, $height, $filename) {
        $this->_filename = $filename;
        $this->_width = $width;
        $this->_height = $height;
        $this->_img = imagecreate($width, $height);
        imagecolorallocate($this->_img, 0, 0, 0);
        if (self::$verbose)
            echo self::class, " instance constructed\n";
    }

    function __destruct() {
        imagedestroy($this->_img);
        if (self::$verbose)
            echo self::class, " instance destructed\n";
    }

    public function renderVertex($vertex) {
        $color = imagecolorallocate($this->_img, $vertex->color->red, $vertex->color->green, $vertex->color->blue);
        $w = $vertex->w;
        imagesetpixel($this->_img,
            ($vertex->x / ($w / $this->_width) + $this->_width / 2),
            ($vertex->y / ($w / $this->_height) + $this->_height / 2),
            $color
        );
    }

    public function renderLine($p1, $p2) {
        $x1 = ($p1->x / ($p1->w / $this->_width) + $this->_width / 2);
        $y1 = ($p1->y / ($p1->w / $this->_height) + $this->_height / 2);
        $x2 = ($p2->x / ($p2->w / $this->_width) + $this->_width / 2);
        $y2 = ($p2->y / ($p2->w / $this->_height) + $this->_height / 2);
        $color = imagecolorallocate($this->_img, $p1->color->red, $p1->color->green, $p1->color->blue);

        imageline($this->_img, $x1, $y1, $x2, $y2, $color);
    }

    public function renderTriangle($t) {
        $x1 = ($t->a->x / ($t->a->w / $this->_width) + $this->_width / 2);
        $y1 = ($t->a->y / ($t->a->w / $this->_height) + $this->_height / 2);
        $x2 = ($t->b->x / ($t->b->w / $this->_width) + $this->_width / 2);
        $y2 = ($t->b->y / ($t->b->w / $this->_height) + $this->_height / 2);
        $x3 = ($t->c->x / ($t->c->w / $this->_width) + $this->_width / 2);
        $y3 = ($t->c->y / ($t->c->w / $this->_height) + $this->_height / 2);
        $color = imagecolorallocate($this->_img, $t->a->color->red, $t->b->color->green, $t->c->color->blue);

        imagepolygon($this->_img, array(
            $x1, $y1,
            $x2, $y2,
            $x3, $y3
        ), 3, $color);
    }

    public function renderMesh($mesh, $mode) {
        foreach ($mesh as $triangle) {
            switch ($mode) {
                case self::VERTEX:
                    $this->renderVertex($triangle->a);
                    $this->renderVertex($triangle->b);
                    $this->renderVertex($triangle->c);
                    break;
                case self::EDGE:
                    $this->renderLine($triangle->a, $triangle->b);
                    $this->renderLine($triangle->b, $triangle->c);
                    $this->renderLine($triangle->c, $triangle->a);
                    break;
                case self::RASTERIZE:
                    $this->renderTriangle($triangle);
                    break;
            }
        }
    }

    public function develop() {
        imagepng($this->_img, $this->_filename);
    }

    function __get($name) {
        $pname = "_$name";
        return $this->$pname;
    }

    public static function doc() {
        return file_get_contents(__DIR__ . "/" . self::name . ".doc.txt");
    }
}