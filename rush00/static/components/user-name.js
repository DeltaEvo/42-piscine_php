(function (){
class UserName extends HTMLElement {
    constructor() {
        super();
    }

    connectedCallback() {
        this.listener = () => this.innerText = window.user && window.user.username;
        this.listener();
        window.addEventListener("user-connect", this.listener);
    }

    disconnectedCallback() {
        window.removeEventListener("user-connect", this.listener);
    }
}

window.customElements.define('user-name', UserName);
})();