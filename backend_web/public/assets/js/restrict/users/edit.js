import { html, LitElement, css } from "/assets/js/vendor/lit.dev/lit-bundle.js"
import get_cssrules from "/assets/js/common/cssrules.js"
import injson from "/assets/js/common/req.js"
import error from "/assets/js/common/fielderrors.js"
import { SNACK } from "/assets/js/common/snackbar.js"
import {cssformflex} from "/assets/js/common/formflex-lit-css.js"
import {cssfielderror} from "/assets/js/common/fielderrors-lit-css.js"

const URL_UPDATE = "/restrict/users/update"
const ACTION = "users.update"

export class FormUserEdit extends LitElement {
  static get styles() {
    const globalStyle = css([get_cssrules([
      "/themes/valex/assets/plugins/bootstrap/css/bootstrap.min.css",
      "/themes/valex/assets/css/style.css",
    ])])
    //console.log(globalStyle)
    return [
      globalStyle,
      cssformflex,
      cssfielderror
    ];
  }

  $get = sel => this.shadowRoot.querySelector(`#${sel}`)

  get_data() {
    const data = Object.keys(this.fields)
        .map(field => {
          const ob = {}
          if (field === "uuid") return {}
          if (["parents", "profiles", "countries", "languages"].includes(field)) return {}
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
    this._is_parent = false
    if (e.target.value === "4")
      this._is_parent = true
    else
      this._id_parent = ""
  }

  on_cancel() {
    window.modalraw.hide()
  }

  //1
  constructor() {
    super()
    this.texts = {}
    this.fields = {}
    //console.log("CONSTRUCTOR","texts",this.texts,"fields:",this.fields)
  }

  static properties = {
    //https://lit.dev/docs/components/properties/#property-options
    csrf: { type: String },
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

    _issending: { type: Boolean, state: true },
    _btnsend: { type: String, state: true },
    _btncancel: { type: String, state: true },

    _email: { type: String, state: true },
    _password: { type: String, state: true },
    _password2: { type: String, state: true },
    _fullname: { type: String, state: true },
    _address: { type: String, state: true },
    _birthdate: { type: String, state: true },
    _phone: { type: String, state: true },

    _is_parent: { type: Boolean, state: true },
    _id_parent: { type: String, state: true },
    _id_country: { type: String, state: true },
    _id_language: { type: String, state: true },
    _id_profile: { type: String, state: true },

    _parents: { type: Array, state: true },
    _countries: { type: Array, state: true },
    _languages: { type: Array, state: true },
    _profiles: { type: Array, state: true },
  }

  //2
  requestUpdate() {
    super.requestUpdate()
    //console.log("requestUpdate","texts",this.texts,"fields:",this.fields)
  }

  //3 (aqui siempre hay datos)
  connectedCallback() {
    super.connectedCallback()
    this._issending = false
    this._btnsend = this.texts.tr00
    this._btncancel = "Cancel"

    //this._email = this.fields.email
    for (let p in this.fields) this["_".concat(p)] = this.fields[p]
    //console.log("connectedCallback","parents:",this._parents)
    this._is_parent = false
    if (this._id_profile === "4") this._is_parent = true
  }

  //4
  render() {
    //console.log("render","texts",this.texts,"fields:",this.fields)
    return html`
    <form @submit=${this.on_submit}>
      <div class="flex-row">
        <div class="flex-column-n1">
          
          <div class="form-group">
            <label for="email">${this.texts.f00}</label>
            <div id="field-email">
              <input type="email" id="email" .value=${this._email} class="form-control">
            </div>
          </div>
    
          <div class="form-group">
            <label for="password">${this.texts.f01}</label>
            <div id="field-password">
              <input type="password" id="password" .value=${this._password} class="form-control">
            </div>
          </div>
    
          <div class="form-group">
            <label for="password2">${this.texts.f02}</label>
            <div id="field-password2">
              <input type="password" id="password2" .value=${this._password2} class="form-control">
            </div>
          </div>
    
          <div class="form-group">
            <label for="fullname">${this.texts.f03}</label>
            <div id="field-fullname">
              <input type="text" id="fullname" .value=${this._fullname} class="form-control">
            </div>
          </div>
    
          <div class="form-group">
            <label for="address">${this.texts.f04}</label>
            <div id="field-address">
              <input type="text" id="address" .value=${this._address} class="form-control">
            </div>
          </div>
    
        </div>
    
        <div class="flex-column-n2">
          <div class="form-group">
            <label for="birthdate">${this.texts.f05}</label>
            <div id="field-birthdate">
              <input type="date" id="birthdate" .value=${this._birthdate} class="form-control">
            </div>
          </div>
    
          <div class="form-group">
            <label for="phone">${this.texts.f06}</label>
            <div id="field-phone">
              <input type="text" id="phone" .value=${this._phone} class="form-control">
            </div>
          </div>
    
          <div class="form-group">
            <label for="id_profile">${this.texts.f08}</label>
            <div id="field-id_profile">
              <select id="id_profile" @change=${this.on_profile} class="form-control">
                ${this._profiles.map((item) =>
                  html`<option value=${item.key} ?selected=${item.key===this._id_profile}>${item.value}</option>`
                )}
              </select>
            </div>
          </div>
    
          ${this._is_parent
          ? html`
            <div class="form-group">
              <label for="id_parent">${this.texts.f07}</label>
              <div id="field-id_parent">
                <select id="id_parent" class="form-control">
                  ${this._parents.map((item) =>
                    html`<option value=${item.key} ?selected=${item.key===this._id_parent}>${item.value}</option>`
                  )}
                </select>
              </div>
            </div>`
          : html``
          }
    
          <div class="form-group">
            <label for="id_country">${this.texts.f10}</label>
            <div id="field-id_country">
              <select id="id_country" class="form-control">
                ${this._countries.map((item) =>
                  html`<option value=${item.key} ?selected=${item.key===this._id_country}>${item.value}</option>`
                )}
              </select>
            </div>
          </div>
    
          <div class="form-group">
            <label for="id_language">${this.texts.f09}</label>
            <div id="field-id_language">
              <select id="id_language" class="form-control">
              ${this._languages.map((item) =>
                html`<option value=${item.key} ?selected=${item.key===this._id_language}>${item.value}</option>`
              )}
              </select>
            </div>
          </div>
    
        </div>
      </div>
    
      <div class="form-group mb-0">
        <button id="btn-submit" ?disabled=${this._issending} class="btn btn-primary mt-3 mb-0">
          ${this._btnsend}
          ${this._issending
            ? html`<img src="/assets/images/common/loading.png" width="25" height="25" />`
            : html``
          }
        </button>
        <button type="button" ?disabled=${this._issending} @click=${this.on_cancel} class="btn btn-secondary mt-3 mb-0">
        ${this._btncancel}
        ${this._issending
          ? html`<img src="/assets/images/common/loading.png" width="25" height="25" />`
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
  updated() {
    //aqui se deberia des setear la prpiedad despues de una llamada async
    //console.log("updated", this.fields)
  }

  async on_submit(e) {
    e.preventDefault()
    //console.log("on_submit","texts",this.texts,"fields:",this.fields)
    error.config({
      wrapper: this.shadowRoot.querySelector("form"),
      fields: Object.keys(this.fields)
    })

    this._issending = true
    this._btnsend = this.texts.tr01
    error.clear()

    const response = await injson.put(
        URL_UPDATE.concat(`/${this.fields.uuid}`), {
          _action: ACTION,
          _csrf: this.csrf,
          uuid: this.fields.uuid,
          ...this.get_data()
        })

    this._issending = false
    this._btnsend = this.texts.tr00

    if (response?.errors) {
      let errors = response.errors[0]?.fields_validation
      if (errors) {
        window.snack.set_time(4).set_inner("Error").set_color(SNACK.ERROR).show()
        return error.append(errors)
      }

      errors = response?.errors
      return window.snack.set_time(4).set_inner(errors.join("<br/>")).set_color(SNACK.ERROR).show()
    }

    $("#table-datatable").DataTable().ajax.reload()
    window.snack.set_time(4)
        .set_color(SNACK.SUCCESS)
        .set_inner(`<b>Data updated</b>`)
        .show()

  }//on_submit

}//FormEdit

if (!customElements.get("form-user-edit"))
  customElements.define("form-user-edit", FormUserEdit)
