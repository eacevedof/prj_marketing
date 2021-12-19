import reqjs from "/assets/js/common/req.js"
import {html, LitElement, css} from "/assets/js/vendor/lit.dev/lit-bundle.js"
import set_cookie from "/assets/js/common/cookie.js"

const URL = "/login/access"
const URL_ON_ACCESS = "/restrict/users"

export class FormLogin extends LitElement {

  static properties = {
    csrf: {type: String},
    issending: {type: Boolean},
    btnsend: {type: String},
  }

  static get styles() {
    const styleSheets = Array.from(document.styleSheets).filter(obj => {
      const href = obj?.href ?? ""
        return href.includes("/themes/valex/")
    }).map(style => {
      //console.log("style",style,"style-values:",Object.values(style.cssRules), "csstext:",Object.values(style.cssRules).map(rule => rule.cssText))
      return Object.values(style.cssRules).map(rule => rule.cssText).join("\n")
    })

    //console.log("stylesheets",styleSheets)
    const globalStyle = css([Object.values(styleSheets).join("\n")])
    return [
      globalStyle
    ];
  }

  constructor() {
    super()
    this.email = ""
    this.password = ""
    this.issending = false
    this.btnsend = "Enviar"
  }

  $get = sel => this.shadowRoot.querySelector(sel)

  async on_submit(e) {
    e.preventDefault()
    this.issending = true
    this.btnsend = "...enviando"

    const response = await reqjs.post(URL,{
      _csrf: this.csrf,
      email: this.$get("#email").value,
      password: this.$get("#password").value,
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

    setTimeout(() => window.location = URL_ON_ACCESS, 1000)
  }//submit

  render() {
    return html`
    <form @submit=${this.on_submit}>
      <div>
        <label for="email">Email</label>
        <input type="email" id="email" .value=${this.email} />
      </div>
      <div>
        <label for="password">Password</label>
        <input type="password" id="password" .value=${this.password} />
      </div>
      <button type="submit" ?disabled=${this.issending}>
        ${this.btnsend}
        ${this.issending ? html`<img src="/assets/images/common/loading.png" width="25" height="25"/>`: html``}
      </button>
    </form>
    `
  }

}//FormLogin
customElements.define("form-login", FormLogin)
