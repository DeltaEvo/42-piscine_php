<?php

require_once "Weapon.class.php";
require_once "ships/Ship.class.php";

class LanceNavale extends Weapon {

	public function __construct($load = 0)
	{
		parent::__construct($load);
	}

	function name()
	{
		return "Lance Navale";
	}

	function short_range()
	{
		return [1, 30];
	}

	function mid_range()
	{
		return [31, 60];
	}

	function long_range()
	{
		return [61, 90];
	}

	function effect_zone($game)
	{
		$arr = [];
		foreach (range(0, 3) as $rot)
		{
			if ($rot !== $this->_ship->rotation)
				continue;
			$dir = [
				'x' => $rot == Ship::WEST ? 1 : ($rot == Ship::EAST ? -1 : 0),
				'y' => $rot == Ship::SOUTH ? -1 : ($rot == Ship::NORTH ? 1 : 0)
			];
			$start = floor($this->_ship->size[1] / 2) + 1;
			$offset = 0;
			$stopRight = FALSE;
			$stopLeft = FALSE;
			foreach (range($start + $this->short_range()[0], $start + $this->long_range()[1]) as $x)
			{
				$range = 'short';
				if ($x >= $this->mid_range()[0])
					$range = 'mid';
				if ($x >= $this->long_range()[0])
					$range = 'long';
				$n = [ 'x' => $this->_ship->pos['x'] + $dir['x'] * $x, 'y' => $this->_ship->pos['y'] + $dir['y'] * $x ];
				if (@$game->map[$newpos['y']][$newpos['x']] == 1)
					break;
				$arr[$range][] = $n;
			}
			
		}
		return $arr;
	}

}
