<?php

class UnholyFactory {
    private $_soldiers = [];

    public function absorb($soldier) {
        if (!($soldier instanceof Fighter)) {
            echo "(Factory can't absorb this, it's not a fighter)", PHP_EOL;
            return ;
        }
        foreach ($this->_soldiers as $s) {
            if ($soldier->type === $s->type) {
                echo "(Factory already absorbed a figther of type ", $soldier->type, ")", PHP_EOL;
                return ;
            }
        }
        $this->_soldiers[] = $soldier;
        echo "(Factory absorbed a figther of type ", $soldier->type, ")", PHP_EOL;
    }

    public function fabricate($type) {
        foreach($this->_soldiers as $s) {
            if ($type === $s->type) {
                echo "(Factory fabricates a figther of type ", $type, ")", PHP_EOL;
                return $s;
            }
        }
        echo "(Factory hasn't absorbed any fighter of type llama)", PHP_EOL;
        return null;
    }
}