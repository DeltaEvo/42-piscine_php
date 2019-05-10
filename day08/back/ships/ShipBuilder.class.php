<?php 

require_once "Ship.class.php";
require_once "weapons/SideLazer.class.php";
require_once "weapons/LanceNavale.class.php";
require_once "weapons/LanceNavaleLourde.class.php";

class ShipBuilder {

	protected $name;
	protected $size;
	protected $shield;
	protected $health;
	protected $sprite;
	protected $pp;
	protected $speed;
	protected $weapons;

	public function setName($name)
	{
		$this->name = $name;
		return ($this);
	}

	public function setSize($size)
	{
		$this->size = $size;
		return ($this);
	}

	public function setHealth($initial_health)
	{
		$this->health = $initial_health;
		return ($this);
	}

	public function setShield($initial_shield)
	{
		$this->shield = $initial_shield;
		return ($this);
	}

	public function setSprite($sprite)
	{
		$this->sprite = $sprite;
		return ($this);
	}

	public function setPP($pp)
	{
		$this->pp = $pp;
		return ($this);
	}

	public function setSpeed($speed)
	{
		$this->speed = $speed;
		return ($this);
	}

	public function setWeapons($weapons)
	{
		$this->weapons = $weapons;
		return ($this);
	}

	public function build()
	{
		$ship = new Ship(
			$this->name,
			$this->size,
			$this->shield,
			$this->health,
			$this->pp,
			$this->speed,
			$this->sprite,
			$this->weapons
		);
		foreach ($ship->weapons as $w)
			$w->assignShip($ship);
		return $ship;
	}

	public static function shipList()
	{
		$ships = [];
		$ships[] = (new ShipBuilder())
			->setName("Honorable Duty")
			->setSize([1, 3])
			->setHealth(5)
			->setSprite("Fregate{{COLOR}}.png")
			->setPP(10)
			->setSpeed(15)
			->setShield(0)
			->setWeapons([new SideLazer()])
			->build();
		
		$ships[] = (new ShipBuilder())
			->setName("Sword Of Absolution")
			->setSize([1, 5])
			->setHealth(4)
			->setSprite("Destroyer{{COLOR}}.png")
			->setPP(10)
			->setSpeed(18)
			//->setSpeed(1800)
			->setShield(0)
			->setWeapons([new SideLazer()])
			->build();
		
		$ships[] = (new ShipBuilder())
			->setName("Hand Of The Emperor")
			->setSize([1, 3])
			->setHealth(5)
			->setSprite("Destroyer{{COLOR}}.png")
			->setPP(10)
			->setSpeed(15)
			->setShield(0)
			->setWeapons([new LanceNavale()])
			->build();

		$ships[] = (new ShipBuilder())
			->setName("Imperator Deus")
			->setSize([3, 7])
			->setHealth(8)
			->setSprite("VessoMaman{{COLOR}}.png")
			->setPP(12)
			->setSpeed(10)
			->setShield(2)
			->setWeapons([new LanceNavale(), new LanceNavaleLourde()])
			->build();
		
		$ships[] = (new ShipBuilder())
			->setName("Orktobre Roug")
			->setSize([3, 3])
			->setHealth(4)
			->setSprite("Attack{{COLOR}}.png")
			->setPP(10)
			->setSpeed(19)
			->setShield(0)
			->setWeapons([new SideLazer(), new LanceNavale()])
			->build();
		
		$ships[] = (new ShipBuilder())
			->setName("Ork’N’Roll")
			->setSize([5, 1])
			->setHealth(6)
			->setSprite("AttackBomber{{COLOR}}.png")
			->setPP(10)
			->setSpeed(12)
			->setShield(0)
			->setWeapons([new SideLazer(), new LanceNavaleLourde()])
			->build();
		return $ships;
	}
}
