<?php

class Jaime extends Lannister {
    public function sleepWith($s) {
        if ($s instanceof Cersei) {
            echo "With pleasure, but only in a tower in Winterfell, then.", PHP_EOL;
        } else if ($s instanceof Sansa) {
            echo "Let's do this.", PHP_EOL;
        } else {
            echo "Not even if I'm drunk !", PHP_EOL;
        }
    }
}