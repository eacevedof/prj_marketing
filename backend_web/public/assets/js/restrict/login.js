import reqjs from "/assets/js/common/req.js"
import {html, LitElement, css} from "/assets/js/vendor/lit.dev/lit-bundle.js"
import get_cssrules from "/assets/js/common/cssrules.js"
import set_cookie from "/assets/js/common/cookie.js"
import {selector, get_formdata} from "/assets/js/common/shadowroot/shadowroot.js"

const URL = "/login/access"

export class FormLogin extends LitElement {

  static properties = {
    csrf: {type: String},
    issending: {type: Boolean},
    btnsend: {type: String},
  }

  static get styles() {
    const globalStyle = css([get_cssrules(["/themes/valex/"])])
    return [
      globalStyle
    ];
  }

  constructor() {
    super()
    this.email = "eaf@eaf.com"
    this.password = "eaf"
    this.issending = false
    this.btnsend = "Enviar"
  }

  _$get(idsel) { return selector(this.shadowRoot)(idsel) }

  async on_submit(e) {
    e.preventDefault()
    this.issending = true
    this.btnsend = "...enviando"

    const response = await reqjs.post(URL,{
      _csrf: this.csrf,
      email: this._$get("email").value,
      password: this._$get("password").value,
    })

    this.issending = false
    this.btnsend = "Enviar"

    if(response?.errors){
      return Swal.fire({
        icon: "warning",
        title: "Errores",
        html: response.errors.join("<br/>"),
      })
    }

    set_cookie("lang", response.lang)
    Swal.fire({
      icon: "success",
      title: "Acceso concedido",
      showConfirmButton: false,
      html: "...redirigiendo al panel de control",
    })

    setTimeout(() => window.location = response.url_default_module, 1000)
  }//submit

  render() {
    return html`
    <form @submit=${this.on_submit} class="form-horizontal">
      <div class="form-group">
        <input type="email" id="email" .value=${this.email} class="form-control" placeholder="Email"/>
      </div>
      <div class="form-group">
        <input type="password" id="password" .value=${this.password} class="form-control" placeholder="Password" />
      </div>
      <div class="form-group mb-0 mt-3 justify-content-end">
        <div>
          <button type="submit" ?disabled=${this.issending} class="btn btn-primary">
            ${this.btnsend}
            ${this.issending ? html`<img src="/assets/images/common/loading.png" width="25" height="25"/>`: html``}
          </button>
        </div>
      </div>
    </form>
    `
  }

}//FormLogin
customElements.define("form-login", FormLogin)
