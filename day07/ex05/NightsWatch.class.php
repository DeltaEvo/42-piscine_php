<?php

class NightsWatch implements IFighter {
    private $_fighters = [];

    public function recruit($elem) {
        $this->_fighters[] = $elem;
    }

    public function fight() {
        foreach ($this->_fighters as $fighter)
            if ($fighter instanceof IFighter)
                $fighter->fight();
    }
}