import {html, LitElement} from "/assets/js/vendor/lit.dev/lit-bundle.js"
import injson, {is_2xx} from "/assets/js/common/req.js"
import set_config, {field_errors, clear_errors} from "/assets/js/common/fielderrors.js"

const URL_POST = "/restrict/users/insert"
const URL_REDIRECT = "/restrict/users"
const ACTION = "users.insert"
let CSRF = ""

let texts = {}
let fields = {}


export class FormCreate extends LitElement {

  static properties = {
    csrf: {type: String},
    issending: {type: Boolean},
    btnsend: {type: String},
  }

  constructor() {
    super()
    this.email = ""
    this.password = ""
    this.password2 = ""
    this.fullname = ""
    this.address = ""
    this.birthdate = ""
    this.phone = ""

    this.issending = false
    this.btnsend = "Enviar"

  }

  async onSubmit(e) {
    e.preventDefault()
    set_config({
      fields: ["email","password"],
      wrapper: this.shadowRoot.querySelector("form")
    })

    this.issending = true
    this.btnsend = "send"//texts.tr01
    clear_errors()

    const response = await injson.post(URL_POST, {
      _action: ACTION,
      _csrf: this.csrf,
      email: this.email,
      password: this.password,
      password2: this.password2,
      fullname: this.fullname,
      address: this.address,
      birthdate: this.birthdate,
      phone: this.phone,
    })

    console.log("REEEESSSPOONNSEE",response, "typeof:", typeof response)

    this.issending = false
    this.btnsend = "tr01"//texts.tr01

    if (response?.error)
      return Swal.fire({
        icon: "warning",
        title: "t03",//texts.tr03,
        html: response.error?.message ?? "Error"
      })

    if (!is_2xx(response))
      return Swal.fire({
        icon: "warning",
        title: "t03",//texts.tr03,
        html: response?.data?.message ?? "Error"
      })

    if(response?.errors.length){
      const errors = response.errors[0]?.fields_validation
      if(errors) {
        return field_errors(errors)
      }
      return Swal.fire({
        icon: "warning",
        title: "t03",//texts.tr03,
        html: errors[0], //texts.tr04.concat(response.errors[0]),
      })

    }

    return window.location = URL_REDIRECT

  }//onSubmit

  render() {
    return html`
    <form @submit=${this.onSubmit}>
      <div>
        <label for="email">Email *</label>
        <div id="field-email">
          <input type="email" id="email" .value=${this.email}>
        </div>
      </div>
      <div>
        <label for="password">Password *</label>
        <div id="field-password">
          <input type="password" id="password" .value=${this.password}>
        </div>
      </div>
      <div>
        <label for="password2">Password confirm *</label>
        <div id="field-password2">
          <input type="password" id="password2" .value=${this.password2}>
        </div>
      </div>
      <div>
        <label for="fullname">Full name *</label>
        <div id="field-fullname">
          <input type="text" id="fullname" .value=${this.fullname}>
        </div>
      </div>
      <div>
        <label for="address">Address *</label>
        <div id="field-address">
          <input type="text" id="address" .value=${this.address}>
        </div>
      </div>
      <div>
        <label for="birthdate">Birthdate *</label>
        <div id="field-birthdate">
          <input type="date" id="birthdate" .value=${this.birthdate}>
        </div>
      </div>
      <div>
        <button id="btn-submit" ?disabled=${this.issending}>
          ${this.btnsend}
          ${this.issending ? html`<img src="/assets/images/common/loading.png" width="25" height="25"/>`: html``}
        </button>
      </div>
    </form>
    `
  }

}//FormCreate
customElements.define("form-create", FormCreate)