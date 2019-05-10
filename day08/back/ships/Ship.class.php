<?php

require_once "utils.php";

class Ship {

	const NORTH = 0;
	const EAST = 1;
	const SOUTH = 2;
	const WEST = 3;

	private $_id;
	private $_name;
	private $_size;
	private $_initial_health;
	private $_shield;
	private $_initial_shield;
	private $_health;
	private $_initial_pp;
	private $_pp;
	private $_initial_speed;
	private $_speed;
	private $_weapons;
	private $_sprite;
	private $_player;
	
	public $pos;
	public $rotation;
	
	public function __construct($name, $size, $shield, $health, $pp, $speed, $sprite, $weapons)
	{
		$this->_id = uniqid("ship");
		$this->_name = $name;
		$this->_size = $size;
		$this->_initial_health = $health;
		$this->_health = $health;
		$this->_initial_shield = $shield;
		$this->_shield = $shield;
		$this->_initial_pp = $pp;
		$this->_pp = $pp;
		$this->_initial_speed = $speed;
		$this->_speed = $speed;
		$this->_weapons = $weapons;
		$this->_sprite = $sprite;
	}

	public function __get($name)
	{
		$var_name = "_$name";
		if (property_exists($this, $var_name))
			return $this->$var_name;
	}

	public function toArray() {
		return array(
			"id" => $this->_id,
			"name" => $this->_name,
			"size" => $this->_size,
			"shield" => $this->_shield,
			"initial_shield" => $this->_initial_shield,
			"health" => $this->_health,
			"initial_health" => $this->_initial_health,
			"pp" => $this->_pp,
			"initial_pp" => $this->_initial_pp,
			"speed" => $this->_speed,
			"initial_speed" => $this->_initial_speed,
			"weapons" => array_map(function ($w) {
				return $w->toArray();
			}, $this->_weapons),
			"position" => $this->pos,
			"rotation" => $this->rotation,
			"sprite" => $this->_sprite
		);
	}

	public function assign_player($player)
	{
		$this->_player = $player;
	}

	public function setPosition(array $pos)
	{
		$this->pos = $pos;
	}

	public function reset()
	{
		$this->_pp = $this->_initial_pp;
		$this->_speed = $this->_initial_speed;
		$this->_shield = $this->_initial_shield;
		foreach ($this->_weapons as $w)
			$w->reset();
	}

	public function move($n, $rotation)
	{
		if ($rotation != $this->rotation)
			if (!$this->consumeSpeed(1))
				return FALSE;
		$this->rotation = $rotation;
		$dir = [
			'x' => $rotation == self::WEST ? 1 : ($rotation == self::EAST ? -1 : 0),
			'y' => $rotation == self::SOUTH ? -1 : ($rotation == self::NORTH ? 1 : 0)
		];
		if (!$this->consumeSpeed($n))
			return (FALSE);
		$this->pos['x'] += $dir['x'] * $n;
		$this->pos['y'] += $dir['y'] * $n;
		return (TRUE);
	}

	public function consumePP($n)
	{
		if ($this->_pp - $n < 0)
			return FALSE;
		$this->_pp -= $n;
		return TRUE;
	}

	public function consumeSpeed($n)
	{
		if ($this->_speed - $n < 0)
			return FALSE;
		$this->_speed -= $n;
		return TRUE;
	}

	public function inBounds($pos)
	{
		$sWidth = $this->rotation % 2 ? $this->size[1] : $this->size[0];
		$sHeight = $this->rotation % 2 ? $this->size[0] : $this->size[1];
		return ($pos['x'] >= $this->pos['x'] - $sWidth / 2
			&& $pos['y'] >= $this->pos['y'] - $sHeight / 2
			&& $pos['x'] < $this->pos['x'] + $sWidth / 2
			&& $pos['y'] < $this->pos['y'] + $sHeight / 2
		);
	}

	public function predictMove($game)
	{
		if ($this->_speed == 0)
			return [];
		$moves = [];
		foreach (range(0, 3) as $rot)
		{
			$dir = [
				'x' => $rot == self::WEST ? 1 : ($rot == self::EAST ? -1 : 0),
				'y' => $rot == self::SOUTH ? -1 : ($rot == self::NORTH ? 1 : 0)
			];
			$arr = [];
			$start = floor($this->_size[($this->rotation + $rot) % 2 ? 0 : 1] / 2) + 1;
			$sub = abs($this->rotation - $rot) % 2;
			foreach (range($start, $start + $this->speed - 1 - $sub) as $v)
			{
				$newpos = [ 'x' => $this->pos['x'] + $dir['x'] * $v, 'y' => $this->pos['y'] + $dir['y'] * $v ];
				if (@$game->map[$newpos['y']][$newpos['x']] == 1)
					break;
				$inside = FALSE;
				foreach ($game->players as $player)
				{
					if ($inside)
						break;
					foreach ($player->ships as $ship)
					{
						if ($ship->inBounds($newpos))
						{
							$inside = TRUE;
							break;
						}
					}
				}
				if ($inside)
					break;
				$arr[] = $newpos;
			}
			$moves[] = $arr;
		}
		return $moves;
	}

	public function tryRepair()
	{
		if ($this->pp <= 0)
			throw new Exception('Null pp');
		$this->_pp--;
		$value = roll_dice();
		if ($this->_health <= $this->_initial_health)
			throw new Exception('Already at max health');
		if ($value == 6)
			$this->_health++;
		return $value;
	}

	public function upgradeSpeed()
	{
		if ($this->pp <= 0)
			throw new Exception('Null pp');
		$this->_pp--;
		$value = roll_dice();
		$this->_speed += $value;
		return $value;
	}

	public function upgradeShield()
	{
		if ($this->pp <= 0)
			throw new Exception('Null pp');
		$this->_pp--;
		$this->_shield += 1;
	}

	public function damage()
	{
		if ($this->_shield > 0)
		{
			$this->_shield--;
			return ;
		}
		$this->_health--;
		if ($this->_health > 0)
			return ;
		$this->_player->removeShip($this);
	}
}
