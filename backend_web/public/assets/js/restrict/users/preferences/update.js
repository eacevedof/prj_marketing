import { html, LitElement, css } from "/assets/js/vendor/lit.dev/lit-bundle.js"
import get_cssrules from "/assets/js/common/cssrules.js"
import injson from "/assets/js/common/req.js"
import error from "/assets/js/common/fielderrors.js"
import { SNACK } from "/assets/js/common/snackbar.js"
import {cssformflex} from "/assets/js/common/formflex-lit-css.js"
import {cssfielderror} from "/assets/js/common/fielderrors-lit-css.js"
import {selector} from "/assets/js/common/shadowroot/shadowroot.js"

const URL_INSERT = "/restrict/users/:uuid/preferences/update"
const URL_UPDATE = "/restrict/users/:uuid/preferences/update"

const ACTION = "userpreferences.update"

export class FormUserPreferencesUpdate extends LitElement {
  static get styles() {
    const globalStyle = css([get_cssrules([
      "/themes/valex/assets/plugins/bootstrap/css/bootstrap.min.css",
      "/themes/valex/assets/css/style.css",
      //"/assets/css/vendor/theme-min/icons.css", los iconos no cargan
      "/assets/css/common/tooltip.css"
    ])])
    return [
      globalStyle,
      cssformflex,
      cssfielderror
    ];
  }

  _$get(idsel) { return selector(this.shadowRoot)(idsel) }

  async _on_insert(e) {
    e.preventDefault()
    error.config({
      wrapper: this.shadowRoot.querySelector("form"),
      fields: Object.keys(this.fields)
    })

    this._issending = true
    this._btnsend = this.texts.tr01
    error.clear()

    const response = await injson.put(
      URL_INSERT.replace(":uuid", this.useruuid), {
        _action: ACTION,
        _csrf: this.csrf,
        pref_key: this._$get("pref_key").value,
        pref_value: this._$get("pref_value").value,
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

    this._list = response.result

    window.snack.set_time(4)
      .set_color(SNACK.SUCCESS)
      .set_inner(this.texts.tr04)
      .show()
  }

  _on_update() {

  }

  _on_delete() {

  }

  //1
  constructor() {
    super()
    this.texts = {}
    this.fields = {}

    this._useruuid = this.useruuid
    this._pref_key = ""
    this._pref_value = ""
    this._list = []
  }

  static properties = {
    csrf: { type: String },
    useruuid: { type: String },

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
    _id_user: {type: String, state:true},
    _pref_key: {type: String, state:true},
    _pref_value: {type: String, state:true},

    _list: {type: Array, state:true},
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
    this._list = this.fields
  }

  //4
  render() {
    return html`
    <form>
      <table>
        <tr>
          <th><label for="pref_key">${this.texts.f02}</label> </th>
          <th><label for="pref_value">${this.texts.f03}</label> </th>
        </tr>
        <tr>
          <td>
            <div id="field-pref_key">
              <input type="text" id="pref_key" .value=${this._pref_key} class="form-control" maxlength="250">
            </div>
          </td>
          <td>
            <div id="field-pref_value">
            <input type="text" id="pref_value" .value=${this._pref_value} class="form-control" maxlength="2000">
            </div>
          </td>         
          <td>
            <button type="button" ?disabled=${this._issending} @click="${this._on_insert}" class="btn btn-secondary btn-success btn-icon me-2">
              <span><i class="mdi mdi-plus-box"></i></span> add
              ${this._issending 
                ? html`<img src="/assets/images/common/loading.png" width="25" height="25" />`
                : html``
              }
            </button>            
          </td>
        </tr>
      </table>
      <hr/>
      <table>
        ${this._list.map( (row, i) =>
            html`      
            <tr>
              <td>
                <input type="hidden" id="id_${i}" value="${row.id}" class="form-control">
                <input type="text" id="pref_key_${i}" value="${row.pref_key}" class="form-control" maxlength="250">
              </td>
              <td>
                <input type="text" id="pref_key_${i}" value="${row.pref_value}" class="form-control" maxlength="2000">
              </td>
              <td>
                <button type="button" @click="${this._on_insert}" class="btn btn-info">
                  <i class="las la-pen"></i>up
                </button>
                <button type="button" @click="${this._on_delete}" class="btn btn-danger">
                  <i class="las la-trash"></i>del
                </button>
              </td>
            </tr>
            `
        )}
      </table>
    </form>
    `
  }

  //5
  firstUpdated() {
    try {
      this._$get("pref_key").focus()
    }
    catch (e) {
      console.log(e)
    }
  }

  //6
  updated() {
    //aqui se deberia des setear la prpiedad despues de una llamada async
  }

}//FormEdit

if (!customElements.get("form-user-preferences-update"))
  customElements.define("form-user-preferences-update", FormUserPreferencesUpdate)
