<?php

require_once "ships/ShipBuilder.class.php";
require_once "ships/Ship.class.php";

class Player {
	const STATE_ACTIVATE = 0;
	const STATE_ORDER = 1;
	const STATE_MOVE = 2;
	const STATE_SHOOT = 3;

	public $state;
	public $currentShip;
	public $game;
	private $_conn;
	private $_ships;
	private $_inTurn;

	function __construct($conn, $num) {
		$this->conn = $conn;	
		$this->state = self::STATE_ACTIVATE;
		$this->_ships = ShipBuilder::shipList();
		$offset = 2;
		foreach ($this->_ships as $ship) {
			$ship->assign_player($this);
			$ship->pos = array(
				"y" => ($num ? 98 - $ship->size[1] : 2) + ($ship->size[1] - 1)/2,
				"x" => ($num ? 150 - $ship->size[0] - $offset : $offset) + ($ship->size[0] - 1)/2
			);
			$ship->rotation = $num == 0 ? Ship::NORTH : Ship::SOUTH;
			$offset += $ship->size[0] + 5;
		}
		$this->currentShip = null;
		$this->_inTurn = False;
	}

	public function __get($name) {
		$var_name = "_$name";
		if (property_exists($this, $var_name))
			return $this->$var_name;
	}

	public function startTurn() {
		$this->conn->send(json_encode(array(
			"action" => "startTurn" 
		)));
		$this->state = self::STATE_ACTIVATE;
		$this->_inTurn = True;
		foreach ($this->_ships as $s) {
			$s->reset();
			if ($this->game)
				$this->game->updateShip($s);
		}
	}

	public function endTurn() {
		$this->conn->send(json_encode(array(
			"action" => "endTurn" 
		)));
		$this->_inTurn = False;
	}

	public function removeShip($ship) {
		$i = array_search($ship, $this->_ships);
		array_splice($this->_ships, $i, 1);
		foreach ($this->game->players as $player) {
			$player->conn->send(json_encode(array(
				"action" => "removeShip",
				"ship" => $ship->id
			)));
		}
	}
}
