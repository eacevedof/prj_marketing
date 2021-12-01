import {html, LitElement} from "/assets/js/vendor/lit.dev/lit-bundle.js"
import injson from "/assets/js/common/req.js"
import set_config, {field_errors, clear_errors} from "/assets/js/common/fielderrors.js"

const URL_POST = "/restrict/users/insert"
const URL_REDIRECT = "/restrict/users"
const ACTION = "users.insert"

let texts = {
  tr00: "Send",
  tr01: "Sending",
  tr02: "Error",
  tr03: "Some unexpected error occurred: ",

  f00: "Email",
  f01: "Password",
  f02: "Password confirm",
  f03: "Full name",
  f04: "Address",
  f05: "Birthdate",
}

let fields = {
  email: "",
  password: "",
  password2: "",
  fullname: "",
  address: "",
  birthdate: "",
  phone: ""
}

export class FormCreate extends LitElement {

  static properties = {
    csrf: {type: String},
    issending: {type: Boolean},
    btnsend: {type: String},
  }

  constructor() {
    super()
    this.issending = false
    this.btnsend = texts.tr00

    for(let p in fields)
      this[p] = fields[p]
  }

  async onSubmit(e) {
    e.preventDefault()
    set_config({
      fields: ["email","password","fullname","address","birthdate","phone"],
      wrapper: this.shadowRoot.querySelector("form")
    })

    this.issending = true
    this.btnsend = texts.tr01
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

    this.issending = false
    this.btnsend = texts.tr00

    if(response?.errors){
      const errors = response.errors[0]?.fields_validation
      if(errors) {
        return field_errors(errors)
      }
      return Swal.fire({
        icon: "warning",
        title: texts.tr02,
        html: texts.tr03.concat(response.errors[0]),
      })
    }

    return window.location = URL_REDIRECT

  }//onSubmit

  render() {
    return html`
    <form @submit=${this.onSubmit}>
      <div>
        <label for="email">${texts.f00}</label>
        <div id="field-email">
          <input type="email" id="email" .value=${this.email}>
        </div>
      </div>
      <div>
        <label for="password">${texts.f01}</label>
        <div id="field-password">
          <input type="password" id="password" .value=${this.password}>
        </div>
      </div>
      <div>
        <label for="password2">${texts.f02}</label>
        <div id="field-password2">
          <input type="password" id="password2" .value=${this.password2}>
        </div>
      </div>
      <div>
        <label for="fullname">${texts.f03}</label>
        <div id="field-fullname">
          <input type="text" id="fullname" .value=${this.fullname}>
        </div>
      </div>
      <div>
        <label for="address">${texts.f04}</label>
        <div id="field-address">
          <input type="text" id="address" .value=${this.address}>
        </div>
      </div>
      <div>
        <label for="birthdate">${texts.f05}</label>
        <div id="field-birthdate">
          <input type="date" id="birthdate" .value=${this.birthdate}>
        </div>
      </div>
      <div>
        <button id="btn-submit" ?disabled=${this.issending}>
          ${this.btnsend}
          ${
            this.issending 
              ? html`<img src="/assets/images/common/loading.png" width="25" height="25"/>`
              : html``
          }
        </button>
      </div>
    </form>
    `
  }

}//FormCreate

export default text => {
  texts = text
  customElements.define("form-create", FormCreate)
}