import { html, LitElement, css } from "/assets/js/vendor/lit.dev/lit-bundle.js"
import get_cssrules from "/assets/js/common/cssrules.js"
import injson from "/assets/js/common/req.js"
import error from "/assets/js/common/fielderrors.js"
import { SNACK } from "/assets/js/common/snackbar.js"
import {cssformflex} from "/assets/js/common/formflex-lit-css.js"
import {cssfielderror} from "/assets/js/common/fielderrors-lit-css.js"
import {selector, get_formdata} from "/assets/js/common/shadowroot/shadowroot.js"

const URL_INSERT = "/restrict/users/:uuid/preferences/insert"
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

  _get_data() {
    return get_formdata(this.shadowRoot)(this.fields)(["uuid"])
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

    for (let p in this.fields) this["_".concat(p)] = this.fields[p]
  }

  //4
  render() {
    return html`
    <form @submit=${this.on_submit}>
      <table>
        <tr>
          <th>key 
            <p class="tt-tooltip">
              <span class="tt-span">?</span>  
              <span class="tt-tooltiptext">hola</span>
            </p>
          </th>
          <th>value</th>
        </tr>
        <tr>
          <td>
            <input type="text" id="pref_key" .value=${this._pref_key} class="form-control" maxlength="250">
          </td>
          <td>
            <input type="text" id="pref_value" .value=${this._pref_value} class="form-control" maxlength="2000">
          </td>         
          <td>
            <button id="btn-submit" ?disabled=${this._issending} class="btn btn-secondary btn-success btn-icon me-2">
              <span><i class="mdi mdi-plus-box"></i></span> add
              ${this._issending 
                ? html`<img src="/assets/images/common/loading.png" width="25" height="25" />`
                : html``
              }
            </button>            
          </td>
        </tr>
      </table>
      <table>
        <tr>
          <td>
            <input type="text" id="pref_key_0" .value=${this._pref_key_1} class="form-control" maxlength="250">
          </td>
          <td>
            <input type="text" id="pref_value_0" .value=${this._pref_value_0} class="form-control" maxlength="2000">
          </td>
          <td>
            <button type="button" btnid="rowbtn-edit" uuid="620d471857bc4" class="btn btn-info" title="edit">
              <i class="las la-pen"></i>up
            </button>
            <button type="button" btnid="rowbtn-del" uuid="620d471857bc4" class="btn btn-danger" title="remove">
              <i class="las la-trash"></i>del
            </button>            
          </td>
        </tr>
      </table>
    </form>
    `
  }

  //5
  firstUpdated() {
    try {
      this._$get("id").focus()
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
      URL_UPDATE.concat(`/${this.fields.uuid}`), {
        _action: ACTION,
        _csrf: this.csrf,
        uuid: this.fields.uuid,
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

    const $dt = document.getElementById("table-datatable")
    if ($dt) $($dt).DataTable().ajax.reload()
    window.snack.set_time(4)
      .set_color(SNACK.SUCCESS)
      .set_inner(this.texts.tr04)
      .show()

  }//on_submit

}//FormEdit

if (!customElements.get("form-user-preferences-update"))
  customElements.define("form-user-preferences-update", FormUserPreferencesUpdate)
