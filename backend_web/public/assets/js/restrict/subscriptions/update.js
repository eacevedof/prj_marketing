import { html, LitElement, css } from "/assets/js/vendor/lit.dev/lit-bundle.js"
import get_cssrules from "/assets/js/common/cssrules.js"
import injson from "/assets/js/common/req.js"
import error from "/assets/js/common/fielderrors.js"
import { SNACK } from "/assets/js/common/snackbar.js"
import {cssformflex} from "/assets/js/common/formflex-lit-css.js"
import {cssfielderror} from "/assets/js/common/fielderrors-lit-css.js"
import {csstooltip} from "/assets/js/common/tooltip-lit-css.js"
import {selector, get_formdata} from "/assets/js/common/shadowroot/shadowroot.js"

const URL_UPDATE = "/restrict/subscriptions/update-status/:uuid"
const ACTION = "subscriptions.update.status"

export class FormSubscriptionUpdate extends LitElement {
  static get styles() {
    const globalStyle = css([get_cssrules([
      "/themes/valex/assets/plugins/bootstrap/css/bootstrap.min.css",
      "/themes/valex/assets/css/style.css",
    ])])
    return [
      globalStyle,
      cssformflex,
      cssfielderror,
      csstooltip
    ];
  }

  _$get(idsel) { return selector(this.shadowRoot)(idsel) }

  _get_data() {
    return get_formdata(this.shadowRoot)(this.fields)(["capuseruuid","subs_status"])
  }

  _on_cancel() {
    window.modalraw.hide()
  }

  _load_response(result) {

  }

  _handle_keyup(e, field) {
    const value = e.target.value
    this[field] = value
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

    _uuid: {type: String, state:true},
    _subs_status: { type: Boolean, state: true},
    _exec_code: {type: String, state:true},

    _issending: { type: Boolean, state: true},
    _btnsend: { type: String, state: true},
    _btncancel: { type: String, state: true},
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
        <div class="flex-row">
          <div class="form-group col-4">
            <label for="exec_code">${this.texts.f00}</label>
            <div id="field-exec_code">
              <input type="text" id="exec_code" .value=${this._exec_code} required class="form-control" maxlength="15">
            </div>
          </div>
        </div>
        <div class="flex-row">
          <div class="form-group col-8">
            <label for="notes">${this.texts.f01}</label>
            <div id="field-notes">
              <textarea id="notes" .value=${this._notes} class="form-control" maxlength="300" rows="3"></textarea>
            </div>
          </div>          
        </div>

        <div class="form-group">
          <button id="btn-submit" ?disabled=${this._issending} class="btn btn-primary mt-3 mb-0">
            ${this._btnsend}
            ${
              this._issending
                ? html`<img src="/assets/images/common/loading.png" width="25" height="25"/>`
                : null
            }
          </button>
          <button type="button" ?disabled=${this._issending} @click=${this._on_cancel} class="btn btn-secondary mt-3 mb-0">
            ${this._btncancel}
            ${
              this._issending
                ? html`<img src="/assets/images/common/loading.png" width="25" height="25"/>`
                : null
            }
          </button>
        </div>
      </form>
    `
  }

  //5
  firstUpdated() {
    try {
      this._$get("exec_code").focus()
    }
    catch (e) {
      console.log(e)
    }
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
    
    const response = await injson.put(URL_UPDATE.replace(":uuid", this.fields.uuid), {
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
    this._load_response(response.result)
    window.snack.set_time(4)
        .set_color(SNACK.SUCCESS)
        .set_inner(this.texts.tr04)
        .show()

  }//on_submit

}//FormEdit

if (!customElements.get("form-subscription-update"))
  customElements.define("form-subscription-update", FormSubscriptionUpdate)
