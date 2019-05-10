(function (){
// For syntax 
function html(strings, ...keys) {
    const lastIndex = strings.length - 1;
    return strings
    .slice(0, lastIndex)
    .reduce((p, s, i) => p + s + keys[i], '')
    + strings[lastIndex];
};

const template = document.createElement('template');

template.innerHTML =
html`
    <style>
        .shop-product {
            width: 250px;
            box-shadow: 0px 3px 1px -2px rgba(0,0,0,0.2), 0px 2px 2px 0px rgba(0,0,0,0.14), 0px 1px 5px 0px rgba(0,0,0,0.12);
        }
        .shop-product:hover {
            box-shadow: 0px 3px 5px -1px rgba(0,0,0,0.2), 0px 5px 8px 0px rgba(0,0,0,0.14), 0px 1px 14px 0px rgba(0,0,0,0.12);
        }
        .shop-product > div {
            padding-top: 24px;
            padding: 16px;
        }
        .shop-product > img {
            width: 100%;
            height: 200px;
            object-fit: contain;
        }
        .shop-product > div > h2 {
            margin: 0;
            font-size: 24px;
            font-weight: 400;
            line-height: 32px;
            letter-spacing: normal;
            font-family: 'Roboto', sans-serif;
        }
        .shop-product > div > p {
            margin: 0;
        }
    </style>
    <div class="shop-product">
        <img>
        <div>
            <h2></h2>
            <p></p>
            <input type="number" name="quantity" value="1">
            <button>Add To Cart</button>
        </div>
    </div>
`;

class ShopProduct extends HTMLElement {
    static get observedAttributes() {
        return ['price', 'name', 'img', 'quantity'];
    }

    constructor() {
        super();
        this.attachShadow({mode: 'open'})
        this.shadowRoot.appendChild(template.content.cloneNode(true));
        this.shop_img = this.shadowRoot.querySelector('div > img');
        this.shop_name = this.shadowRoot.querySelector('div > div > h2');
        this.shop_price = this.shadowRoot.querySelector('div > div > p');
        this.shop_quantity = this.shadowRoot.querySelector('div > div > input');
        this.shop_button = this.shadowRoot.querySelector('div > div > button');
    }

    connectedCallback() {
        this.shop_price.textContent = `${(this.getAttribute('price') / 100).toFixed(2)} €`
        this.shop_img.src = this.getAttribute('img')
        this.shop_name.textContent = `${this.getAttribute('product-id')} - ${this.getAttribute('name')}`
        this.shop_quantity.addEventListener("input", () => {
            this.dispatchEvent(new CustomEvent("quantity-change", {
                detail: +this.shop_quantity.value
            }))
        })
        if (this.getAttribute("cart") === "false")
            this.shop_button.addEventListener("click", () => {
                if (!window.shop_cart)
                    window.shop_cart = [];
                const id = +this.getAttribute('product-id');
                const data = window.shop_cart.find(({ id: eId }) => id === eId);
                if (data) {
                    data.quantity += +this.getAttribute("quantity");
                } else {
                    window.shop_cart.push({
                        name: this.getAttribute('name'),
                        price: +this.getAttribute('price'),
                        img: this.getAttribute('img'),
                        id,
                        quantity: +this.getAttribute('quantity')
                    })
                }
                window.dispatchEvent(new Event("cart-modify"));
            })
        else
            this.shadowRoot.querySelector('div > div').removeChild(this.shop_button)
    }

    attributeChangedCallback(attr, oldValue, newValue) {
        if (attr === 'price')
            this.shop_price.textContent = `${(newValue / 100).toFixed(2)} €`
        if (attr === 'name')
            this.shop_name.textContent = newValue
        if (attr === 'img')
            this.shop_img.src = newValue;
        if (attr === 'quantity')
            this.shop_quantity.value = newValue;
    }
}

window.customElements.define('shop-product', ShopProduct);
})();