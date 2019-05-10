(function (){
class CartCount extends HTMLElement {
    constructor() {
        super();
    }

    connectedCallback() {
        this.listener = () => this.innerText = (window.shop_cart && window.shop_cart.length) || 0;
        this.listener();
        window.addEventListener("cart-modify", this.listener);
    }

    disconnectedCallback() {
        window.removeEventListener("cart-modify", this.listener);
    }
}

window.customElements.define('cart-count', CartCount);
})();