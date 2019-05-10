(function (){
class ShopCategories extends HTMLElement {
    constructor() {
        super();
        const style = document.createElement('style');
        style.appendChild(document.createTextNode(`
            .categories {
                display: flex;
                flex-direction: row;
                justify-content: space-around;
                overflow-x: scroll;
            }

            .categories > span {
                cursor: pointer;
                padding: 8px;
                border-left: solid 2px var(--primary-color);
                border-right: solid 2px var(--primary-color);
                width: 100%;
                text-align: center;
            }

            .categories > span.active {
                color: white;
                background: var(--primary-color);
            }
        `))
        this.attachShadow({mode: 'open'})
        this.shadowRoot.appendChild(style)
    }
    
    render() {
        if (this.shop_categories)
            this.shadowRoot.removeChild(this.shop_categories);
        this.shop_categories = document.createElement("div");
        this.shop_categories.classList.add("categories");

        for (const [id, name] of Object.entries(this.categories)) {
            const el = document.createElement('span');
            el.textContent = name;
            el.addEventListener("click", () => {
                if (el.classList.contains("active"))
                    el.classList.remove("active")
                else {
                    this.shop_categories.childNodes.forEach(el => el.classList.remove("active"))
                    el.classList.add("active")
                }
                this.dispatchEvent(new CustomEvent("category-select", { detail: id }))
            })
            this.shop_categories.appendChild(el);
        }
        this.shadowRoot.appendChild(this.shop_categories);
    }

    connectedCallback() {
        const fn = () => {
            fetch("/api/categories")
                .then(res => res.json())
                .then(categories => {
                    this.categories = categories
                    this.render();
                });
        }
        fn();
        this.interval = setInterval(fn, 15000);
    }

    disconnectedCallback() {
        clearInterval(this.interval);
    }

}

window.customElements.define('shop-categories', ShopCategories);
})();