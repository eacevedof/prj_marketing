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
const URL_DELETE = "/restrict/users/:uuid/preferences/delete"

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

  _on_change(ev){
    const input = ev.target
    const id = this._get_id(input)
    if (!id) return
    this[`_${id}`] = input.value
  }

  _get_id(el){
    if (!el) return null
    const id = el?.id
    if (!id) return null
    return id
  }

  _get_grid_idx = id => id.split("_").pop()

  _get_row_value = el => {
    const id = this._get_id(el)
    const idx = this._get_grid_idx(id)
    const row = [`id_${idx}`,`pref_key_${idx}`,`pref_value_${idx}`]
                  .map( id => {
                    const tmp = {}
                    tmp[id.replace(`_${idx}`,"")] = this._$get(id)?.value
                    return tmp
                  })
                  .reduce((p, obc) => ({...p, ...obc}), {})
    row["_idx"] = idx
    return row
  }

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
        pref_key: this._pref_key,
        pref_value: this._pref_value,
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
    this._pref_key = ""
    this._pref_value = ""

    this._$get("pref_key").focus()

    window.snack.set_time(4)
      .set_color(SNACK.SUCCESS)
      .set_inner(this.texts.tr04)
      .show()
  }// on_insert

  async _on_update(e) {
    e.preventDefault()

    this._issending = true
    this._btnsend = this.texts.tr01

    const row = this._get_row_value(e.target)

    const response = await injson.del(
      URL_UPDATE.replace(":uuid", this.useruuid), {
        _action: ACTION,
        _csrf: this.csrf,

        id: row.id,
        pref_key: row.pref_key
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
    this._$get(`pref_key_${row._idx}`)?.focus()

    window.snack.set_time(4)
      .set_color(SNACK.SUCCESS)
      .set_inner(this.texts.tr04)
      .show()
  }// on_update

  async _on_delete(e) {
    e.preventDefault()

    this._issending = true
    this._btnsend = this.texts.tr01

    const row = this._get_row_value(e.target)

    const response = await injson.put(
      URL_DELETE.replace(":uuid", this.useruuid), {
        _action: ACTION,
        _csrf: this.csrf,

        id: row.id,
        pref_key: row.pref_key
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

    this._list = []
    this._list = response.result
    this._$get(`pref_key_${row._idx}`)?.focus()

    window.snack.set_time(4)
      .set_color(SNACK.SUCCESS)
      .set_inner(this.texts.tr04)
      .show()
  }

  //propiedades reactivas
  static properties = {
    csrf: { type: String },
    useruuid: { type: String },

    texts: {
      converter: (strjson) => {
        console.log("converting texts")
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

    //state true indica que es un estado interno
    _issending: { type: Boolean, state: true },
    _btnsend: { type: String, state: true },
    _btncancel: { type: String, state: true },

    _id: {type: String, state:true},
    _id_user: {type: String, state:true},
    _pref_key: {type: String, state:true},
    _pref_value: {type: String, state:true},

    //_pref_key_up: {type: String, state:true},
    //_pref_value_up: {type: String, state:true},

    _list: {type: Array, state:true},
  }

  //1
  constructor() {
    console.log("contructor")
    super()
    this._pref_key = ""
    this._pref_value = ""
    this._issending = false
  }

  //3 aqui los callbacks de properties ya se han procesado
  connectedCallback() {
    super.connectedCallback()
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
          <th><label for="pref_key">${this.texts.f02}</label></th>
          <th><label for="pref_value">${this.texts.f03}</label></th>
        </tr>
        <tr>
          <td>
            <div id="field-pref_key">
              <input type="text" id="pref_key" .value=${this._pref_key} @change=${this._on_change} class="form-control" maxlength="250">
            </div>
          </td>
          <td>
            <div id="field-pref_value">
            <input type="text" id="pref_value" .value=${this._pref_value} @change=${this._on_change} class="form-control" maxlength="2000">
            </div>
          </td>         
          <td>
            <button type="button" ?disabled=${this._issending} @click="${this._on_insert}" class="btn btn-secondary btn-success btn-icon me-2">
              <span><i class="mdi mdi-plus-box"></i></span> add
              ${this._issending 
                ? html`<img src="/assets/images/common/loading.png" width="25" height="25" />`
                : null
              }
            </button>            
          </td>
        </tr>
      </table>
      <hr/>
      <table>
        ${this._list.map( (row, i) =>
          html`      
            <tr id="row_${i}">
              <td>
                <input type="hidden" id="id_${i}" value="${row.id}" class="form-control">
                <input type="text" id="pref_key_${i}" value=${row.pref_key} class="form-control" maxlength="250">
              </td>
              <td>
                <input type="text" id="pref_value_${i}" value=${row.pref_value} class="form-control" maxlength="2000">
              </td>
              <td>
                <button type="button" id="update_${i}" @click="${this._on_update}" class="btn btn-info">
                  <i class="las la-pen"></i>up
                </button>
                <button type="button" id="delete_${i}" @click="${this._on_delete}" class="btn btn-danger">
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
