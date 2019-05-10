const list = $("#ft_list")
const btn = $("#btn")

function addTodo(text, id) {
    const div = $(document.createElement("div"))
    div.append(text)
    list.prepend(div)
    function handleClick(id) {
        div.click(() => {
            if (confirm("Remove todo")) {
                div.remove()
                $.get(`delete.php?id=${id}`, () => {})
            }
        })
    }
    if (!id) {
        $.get(`insert.php?text=${encodeURIComponent(text)}`, (id) =>
            id ? handleClick(id) : div.remove()
        )
    } else
        handleClick(id)
}

btn.click(() => {
    const text = prompt("New note").trim()
    if (text.length)
        addTodo(text)
})

$.getJSON("select.php", data => {
    data.forEach(({ id, value }) => {
        addTodo(value, id);
    })
})
