<template>
	<v-container fluid v-if="user" class="container-toolbar">
		<v-layout class="overflow pa-2" :style="{ 'opacity': locked ? 0.5 : 1 }">
			<template v-for="(ship, i) in user.ships">
				<v-flex xs12 md4 class="pa-1" :key="i">
					<div class="ship-card elevation-2"
						:class="{ 'elevation-12': ship.selected }"
						@click="onClickShip(ship)"
					>
						<v-layout row column align-center>
							<div class="ship-title">{{ ship.name }}</div>
							<v-badge left :class="{ 'hidden': !ship.selected }">
								<template v-slot:badge>
									<span>{{ ship.pp }}</span>
								</template>
								<v-icon large v-if="user.me" @click="upgradeShip(ship)">control_point</v-icon>
							</v-badge>
							<img class="img" :src="shipImg(ship)" data-rotate="90"/>
							<div class="infos">
								Health ( {{ ship.health }} / {{ ship.initial_health }} )
								<v-progress-linear
									color="error"
									height="5"
									:value="(ship.health / ship.initial_health) * 100"
								></v-progress-linear>
								Shield ( {{ ship.shield }} / {{ ship.initial_shield + ship.initial_pp }} )
								<v-progress-linear
									color="info"
									height="5"
									:value="(ship.shield / (ship.initial_shield + ship.initial_pp)) * 100"
								></v-progress-linear>
								Speed ( {{ ship.speed }} / {{ ship.initial_pp * 6 + ship.initial_speed }} )
								<v-progress-linear
									color="success"
									height="5"
									:value="(ship.speed / (ship.initial_pp * 6 + ship.initial_speed)) * 100"
								></v-progress-linear>
							</div>

						</v-layout>
					</div>
				</v-flex>
			</template>
		</v-layout>
		<v-dialog v-model="dialog.value" max-width="500" v-if="dialog.data && dialog.data.pp">
			<v-card>
				<v-card-title class="headline">
					Upgrade {{ dialog.data.name }}
					<v-spacer></v-spacer>
					<v-badge left >
						<template v-slot:badge>
							<span>{{ dialog.data.pp }}</span>
						</template>
					</v-badge>
				</v-card-title>

				<v-card-text>
					<v-list two-line>
						<v-list-tile>
							<v-list-tile-content>
								<v-list-tile-title>
									Shields ( {{ dialog.data.shield }} / {{ dialog.data.initial_shield + dialog.data.initial_pp }} )
								</v-list-tile-title>
								<v-list-tile-sub-title>
									<v-progress-linear
										color="info"
										height="20"
										:value="(dialog.data.shield / (dialog.data.initial_shield + dialog.data.initial_pp)) * 100"
									></v-progress-linear>
								</v-list-tile-sub-title>
							
							</v-list-tile-content>
							<v-list-tile-action>
								<v-btn icon ripple>
									<v-icon color="grey lighten-1" @click="upgradeFeature(dialog.data, 'shield')">add</v-icon>
								</v-btn>
							</v-list-tile-action>
						</v-list-tile>
						<v-list-tile>
							<v-list-tile-content>
								<v-list-tile-title>
									Speed ( {{ dialog.data.speed }} / {{ dialog.data.initial_speed + dialog.data.initial_pp }} )
								</v-list-tile-title>
								<v-list-tile-sub-title>
									<v-progress-linear
										color="success"
										height="20"
										:value="(dialog.data.speed / (dialog.data.initial_pp + dialog.data.initial_speed)) * 100"
									></v-progress-linear>
								</v-list-tile-sub-title>
							
							</v-list-tile-content>
							<v-list-tile-action>
								<v-btn icon ripple>
									<v-icon color="grey lighten-1" @click="upgradeFeature(dialog.data, 'speed')">add</v-icon>
								</v-btn>
							</v-list-tile-action>
						</v-list-tile>
					</v-list>
				</v-card-text>

				<v-card-actions>
				<v-spacer></v-spacer>
				<v-btn
					color="green darken-1"
					flat="flat"
					@click="dialog.value = false"
				>
					Finish
				</v-btn>
				</v-card-actions>
			</v-card>
		</v-dialog>
		<v-icon class="overlay" v-if="locked">lock</v-icon>
	</v-container>
</template>

<script>
export default {
	name: 'toolbar',
	props: ['game', 'user', 'locked'],
	data() {
		return {
			dialog: {
				value: false,
				data: []
			}
		}
	},
	computed: {
		me() {
			return this.game && this.game.players.find(({ me }) => me == true);
		},
	},
	methods:  {
		shipImg(ship) {
			const i = this.game.players.findIndex(p => p == this.user);
			return `/ships/${ship.sprite.replace("{{COLOR}}", i == 1 ? "Bleu" : "Rouge")}`;
		},
		onClickShip(ship){
			this.$emit("selectShip", {
				ship,
				player: this.me
			})
		},
		upgradeShip(ship) {
			this.dialog.value = true;
			this.dialog.data = ship;
		},
		upgradeFeature(ship, feature) {
			this.$emit('upgradeShip', {
				ship, feature
			})
		}
	}
}
</script>

<style>


.container {
	padding: 0;
}

.ship-card {
	margin: auto;
	display: flex;
	flex-direction: column;
	padding: 5px;
	align-items: center;
	align-content: center;
	cursor: pointer;
	height: 100%;
}

.hidden {
	visibility: hidden;
}

.overflow {
	overflow-x: auto;
}

.ship-card .selected {
	border: 2px solid blue;
}

.ship-card .infos {
	margin-top: auto;
	width: 100%;
}
.ship-card .icon {
	text-align: center;
}

.ship-card .img {
	height: 120px;
	width: 120px;
	transform: rotate(-90deg);
	transform-origin: center center;
	object-fit: contain;
}


.ship-card .ship-title {
	width: 100%;
	font-size: 15pt;
	text-align: center;
	padding-bottom: 10px;
}

.container-toolbar {
	position: relative;
}

.container-toolbar .overlay {
	position: absolute;

	width: 108px;
	z-index: 9001;
	height: 108px;
	font-size: 108px;
	top: 50%;
	left: 50%;
	transform: translate(-50%, -50%);
}

</style>
