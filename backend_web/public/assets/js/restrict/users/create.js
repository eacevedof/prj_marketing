import {html, LitElement} from "/assets/js/vendor/lit.dev/lit-bundle.js"
import injson from "/assets/js/common/req.js"
import error from "/assets/js/common/fielderrors.js"

const URL_POST = "/restrict/users/insert"
const URL_REDIRECT = "/restrict/users"
const ACTION = "users.insert"

export class FormUserCreate extends LitElement {

  $get = sel => this.shadowRoot.querySelector(`#${sel}`)

  get_data() {
    const data = Object.keys(this.fields)
      .map(field => {
        const ob = {}
        if (field==="uuid") return {}
        if (["parents","profiles","countries","languages"].includes(field)) return {}
        ob[field] = this.$get(field)?.value ?? ""
        return ob
      })
      .reduce((old, cur) => ({
        ...old,
        ...cur
      }), {})

    return data
  }

  on_profile(e) {
    //console.log("E",e.target.value)
    this.is_parent = false
    if (e.target.value === "4")
      this.is_parent = true
    //this.requestUpdate()
  }

  //1
  constructor() {
    super()
    this.texts = {}
    this.fields = {}
    this.is_parent = false
    //console.log("CONSTRUCTOR","texts",this.texts,"fields:",this.fields)
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

    is_parent: {type: Boolean},
    id_parent: {type: String},
    id_country: {type: String},
    id_language: {type: String},
    id_profile: {type: String},

    parents: {type: Array},
    countries: {type: Array},
    languages: {type: Array},
    profiles: {type: Array},
  }

  //2
  requestUpdate() {
    super.requestUpdate()
    //console.log("requestUpdate","texts",this.texts,"fields:",this.fields)
  }

  //3 (aqui siempre hay datos)
  connectedCallback() {
    super.connectedCallback()
    this.issending = false
    this.btnsend = this.texts.tr00

    //this.email = this.fields.email
    for(let p in this.fields) this[p] = this.fields[p]
    //console.log("connectedCallback","parents:",this.parents)
  }

  //4
  render() {
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
        <label for="id_profile">${this.texts.f08}</label>
        <div id="field-id_profile">
          <select id="id_profile" @change=${this.on_profile}>
            ${this.profiles.map((item) =>
              html`<option value="${item.key}" ?selected="${item.key===this.id_profile}">${item.value}</option>`
            )}
          </select>
        </div>
      </div>
      
      ${this.is_parent
        ? html`<div>
            <label for="id_parent">${this.texts.f07}</label>
            <div id="field-id_parent">
              <select id="id_parent">
                ${this.parents.map((item) =>
                  html`<option value="${item.key}">${item.value}</option>`
                )}
              </select>
            </div>
          </div>`
        : html ``
      }
      
      <div>
        <label for="id_country">${this.texts.f10}</label>
        <div id="field-id_country">
          <select id="id_country">
            ${this.countries.map((item) =>
              html`<option value="${item.key}">${item.value}</option>`
            )}
          </select>
        </div>
      </div>
      
      <div>
        <label for="id_language">${this.texts.f09}</label>
        <div id="field-id_language">
          <select id="id_language">
            ${this.languages.map((item) =>
              html`<option value="${item.key}">${item.value}</option>`
            )}
          </select>
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
  }
  //render

  //5
  firstUpdated(changedProperties) {
    this.$get("email").focus()
    //console.log("firstUpdated","texts",this.texts,"fields:",this.fields)
  }

  //6
  updated(){
    //aqui se deberia des setear la prpiedad despues de una llamada async
    //console.log("updated", this.fields)
  }

  async onSubmit(e) {
    e.preventDefault()
    ////console.log("onSubmit","texts",this.texts,"fields:",this.fields)
    error.config({
      wrapper: this.shadowRoot.querySelector("form"),
      fields: Object.keys(this.fields)
    })

    this.issending = true
    this.btnsend = this.texts.tr01
    error.clear()

    const response = await injson.post(
      URL_POST, {
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

    $("#table-datatable").DataTable().ajax.reload()
    window.modalraw.hide()
    window.snack.set_time(4)
      .set_color("green")
      .set_inner(`<b>Data created</b>`)
      .show()

  }//onSubmit

}//FormEdit

if (!customElements.get("form-user-create"))
  customElements.define("form-user-create", FormUserCreate)
