import {html, LitElement} from "/assets/js/vendor/lit.dev/lit-bundle.js"
import injson from "/assets/js/common/req.js"
import set_config, {field_errors, clear_errors} from "/assets/js/common/fielderrors.js"

const URL_UPDATE = "/restrict/users/update"
const ACTION = "users.update"

let _texts = {
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
  f06: "Phone",
}

let _fields = {
  uuid: "",
  email: "",
  password: "",
  password2: "",
  fullname: "",
  address: "",
  birthdate: "",
  phone: ""
}

export class FormEdit extends LitElement {

  static properties = {
    csrf: {type: String},
    texts: {
      converter: (strjson) => {
        if (strjson) return JSON.parse(strjson)
        return {}
      },
    },

    fields: {
      converter: (strjson) => {
        if (strjson) return JSON.parse(strjson)
        return {}
      },
    },

    issending: {type: Boolean},
    btnsend: {type: String},

    email: {type: String},
    password: {type: String},
    password2: {type: String},
    fullname: {type: String},
    address: {type: String},
    birthdate: {type: String},
    phone: {type: String},
  }



  $get = sel => this.shadowRoot.querySelector(`#${sel}`)
  get_data() {
    const data = Object.keys(_fields)
      .map(field => {
        const ob = {}
        if (field==="uuid") return {}
        ob[field] = this.$get(field)?.value ?? ""
        return ob
      })
      .reduce((old, cur) => ({
        ...old,
        ...cur
      }), {})

    return data
  }

  //1
  constructor() {
    super()
    this.issending = false
    this.btnsend = _texts.tr00

    //this.texts = {}
    //this.fields = {}
    console.log("CONSTRUCTOR","texts",this.texts,"fields:",this.fields)
    for(let p in _fields) this[p] = _fields[p]
  }

  //2
  requestUpdate() {
    super.requestUpdate()
    console.log("requestUpdate","texts",this.texts,"fields:",this.fields)
  }

  //3
  connectedCallback() {
    super.connectedCallback()
    console.log("connectedCallback","texts",this.texts,"fields:",this.fields)
  }

  //4
  render() {
    console.log("render","texts",this.texts,"fields:",this.fields)
    return html`
    <form @submit="${this.onSubmit}">
      <div>
        <label for="email">${_texts.f00}</label>
        <div id="field-email">
          <input type="email" id="email" .value="${this.email}">
        </div>
      </div>
      <div>
        <label for="password">${_texts.f01}</label>
        <div id="field-password">
          <input type="password" id="password" .value="${this.password}">
        </div>
      </div>
      <div>
        <label for="password2">${_texts.f02}</label>
        <div id="field-password2">
          <input type="password" id="password2" .value="${this.password2}">
        </div>
      </div>
      <div>
        <label for="fullname">${_texts.f03}</label>
        <div id="field-fullname">
          <input type="text" id="fullname" .value="${this.fullname}">
        </div>
      </div>
      <div>
        <label for="address">${_texts.f04}</label>
        <div id="field-address">
          <input type="text" id="address" .value="${this.address}">
        </div>
      </div>
      <div>
        <label for="birthdate">${_texts.f05}</label>
        <div id="field-birthdate">
          <input type="date" id="birthdate" .value="${this.birthdate}">
        </div>
      </div>
      <div>
        <label for="phone">${_texts.f06}</label>
        <div id="field-phone">
          <input type="text" id="phone" .value="${this.phone}">
        </div>
      </div>
      <div>
        <button id="btn-submit" ?disabled="${this.issending}">
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
  }//render

  //5
  firstUpdated(changedProperties) {
    console.log("firstUpdated","texts",this.texts,"fields:",this.fields)
    this.$get("email").focus()
  }

  async onSubmit(e) {
    e.preventDefault()
    console.log("onSubmit","texts",this.texts,"fields:",this.fields)
    set_config({
      fields: Object.keys(_fields),
      wrapper: this.shadowRoot.querySelector("form")
    })

    this.issending = true
    this.btnsend = _texts.tr01
    clear_errors()

    const response = await injson.put(
      URL_UPDATE.concat(`/${_fields.uuid}`), {
      _action: ACTION,
      _csrf: this.csrf,
      uuid: _fields.uuid,
      ...this.get_data()
    })

    this.issending = false
    this.btnsend = _texts.tr00

    if(response?.errors){
      let errors = response.errors[0]?.fields_validation
      if(errors) {
        window.snack.set_time(4).set_inner("error").set_color("red").show()
        return field_errors(errors)
      }

      errors = response?.errors
      return window.snack.set_time(4).set_inner(errors.join("<br/>")).set_color("red").show()
    }

    window.snack.set_time(4)
      .set_color("green")
      .set_inner(`<b>Data updated</b>`)
      .show()

    $("#table-datatable").DataTable().ajax.reload()
  }//onSubmit


}//FormEdit

export default (texts, fields) => {
  _texts = texts
  _fields = fields
  //console.log("EDIT.FIELDS",_fields)
  if (!customElements.get("form-edit")) customElements.define("form-edit", FormEdit)
}