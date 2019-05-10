<?php

abstract class Fighter {
    abstract public function fight($target);
    public $type;

    function __construct($type) {
        $this->type = $type;
    }
}