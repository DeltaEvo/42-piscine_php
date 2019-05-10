<?php

require_once "Weapon.class.php";
require_once "ships/Ship.class.php";

class SideLazer extends Weapon {

	public function __construct()
	{
		parent::__construct(0);
	}

	function name()
	{
		return "Side Lazers";
	}

	function short_range()
	{
		return [1, 10];
	}

	function mid_range()
	{
		return [11, 20];
	}

	function long_range()
	{
		return [21, 30];
	}

	function effect_zone($game)
	{
		$arr = [];
		foreach (range(0, 3) as $rot)
		{
			if ($rot % 2 === $this->_ship->rotation % 2)
				continue;
			$dir = [
				'x' => $rot == Ship::WEST ? 1 : ($rot == Ship::EAST ? -1 : 0),
				'y' => $rot == Ship::SOUTH ? -1 : ($rot == Ship::NORTH ? 1 : 0)
			];
			$start = floor($this->_ship->size[0] / 2) + 1;
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
				foreach(range(0, $x) as $y) {
					if (!$stopRight) {
						$n = ['x' => $this->_ship->pos['x'] + $dir['x'] * $x + $dir['y'] * $y, 'y' => $this->_ship->pos['y'] + $dir['y'] * $x + $dir['x'] * $y];
						if (@$game->map[$n['y']][$n['x']] == 1)
						{
							$stopRight = TRUE;
							break;
						}
						$arr[$range][] = $n;
					}
					if ($y && !$stopLeft)
					{
						$n = ['x' => $this->_ship->pos['x'] + $dir['x'] * $x + -$dir['y'] * $y, 'y' => $this->_ship->pos['y'] + $dir['y'] * $x + -$dir['x'] * $y];
						if (@$game->map[$n['y']][$n['x']] == 1)
						{
							$stopLeft = TRUE;
							break;
						}
						$arr[$range][] = $n;
					}
				}
			}
			
		}
		return $arr;
	}
}
