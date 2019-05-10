<?php
use Ratchet\Server\IoServer;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

require __DIR__ . '/vendor/autoload.php';

require_once 'Player.class.php';
require_once 'Game.class.php';

class Chat implements MessageComponentInterface {
    protected $players;
	protected $rooms;

    public function __construct() {
        $this->players = new \SplObjectStorage;
		$this->rooms = [];
    }

    public function onOpen(ConnectionInterface $conn) {
        echo "New connection! ({$conn->resourceId})\n";
    }

	private function removeFromRooms($conn) {
		foreach ($this->rooms as $room) {
			if (($i = array_search($conn, $room)) !== FALSE) {
				array_splice($room, $i, 1);
			}
		}
	}

	public function join($from, $message) {
		$room = $this->rooms[$message["room"]];
		$room[] = $from;
		$this->rooms[$message["room"]] = $room;

		if (count($room) == 2) {
			$players = array_map(function ($conn, $i) {
				return new Player($conn, $i);
			}, $room, array_keys($room));
			$game = new Game($players);
			foreach ($players as $player) {
				$client = $player->conn;
				$player->game = $game;
				$this->players[$client] = $player;
				$client->send(json_encode(array(
					"action" => "start", 
					"game" => array(
						"players" => array_map(function ($p) use ($player) {
							return array(
								"me" => $p === $player,
								"ships" => array_map(function ($ship) {
									return $ship->toArray();
								}, $p->ships)
							);
						}, $players),
						"map" => $game->map
					)
				)));
			}
			unset($this->rooms[$message["room"]]);
		}
	}

	public function activate($from, $message) {
		$player = $this->players[$from];
		$ship = intval($message["ship"]);
		if ($player && $player->inTurn && $player->state === Player::STATE_ACTIVATE && $ship >= 0 && $ship < count($player->ships)) {
			$player->currentShip = $player->ships[$ship];
			$player->state = Player::STATE_ORDER;
		} else
			$from->close();
	}

	private function launchDice($value, $to) {
		$to->send(json_encode(array(
			"action" => "dice",
			"value" => $value
		)));
	}

	public function order($from, $message) {
		$player = $this->players[$from];
		if ($player && $player->inTurn && $player->state === Player::STATE_ORDER) {
			if ($message["order"] === "speed" && $player->currentShip->pp != 0) {
				$this->launchDice($player->currentShip->upgradeSpeed(), $from);
			} else if ($message["order"] === "shield" && $player->currentShip->pp != 0) {
				$player->currentShip->upgradeShield();
			}
			else if ($message["order"] === "repair" && $player->currentShip->pp != 0) {
				$player->currentShip->tryRepair();
			}
			else if ($message["order"] === "weapon" && $player->currentShip->pp != 0) {
				$player->currentShip->weapons[$message["weapon"]]->load();
			}
			else if ($message["order"] === "end") {
				$player->state = Player::STATE_MOVE;
				return ;
			} else {
				return ;
			}
			$player->game->updateShip($player->currentShip);
		} else
			$from->close();
	}

	public function move($from, $message) {
		$player = $this->players[$from];
		if ($player && $player->inTurn && $player->state === Player::STATE_MOVE) {
			if ($message["move"] === "forward") {
				$player->currentShip->move($message["n"], $message["dir"]);
				$player->game->updateShip($player->currentShip);
			}
			else if ($message["move"] === "end") {
				$player->state = Player::STATE_SHOOT;
			}
		}
	}

	public function shoot($from, $message) {
		$player = $this->players[$from];
		if ($player && $player->inTurn && $player->state === Player::STATE_SHOOT) {
			if ($message["shoot"] === "fire") {
				$this->launchDice($player->currentShip->weapons[$message["weapon"]]->fire($player->game), $from);
				$player->game->updateShip($player->currentShip);
			}
			if ($message["shoot"] === "end") {
				$player->game->nextPlayer();
			}
		}
	}

	public function onMessage(ConnectionInterface $from, $msg) {
		error_reporting(E_ALL);
		$message = json_decode($msg, TRUE);
		switch ($message["action"]) {
			case "join":
				$this->join($from, $message);
				break;
			case "activate":
				$this->activate($from, $message);
				break;
			case "order":
				$this->order($from, $message);
				break;
			case "move":
				$this->move($from, $message);
				break;
			case "shoot":
				$this->shoot($from, $message);
				break;
			case "refresh":
				$player = $this->players[$from];
				if ($player && $player->currentShip) {
					$from->send(json_encode(array(
						"action" => "updateMoves",
						"moves" => $player->currentShip->predictMove($player->game)
					)));
					$from->send(json_encode(array(
						"action" => "effectZones",
						"zones" => array_map(function ($weapon) use ($player) {
							return $weapon->effect_zone($player->game);
						}, $player->currentShip->weapons)
					)));
				} else
					$from->close();
				break;
		};
    }

    public function onClose(ConnectionInterface $conn) {
        // The connection is closed, remove it, as we can no longer send it messages
		$this->players->detach($conn);
		$this->removeFromRooms($conn);

        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "An error has occurred: {$e->getMessage()}\n";

        $conn->close();
    }
}

$server = IoServer::factory(
	new HttpServer(
		new WsServer(
			new Chat()
		)
	),
	9000
);

$server->run();
