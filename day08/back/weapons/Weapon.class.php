<?php

require_once "utils.php";

abstract class Weapon {

	protected $_initial_load = 0;
	protected $_load = 0;
	protected $_ship;

	public function __construct($initial_load)
	{
		$this->_initial_load = $initial_load;
		$this->_load = $initial_load;
	}

	function reset()
	{
		$this->_load = $this->_initial_load;
	}

	function assignShip($ship)
	{
		$this->_ship = $ship;
	}

	function load()
	{
		$this->_load++;
		return ($this->_ship->consumePP(1));
	}

	public function toArray()
	{
		return [
			'load' => $this->_load,
			'initial_load' => $this->_initial_load,
			'name' => $this->name(),
		];
	}

	public function fire($game)
	{
		$effect_range = $this->effect_zone($game);
		$shooted_ships = [];
		$dice = roll_dice();
		$range = -1;
		if ($dice >= 4)
			$range = 0;
		if ($dice >= 5)
			$range = 1;
		if ($dice == 6)
			$range = 2;
		if ($this->_load > 0)
			$this->_load--;
		if ($range === -1)
			return $dice;
		foreach ($effect_range as $key => $r)

		{
			if ($range < array_search($key, ["short", "mid", "long"]))
				continue;
			foreach ($r as $case)
			{
				foreach ($game->players as $player)
				{
					foreach ($player->ships as $ship)
					{
						if ($ship->inBounds($case) && !in_array($ship, $shooted_ships))
							$shooted_ships[] = $ship;
					}
				}
			}
		}
		foreach ($shooted_ships as $ship) {
			$ship->damage();
			$game->updateShip($ship);
		}
		return $dice;
	}

	abstract function name();
	abstract function short_range();
	abstract function mid_range();
	abstract function long_range();
	abstract function effect_zone($game);
}
