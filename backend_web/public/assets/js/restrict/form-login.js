import reqjs from "/assets/js/common/req.js"
import {html, LitElement, css} from "/assets/js/vendor/lit.dev/lit-bundle.js"
import get_cssrules from "/assets/js/common/cssrules.js"
import set_cookie from "/assets/js/common/cookie.js"
import {selector} from "/assets/js/common/shadowroot/shadowroot.js"

export class FormLogin extends LitElement {

  static properties = {
    csrf: "",
    url: "",
    texts: {
      converter: (strjson) => {
        if (strjson) return JSON.parse(strjson)
        return {}
      },
    },

    _email: "",
    _password: "",
    _issending: false,
    _btnsend: "",
  }

  static get styles() {
    const globalStyle = css([get_cssrules(["/themes/valex/"])])
    return [
      globalStyle
    ];
  }

  constructor() {
    super()
    this._email = "eaf@eaf.com"
    this._password = "eaf"
  }

  connectedCallback() {
    super.connectedCallback()
    this._btnsend = this.texts.tr00
  }

  _$get(idsel) { return selector(this.shadowRoot)(idsel) }

  firstUpdated() {
    try {
      this._$get("email").focus()
    }
    catch (e) {
      console.log(e)
    }
  }

  async on_submit(e) {
    e.preventDefault()

    if (!(this._email.trim() || this._password.trim())) {
      return Swal.fire({
        icon: "warning",
        title: this.texts.tr06,
      })
    }

    this._issending = true
    this._btnsend = this.texts.tr01
    const geturl = window.location.search.substr(1)
    const response = await reqjs.post(
      this.url.concat(geturl.includes("redirect=") && geturl.includes("restrict") ? "?".concat(geturl) : ""),
      {
        _csrf: this.csrf,
        email: this._email,
        password: this._password,
      }
    )

    this._issending = false
    this._btnsend = this.texts.tr00

    if(response?.errors){
      //this._$get("email").focus()
      return Swal.fire({
        icon: "warning",
        title: this.texts.tr03,
        html: response.errors.join("<br/>"),
      })
    }

    set_cookie("lang", response.lang)
    window.Swal.fire({
      icon: "success",
      title: this.texts.tr04,
      showConfirmButton: false,
      html: this.texts.tr05
    })
    
    setTimeout(() => window.location = response.url_default_module, 1000)
  }

  _handle_keyup(e, field) {
    const value = e.target.value
    this[field] = value
  }

  render() {
    return html`
    <form @submit=${this.on_submit} class="form-horizontal">
      <div class="form-group">
        <input type="email" id="email" .value=${this._email} 
               @change=${e => this._handle_keyup(e, "_email")} class="form-control" placeholder=${this.texts.f00} autofocus />
      </div>
      <div class="form-group">
        <input type="password" id="password" .value=${this._password} @change=${e => this._handle_keyup(e, "_password")} class="form-control" placeholder=${this.texts.f01} />
      </div>
      <div class="form-group mb-0 mt-3 justify-content-end">
        <div>
          <button type="submit" ?disabled=${this._issending} class="btn btn-primary">
            ${this._btnsend}
            ${this._issending ? html`<img src="/assets/images/common/loading.png" width="25" height="25"/>`: html``}
          </button>
        </div>
      </div>
    </form>
    `
  }
}
customElements.define("form-login", FormLogin)
