(function (){
class CartTotal extends HTMLElement {
    constructor() {
        super();
    }

    connectedCallback() {
        this.listener = () => {
            if (window.shop_cart) {
                this.innerText = `${
                    (window.shop_cart.reduce((curr, el) => curr + el.price * el.quantity, 0) / 100).toFixed(2)
                } €`;
            } else
                this.innerText = "0 €";
        }
        this.listener();
        window.addEventListener("cart-modify", this.listener);
    }

    disconnectedCallback() {
        window.removeEventListener("cart-modify", this.listener);
    }
}

window.customElements.define('cart-total', CartTotal);
})();