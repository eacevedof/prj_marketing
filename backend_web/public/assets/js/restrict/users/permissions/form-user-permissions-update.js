import { html, LitElement, css } from "/assets/js/vendor/lit.dev/lit-bundle.js"
import get_cssrules from "/assets/js/common/cssrules.js"
import injson from "/assets/js/common/req.js"
import error from "/assets/js/common/fielderrors.js"
import {SNACK} from "/assets/js/common/snackbar.js"
import {cssformflex} from "/assets/js/common/formflex-lit-css.js"
import {cssfielderror} from "/assets/js/common/fielderrors-lit-css.js"
import {selector, get_formdata} from "/assets/js/common/shadowroot/shadowroot.js"

const URL_UPDATE = "/restrict/users/:uuid/permissions/update"
const ACTION = "userspermissions.update"

export class FormUserPermissionsUpdate extends LitElement {
  static get styles() {
    const globalStyle = css([get_cssrules([
      "/themes/valex/assets/plugins/bootstrap/css/bootstrap.min.css",
      "/themes/valex/assets/css/style.css",
    ])])
    return [
      globalStyle,
      cssformflex,
      cssfielderror
    ];
  }

  _$get(idsel) { return selector(this.shadowRoot)(idsel) }

  _get_data() {
    return get_formdata(this.shadowRoot)(this.fields)(["id","uuid","id_user"])
  }

  _on_cancel() {
    window.modalraw.hide()
  }

  //1
  constructor() {
    super()
    this.texts = {}
    this.fields = {}
  }

  static properties = {
    csrf: { type: String },
    useruuid: {type:String},

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

    _id: {type: String, state:true},
    _uuid: {type: String, state:true},
    _id_user: {type: String, state:true},
    _json_rw: {type: String, state:true},
  }

  //2
  requestUpdate() {
    super.requestUpdate()
  }

  //3 (aqui siempre hay datos)
  connectedCallback() {
    super.connectedCallback()
    this._issending = false
    this._btnsend = this.texts.tr00
    this._btncancel = this.texts.tr02

    for (let p in this.fields) this["_".concat(p)] = this.fields[p]
  }

  //4
  render() {
    return html`
      <form @submit=${this.on_submit}>
        ${this._id
            ? html`<div class="form-group">
                    <label for="uuid">${this.texts.f01}</label>
                    <div id="field-uuid">${this._uuid}</div>
                  </div>`
            : html``
        }
        
        <div class="form-group">
          <label for="json_rw">${this.texts.f03}</label>
          <div id="field-json_rw">
            <textarea id="json_rw" .value=${this._json_rw} class="form-control" maxlength="2000" rows="18"></textarea>
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
          <button type="button" ?disabled=${this._issending} @click=${this._on_cancel} class="btn btn-secondary mt-3 mb-0">
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

  //5
  firstUpdated() {
    try {
      this._$get("json_rw").focus()
    }
    catch (e) {
      console.log(e)
    }
  }

  //6
  updated() {
    //aqui se deberia des setear la prpiedad despues de una llamada async
  }

  async on_submit(e) {
    e.preventDefault()
    error.config({
      wrapper: this.shadowRoot.querySelector("form"),
      fields: Object.keys(this.fields)
    })

    this._issending = true
    this._btnsend = this.texts.tr01
    error.clear()

    const response = await injson.put(
        URL_UPDATE.replace(":uuid", this.useruuid), {
          _action: ACTION,
          _csrf: this.csrf,
          id: this.fields.id,
          uuid: this.fields.uuid,
          id_user: this.fields.id_user,

          ...this._get_data()
        })

    this._issending = false
    this._btnsend = this.texts.tr00

    if(response?.errors){
      let errors = response.errors[0]?.fields_validation
      if(errors) {
        window.snack.set_time(4).set_inner(this.texts.tr03).set_color(SNACK.ERROR).show()
        return error.append(errors)
      }

      errors = response?.errors
      return window.snack.set_time(4).set_inner(errors.join("<br/>")).set_color(SNACK.ERROR).show()
    }

    //solo si era nuevo permiso
    if (!this.fields.id) {
      this.fields.id = response.result.id
      this.fields.uuid = response.result.uuid

      this._id = this.fields.id
      this._uuid = this.fields.uuid
    }

    window.snack.set_time(4)
      .set_color(SNACK.SUCCESS)
      .set_inner(this.texts.tr04)
      .show()

  }//on_submit
}

if (!customElements.get("form-user-permissions-update"))
  customElements.define("form-user-permissions-update", FormUserPermissionsUpdate)
