<?php

require_once "Map.class.php";

class Game {
	private $_ships;
	private $_players;
	private $_map;
	private $_turns;

	function __construct($players) {
		$this->_players = $players;
		$this->_players[0]->startTurn();
		$this->_map = Map::default_map();
		$this->_turns = 0;
	}

	public function nextPlayer() {
		foreach ($this->_players as $i => $player) {
			if ($player->inTurn) {
				$player->endTurn();
				if ($i === (count($this->_players) - 1)) {
					$this->_players[0]->startTurn();
					$this->_turns++;
				} else {
					$this->_players[$i + 1]->startTurn();
				}
				return ;
			}
		}
		throw new Exception("Unreachable");
	}

	public function updateShip($ship) {
		foreach ($this->_players as $player) {
			$player->conn->send(json_encode(array(
				"action" => "updateShip",
				"ship" => $ship->toArray()
			)));
		}
	}

	public function __get($name) {
		$var_name = "_$name";
		if (property_exists($this, $var_name))
			return $this->$var_name;
	}

}
