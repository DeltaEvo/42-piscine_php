<template>
    <canvas @click="onClick"></canvas>
</template>

<script>

const asteroids = new Image();
asteroids.src = "/asteroid.png"

const cache = new Map();

export default {
	props: ['map', 'players', 'moves', 'zones'],
	data() {
		return {};
	},
	mounted() {
		const canvas = this.$el;
		this.width = this.map[0].length;
		this.height = this.map.length;
		this.cell_width = 16;
		this.context = canvas.getContext("2d");
		canvas.width = this.width * this.cell_width;
		canvas.height = this.height * this.cell_width;
		this.render();
	},
	watch: {
		map: {
			handler() {
				this.render()
			},
			deep: true
		},
		players: {
			handler() {
				this.render()
			},
			deep: true
		},
		moves: {
			handler() {
				this.render()
			},
			deep: true
		},
		zones: {
			handler() {
				this.render()
			}
		}
	},
	methods: {
		onClick(e) {
			const x = Math.floor((e.offsetX / this.$el.clientWidth) * this.width);
			const y = Math.floor((e.offsetY / this.$el.clientHeight) * this.height);

			for (const player of this.players) {
				for (const ship of player.ships) {
					const sWidth = ship.rotation % 2 ? ship.size[1] : ship.size[0];
					const sHeight = ship.rotation % 2 ? ship.size[0] : ship.size[1];
					if (x >= ship.position.x - sWidth / 2
						&& y >= ship.position.y - sHeight / 2
						&& x < ship.position.x + sWidth / 2
						&& y < ship.position.y + sHeight / 2
					) {
						this.$emit("selectShip", {
							player,
							ship,
							x: e.offsetX,
							y: e.offsetY
						})
					}
				}
			}

			for (const [dir, moves] of this.moves.entries()) {
				for (const [n, { x: xM, y: yM }] of moves.entries()) {
					if (xM === x && yM === y) {
						this.$emit("selectMove", {
							dir,
							n
						})
					}
				}
			}
		},
		render() {
			const cell_width = this.cell_width;
			this.context.clearRect(0, 0, this.$el.width, this.$el.height);

			for (const [y, line] of this.map.entries()) {
				for (const [x, el] of line.entries()) {
					if (el) {
						this.context.drawImage(
							asteroids,
							0,
							0,
							128,
							128,
							x * cell_width,
							y * cell_width,
							cell_width,
							cell_width
						);
					}
				}
			}
			for (const [i, player] of this.players.entries()) {
				for (const ship of player.ships) {
					const renderShip = (image) => {
						const width = cell_width * ship.size[0];
						const height = cell_width * ship.size[1];
						if (ship.selected) {
							this.context.fillStyle = "rgba(255, 255, 255, 0.5)";
							const sWidth = ship.rotation % 2 ? ship.size[1] : ship.size[0];
							const sHeight = ship.rotation % 2 ? ship.size[0] : ship.size[1];
							this.context.fillRect(
								(ship.position.x - (sWidth - 1) / 2) * cell_width,
								(ship.position.y - (sHeight - 1) / 2) * cell_width,
								sWidth * cell_width,
								sHeight * cell_width
							);
						}
						this.context.save();
						this.context.translate(
							(ship.position.x + 0.5) * cell_width,
							(ship.position.y + 0.5) * cell_width,
						);
						this.context.rotate(Math.PI + ship.rotation * Math.PI / 2);
						this.context.drawImage(
							image,
							-width/2,
							-height/2,
							width,
							height
						);
						this.context.restore();
					}
					const src = `/ships/${ship.sprite.replace("{{COLOR}}", i ? "Bleu" : "Rouge")}`;
					if (cache.has(src)) {
						renderShip(cache.get(src));
					} else {
						const image = new Image();
						image.src = src;
						image.onload = () => {
							renderShip(image);
							cache.set(src, image);
						}
					}
				}
			}
			for (const moves of this.moves) {
				for (const { x, y } of moves) {
					this.context.fillStyle = "rgba(150, 100, 255, 0.2)";
					this.context.fillRect(
						x * cell_width,
						y * cell_width,
						cell_width,
						cell_width
					);
				}
			}
			for (const [name, zone] of Object.entries(this.zones)) {
				if (name === "short")
					this.context.fillStyle = "rgba(0, 255, 0, 0.3)";
				if (name === "mid")
					this.context.fillStyle = "rgba(0, 0, 255, 0.3)";
				if (name === "long")
					this.context.fillStyle = "rgba(255, 0, 0, 0.3)";
				for (const { x, y } of zone) {
					this.context.fillRect(
						x * cell_width,
						y * cell_width,
						cell_width,
						cell_width
					);
				}
			}
		}
	}
}
</script>
<style scoped>
canvas {
	max-width: 100%;
	max-height: 100%;
    border: 2px solid rebeccapurple;
}
</style>
