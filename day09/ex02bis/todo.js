const list = $("#ft_list")
const btn = $("#btn")

const todos = []

function save() {
    document.cookie = `todos=${JSON.stringify(todos)}`
}

function addTodo(text, saveTodo) {
    const div = $(document.createElement("div"))
    div.append(text)
    list.prepend(div)
    todos.push(text)
    if (saveTodo) save()
    div.click(() => {
        if (confirm("Remove todo")) {
            todos.splice(todos.indexOf(text), 1)
            div.remove()
            save()
        }
    })
}

btn.click(() => {
    const text = prompt("New note").trim()
    if (text.length)
        addTodo(text, true)
})

document.cookie.split(";")
    .map(e => e.trim().split("=", 2))
    .forEach(([name, value]) => {
        if (name === "todos") {
            const todos = JSON.parse(value);
            if (Array.isArray(todos))
                todos.forEach(todo => addTodo(todo, false))
        }
    })