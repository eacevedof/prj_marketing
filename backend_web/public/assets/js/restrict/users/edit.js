import {html, LitElement} from "/assets/js/vendor/lit.dev/lit-bundle.js"
import injson from "/assets/js/common/req.js"
import error from "/assets/js/common/fielderrors.js"

const URL_UPDATE = "/restrict/users/update"
const ACTION = "users.update"


export class FormEdit extends LitElement {

  $get = sel => this.shadowRoot.querySelector(`#${sel}`)
  get_data() {
    const data = Object.keys(this.fields)
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
    this.texts = {}
    this.fields = {}
    console.log("CONSTRUCTOR","texts",this.texts,"fields:",this.fields)
  }

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

  /*
  static get properties() {
    //no se ejecuta en ningun lado
    console.log("GETTING PROPERTIES")
    return {
      texts: {type: Object}
    }
  }
  */

  //2
  requestUpdate() {
    super.requestUpdate()
    console.log("requestUpdate","texts",this.texts,"fields:",this.fields)
  }

  //3 (aqui siempre hay datos)
  connectedCallback() {
    super.connectedCallback()
    this.issending = false
    this.btnsend = this.texts.tr00

    //this.email = this.fields.email
    for(let p in this.fields) this[p] = this.fields[p]
    console.log("connectedCallback","texts",this.texts,"fields:",this.fields)
  }

  //4
  render() {
    console.log("render","texts",this.texts,"fields:",this.fields)
    return html`
    <form @submit="${this.onSubmit}">
      <div>
        <label for="email">${this.texts.f00}</label>
        <div id="field-email">
          <input type="email" id="email" .value="${this.email}">
        </div>
      </div>
      <div>
        <label for="password">${this.texts.f01}</label>
        <div id="field-password">
          <input type="password" id="password" .value="${this.password}">
        </div>
      </div>
      <div>
        <label for="password2">${this.texts.f02}</label>
        <div id="field-password2">
          <input type="password" id="password2" .value="${this.password2}">
        </div>
      </div>
      <div>
        <label for="fullname">${this.texts.f03}</label>
        <div id="field-fullname">
          <input type="text" id="fullname" .value="${this.fullname}">
        </div>
      </div>
      <div>
        <label for="address">${this.texts.f04}</label>
        <div id="field-address">
          <input type="text" id="address" .value="${this.address}">
        </div>
      </div>
      <div>
        <label for="birthdate">${this.texts.f05}</label>
        <div id="field-birthdate">
          <input type="date" id="birthdate" .value="${this.birthdate}">
        </div>
      </div>
      <div>
        <label for="phone">${this.texts.f06}</label>
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
    error.config({
      wrapper: this.shadowRoot.querySelector("form"),
      fields: Object.keys(this.fields)
    })

    this.issending = true
    this.btnsend = this.texts.tr01
    error.clear()

    const response = await injson.put(
      URL_UPDATE.concat(`/${this.fields.uuid}`), {
      _action: ACTION,
      _csrf: this.csrf,
      uuid: this.fields.uuid,
      ...this.get_data()
    })

    this.issending = false
    this.btnsend = this.texts.tr00

    if(response?.errors){
      let errors = response.errors[0]?.fields_validation
      if(errors) {
        window.snack.set_time(4).set_inner("error").set_color("red").show()
        return error.append(errors)
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

if (!customElements.get("form-edit"))
  customElements.define("form-edit", FormEdit)
