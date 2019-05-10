const tools = document.querySelectorAll(".toolbox *")
const scene = document.getElementById("scene")
const text = document.getElementById("text")
const wall = document.getElementById("wall")
const outside = document.getElementById("outside")
const input = document.getElementById("input")
const reload = document.getElementById("reload")
const close = document.getElementById("close")
const answer = document.getElementById("answer")
let selected;

for (const tool of tools) {
    tool.addEventListener("click", () => {
        if (selected === tool) {
            selected.classList.remove("selected")
            document.body.style.cursor = ''
            selected = null
        } else {
            if (selected)
                selected.classList.remove("selected")
            selected = tool;
            selected.classList.add("selected")
            const url = selected.tagName === "IMG" ? selected.src : "resources/brick"
            document.body.style.cursor = `url("${url}.small.png"), auto`
        }
    })
}


scene.addEventListener("click", e => {
    if (selected && selected.title === "Le Mur Portugais") {
        wall.style.display = "block"
    }
    if (selected && selected.title === "Livre") {
        wall.style.display = "none"
    }
    if (selected && selected.title === "Avancer" && e.target.classList.contains("door")) {
        outside.style.display = "block"
    }
    if (e.target.getAttribute("data-speek") && selected && selected.title === "Parler") {
        alert(e.target.getAttribute("data-speek"))
    }
})

scene.addEventListener("mousemove", e => {
    if (selected && selected.title === "Regarder" && e.target.getAttribute("data-desc")) {
        text.textContent = e.target.getAttribute("data-desc")
    } else
        text.textContent = ""
})

answer.addEventListener("click", () => {
    const text = input.value;

    if (text === "Oui")
        alert("Bienvenue");
    else if (text === "Non")
        alert("Ah ... amusez vous bien alors")
    else
        alert("I don't speek french")
})

reload.addEventListener("click", () => document.location.reload())