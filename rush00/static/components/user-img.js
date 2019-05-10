(function (){
class UserImg extends HTMLElement {
    constructor() {
        super();
        this.user_img = document.createElement("img");
        const style = document.createElement('style');
        style.appendChild(document.createTextNode(`
            img {
                width: 100%;
                height: 100%;
            }
        `))
        this.attachShadow({mode: 'open'})
        this.shadowRoot.appendChild(style)
        this.shadowRoot.appendChild(this.user_img);
    }

    connectedCallback() {
        this.setAttribute("mounted", true);
        this.listener = () => {
            const img = window.user && window.user.img;
            if (img)
                this.user_img.src = img;
        };
        this.listener();
        window.addEventListener("user-connect", this.listener);
    }

    disconnectedCallback() {
        this.removeAttribute("mounted");
        window.removeEventListener("user-connect", this.listener);
    }
}

window.customElements.define('user-img', UserImg);
})();