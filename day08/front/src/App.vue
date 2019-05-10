<template>
  <v-app>
    <v-content>
        <div v-if="dice" class="dice-container">
          <dice :value="dice"></dice>
        </div>
        <div class="layout">
          <div class="top-container">
              <toolbar :game="game" :user="notMe"></toolbar>
              <div class="canvas-area" v-if="game">
                <game-map :map="game.map" :players="game.players" :moves="moves" :zones="zones[hoveredWeapon] || [] " @selectShip="onSelect" @selectMove="onMove"/>
              </div>
              <toolbar :game="game" :user="me" @selectShip="onSelect" @upgradeShip="onUpgrade" :locked="!inTurn"></toolbar>
              <v-container v-if="me">
                <v-layout align-center class="ma-2">
                  <v-flex xs10 class="pa-2">
                    <v-stepper non-linear alt-labels>
                      <v-stepper-header>
                        <v-stepper-step :complete="state === 'ACTIVATE'" step="0">Activate</v-stepper-step>
                        <v-divider></v-divider>
                        <v-stepper-step :complete="state === 'ORDER'" step="1">Order</v-stepper-step>
                        <v-divider></v-divider>
                        <v-stepper-step :complete="state === 'MOVE'" step="2">Move</v-stepper-step>
                        <v-divider></v-divider>
                        <v-stepper-step :complete="state === 'SHOOT'" step="3">Shoot</v-stepper-step>
                      </v-stepper-header>
                    </v-stepper>
                  </v-flex>
                  <v-flex xs2>
                    <v-btn v-if="inTurn" @click="onEndPhase">End Phase</v-btn>
                  </v-flex>
                </v-layout>
              </v-container>
            </div>
            <status-bar class="status-bar" :game="game" :state="state" @loadWeapon="({ weapon }) => onUpgrade({ feature: 'weapon', weapon })" @shootWeapon="shootWeapon" @showRange="i => hoveredWeapon = i"></status-bar>
        </div>
    </v-content>
  </v-app>
</template>

<script>

import GameMap from './components/Map.vue'
import Toolbar from './components/Toolbar.vue'
import StatusBar from './components/StatusBar.vue'
import Dice from './components/Dice.vue'

export default {
  name: 'App',
  components: {
    GameMap,
    Toolbar,
    StatusBar,
    Dice
  },
  data () {
    return {
      game: null,
      state: "ACTIVATE",
      moves: [],
      zones: [],
      hoveredWeapon: null,
      inTurn: false,
      dice: null
    }
  },
  computed: {
    me() {
      return this.game && this.game.players.find(({ me }) => me == true);
    },
    notMe() {
      return this.game && this.game.players.find(({ me }) => me == false);
    }
  },
  mounted() {
    this.id = 0;
    const ws = new WebSocket("ws://localhost:9000/");
    ws.addEventListener("message", event => {
      const msg = JSON.parse(event.data);

      switch (msg.action) {
        case "start":
          msg.game.players.forEach(({ ships }) => ships.forEach(ship => ship.selected = false))
          this.game = msg.game;
          break ;
        case "updateShip":
          for(const player of this.game.players) {
            for(const ship of player.ships) {
              if (ship.id === msg.ship.id) {
                for (const [key, value] of Object.entries(msg.ship)) {
                  ship[key] = value;
                }
              }
            }
          }
          break ;
        case "removeShip":
          for (const player of this.game.players) {
            for (const [i, ship] of player.ships.entries()) {
              if (ship.id === msg.ship) {
                player.ships.splice(i, 1);
                if (player.ships.length === 0) {
                  alert(player === this.me ? "You Loose little red shit" : "You won, be praised by the sun");
                  window.location.reload();
                }
              }
            }
          }
          break;
        case "updateMoves":
          this.moves = msg.moves;
          break;
        case "startTurn":
          this.state = "ACTIVATE";
          this.inTurn = true;
          break;
        case "endTurn":
          this.inTurn = false;
          break;
        case "dice":
          this.launchDice(msg.value);
          break;
        case "effectZones":
          this.zones = msg.zones;
          break;
      }
    })

    ws.addEventListener("open", () => {
      ws.send(JSON.stringify({ action: "join", room: 0 }))
    })

    ws.addEventListener("close", () => {
      this.game = null;
      alert("Websocket closed")
    })

    this.ws = ws;
  },
  methods: {
    launchDice(value) {
      this.dice = value;
      if (this.dice_timeout)
        clearTimeout(this.dice_timeout);
      this.dice_timeout = setTimeout(() => this.dice = null, 2000);
    },
    shootWeapon({ weapon }) {
      if (this.state === "SHOOT") {
          this.ws.send(JSON.stringify({
            action: "shoot",
            shoot: "fire",
            weapon
          }))
      }
    },
    onSelect({ ship, player }) {
      if (player.me && this.state === "ACTIVATE" && this.inTurn) {
        player.ships.forEach(ship => ship.selected = false);
        ship.selected = !ship.selected;
      }
    },
    async onUpgrade({ feature, weapon }) {
      const me = this.game.players.find(({ me }) => me)
      if (this.state === "ACTIVATE") {
          this.ws.send(JSON.stringify({
            action: "activate",
            ship: me.ships.findIndex(({ selected }) => selected)
          }))
          this.state = "ORDER"
      }
      if (this.state === "ORDER") {
        this.ws.send(JSON.stringify({
          action: "order",
          order: feature,
          weapon
        }))
        this.ws.send(JSON.stringify({
          action: "refresh"
        }))
      }
    },
    onMove({ n, dir }) {
      if (this.state === "ORDER")
        this.onEndPhase();
      if (this.state === "MOVE") {
        this.ws.send(JSON.stringify({
          action: "move",
          move: "forward",
          n: n + 1,
          dir
        }))
        this.ws.send(JSON.stringify({
          action: "refresh"
        }))
      }
    },
    onEndPhase() {
      if (this.state === "ORDER") {
        this.ws.send(JSON.stringify({
          action: "order",
          order: "end"
        }))
        this.state = "MOVE";
      }
      else if (this.state === "MOVE") {
        this.ws.send(JSON.stringify({
          action: "move",
          move: "end"
        }))
        this.moves = [];
        this.state = "SHOOT";
      }
      else if (this.state === "SHOOT") {
        this.ws.send(JSON.stringify({
          action: "shoot",
          shoot: "end"
        }))
        this.hoveredWeapon = null;
        this.zones = [];
      }
    }
  }
}
</script>
<style>
.top-container {
  display: flex;
  flex-direction: column;
  min-width: 75vw;
}

.status-bar {
  min-width: 300px;
  width: 300px;
  display: block;
  border-left: 2px solid #000;
}

.canvas-area {
  background: url(https://i.redd.it/rqs2x9e6tfc21.png) no-repeat center;
  background-size: cover;
  
}

.layout {
  display: flex;
  flex-direction: row;
}

.bottom {
  min-height: 20%;
}

.dice-container {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
}

</style>
