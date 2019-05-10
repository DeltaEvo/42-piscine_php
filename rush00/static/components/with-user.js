(function (){
class WithUser extends HTMLElement {
    constructor() {
        super();

        this.user_slot = document.createElement("slot");
        this.attachShadow({mode: 'open'})
            .appendChild(this.user_slot);
    }

    connectedCallback() {
        this.setAttribute("mounted", true);
        this.shadowRoot.firstChild.name = 'loading';

        this.listener = () => {
            if (user !== false && (!this.hasAttribute("role")
                        || this.getAttribute("role") === user.role)) {
                this.user_slot.name = 'logged';
            } else
                this.user_slot.name = 'none';
        }
        window.addEventListener("user-connect", this.listener);

        fetch("/api/auth/who")
            .then(res => res.json())
            .then(user => {
                window.user = user;
                window.dispatchEvent(new Event("user-connect"));
            })
    }

    disconnectedCallback() {
        this.removeAttribute("mounted");
        window.removeEventListener("user-connect", this.listener);
    }
}

window.customElements.define('with-user', WithUser);
})();