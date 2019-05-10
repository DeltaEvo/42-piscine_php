<template>
	<div>
		<div class="status-container" v-if="selectedShip">
			<div  class="weapon elevation-3" v-for="(weapon, i) in selectedShip.weapons" :key="i" @mouseenter="showRange(i)" @mouseleave="showRange(null)">
				<div class="title">{{ weapon.name }}</div>
				Load ( {{ weapon.load }} / {{ selectedShip.initial_pp + weapon.initial_load }} )
				<v-progress-linear
					color="success"
					height="5"
					:value="(weapon.load / (selectedShip.initial_pp + weapon.initial_load)) * 100"
				></v-progress-linear>
				<v-layout>
					<v-btn v-if="(state == 'ORDER' || state == 'ACTIVATE') && selectedShip.initial_pp > 0" block color="info" @click="loadWeapon(i)">Load</v-btn>
					<v-btn v-if="state == 'SHOOT' && weapon.load > 0" block color="error" @click="shootWeapon(i)">SHOOT !</v-btn>
				</v-layout>
			</div>
		</div>
	</div>
</template>

<script>
export default {
	title: 'status-bar',
	props: ['game', 'state'],
	computed: {
		me() {
			return this.game && this.game.players.find(({me}) => me == true);
		},
		selectedShip() {
			return this.me && this.me.ships.find(({ selected }) => selected == true);
		}
	},
	methods: {
		loadWeapon(i) {
			this.$emit('loadWeapon', {
				weapon: i
			});
		},
		shootWeapon(i) {
			this.$emit('shootWeapon', {
				weapon: i
			});
		},
		showRange(i) {
			this.$emit('showRange', i);
		}
	}
}
</script>

<style>

.weapon {
	margin: 10px;
	padding: 10px;
}

.weapon .title {
	padding: 5px;
	padding-bottom: 10px;
	font-size: 18pt;
	text-align: center;
}

</style>

