(function (){
class ShopProducts extends HTMLElement {
    static get observedAttributes() {
        return ['filter', 'category'];
    }

    constructor() {
        super();
        const style = document.createElement('style');
        style.appendChild(document.createTextNode(`
            .grid {
                display: flex;
                flex-direction: row;
                flex-wrap: wrap;
                justify-content: space-around;
            }

            .grid > shop-product {
                padding: 8px;
            }
            .grid > div.filler {
                width: 250px;
                padding: 0 8px;
                height: 0;
            }
        `))
        this.attachShadow({mode: 'open'})
        this.shadowRoot.appendChild(style)
    }
    
    render(nameFilter = this.getAttribute('filter') || '', categoryFilter = this.getAttribute('category')) {
        if (this.shop_products)
            this.shadowRoot.removeChild(this.shop_products);
        this.shop_products = document.createElement("div");
        this.shop_products.classList.add("grid");

        for (const product of this.products.filter(({ name, category }) => {
            return name.includes(nameFilter) && (!categoryFilter || (category && category.includes(+categoryFilter)))
        })) {
            const { name, img, price, id, quantity } = product;
            const el = document.createElement('shop-product');
            el.setAttribute('name', name);
            el.setAttribute('img', img);
            el.setAttribute('product-id', id);
            el.setAttribute('price', price);
            el.setAttribute('quantity', quantity);
            el.setAttribute('cart', this.getAttribute('from') === "cart");
            el.addEventListener("quantity-change", (e) => {
                product.quantity = e.detail;
                if (this.getAttribute('from') === "cart" && product.quantity === 0) {
                    this.products.splice(this.products.findIndex(({ id: eId }) => eId === id), 1);
                    window.dispatchEvent(new Event("cart-modify"));
                    this.render();
                    return ;
                }
                if (product.quantity < 1)
                    product.quantity = 1;
                el.setAttribute('quantity', product.quantity);
                window.dispatchEvent(new Event("cart-modify"));
            });
            this.shop_products.appendChild(el);
        }
        for (let i = 0; i < 24; i++) {
            const el = document.createElement('div');
            el.classList.add("filler");
            this.shop_products.appendChild(el);
        }
        this.shadowRoot.appendChild(this.shop_products);
    }

    connectedCallback() {
        if (this.getAttribute("from") === "cart") {
            this.listener = () => {
                this.products = window.shop_cart || [];
                this.render();
            };
            this.listener();
            window.addEventListener("cart-modify", this.listener);
        } else {
            const fn = () => {
                fetch("/api/products")
                    .then(res => res.json())
                    .then(products => {
                        this.products = products.map(data => ({ quantity: 1, ...data }));
                        this.render();
                    });
            }
            fn();
            this.interval = setInterval(fn, 15000);
        }
    }

    attributeChangedCallback(attr, oldValue, newValue) {
        if (attr === 'filter')
            this.render(newValue);
        if (attr === 'category')
            this.render(undefined, newValue);
    }

    disconnectedCallback() {
        if (this.getAttribute("from") === "cart") {
            window.removeEventListener("cart-modify", this.listener);
        } else {
            clearInterval(this.interval);
        }
    }

}

window.customElements.define('shop-products', ShopProducts);
})();