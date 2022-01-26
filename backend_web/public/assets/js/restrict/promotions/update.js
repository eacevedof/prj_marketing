import { html, LitElement, css } from "/assets/js/vendor/lit.dev/lit-bundle.js"
import get_cssrules from "/assets/js/common/cssrules.js"
import injson from "/assets/js/common/req.js"
import error from "/assets/js/common/fielderrors.js"
import { SNACK } from "/assets/js/common/snackbar.js"
import {cssformflex} from "/assets/js/common/formflex-lit-css.js"
import {cssfielderror} from "/assets/js/common/fielderrors-lit-css.js"
import {selector, get_formdata} from "/assets/js/common/shadowroot/shadowroot.js"

const URL_UPDATE = "/restrict/promotions/update"
const ACTION = "promotions.update"

export class FormPromotionEdit extends LitElement {
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
          <div class="form-group">
                    <label for="id">${this.texts.f00}</label>
                    <div id="field-id">
                        <input type="text" id="id" .value=${this._id} class="form-control" maxlength="10">
                    </div>
                </div>
<div class="form-group">
                    <label for="uuid">${this.texts.f01}</label>
                    <div id="field-uuid">
                        <input type="text" id="uuid" .value=${this._uuid} class="form-control" maxlength="50">
                    </div>
                </div>
<div class="form-group">
                    <label for="id_owner">${this.texts.f02}</label>
                    <div id="field-id_owner">
                        <input type="text" id="id_owner" .value=${this._id_owner} class="form-control" maxlength="10">
                    </div>
                </div>
<div class="form-group">
                    <label for="code_erp">${this.texts.f03}</label>
                    <div id="field-code_erp">
                        <input type="text" id="code_erp" .value=${this._code_erp} class="form-control" maxlength="25">
                    </div>
                </div>
<div class="form-group">
                    <label for="description">${this.texts.f04}</label>
                    <div id="field-description">
                        <input type="text" id="description" .value=${this._description} class="form-control" maxlength="250">
                    </div>
                </div>
<div class="form-group">
                    <label for="slug">${this.texts.f05}</label>
                    <div id="field-slug">
                        <input type="text" id="slug" .value=${this._slug} class="form-control" maxlength="250">
                    </div>
                </div>
<div class="form-group">
                    <label for="content">${this.texts.f06}</label>
                    <div id="field-content">
                        <input type="text" id="content" .value=${this._content} class="form-control" maxlength="2000">
                    </div>
                </div>
<div class="form-group">
                    <label for="id_type">${this.texts.f07}</label>
                    <div id="field-id_type">
                        <input type="text" id="id_type" .value=${this._id_type} class="form-control" maxlength="10">
                    </div>
                </div>
<div class="form-group">
                    <label for="date_from">${this.texts.f08}</label>
                    <div id="field-date_from">
                        <input type="text" id="date_from" .value=${this._date_from} class="form-control" maxlength="">
                    </div>
                </div>
<div class="form-group">
                    <label for="date_to">${this.texts.f09}</label>
                    <div id="field-date_to">
                        <input type="text" id="date_to" .value=${this._date_to} class="form-control" maxlength="">
                    </div>
                </div>
<div class="form-group">
                    <label for="url_social">${this.texts.f10}</label>
                    <div id="field-url_social">
                        <input type="text" id="url_social" .value=${this._url_social} class="form-control" maxlength="250">
                    </div>
                </div>
<div class="form-group">
                    <label for="url_design">${this.texts.f11}</label>
                    <div id="field-url_design">
                        <input type="text" id="url_design" .value=${this._url_design} class="form-control" maxlength="250">
                    </div>
                </div>
<div class="form-group">
                    <label for="is_active">${this.texts.f12}</label>
                    <div id="field-is_active">
                        <input type="text" id="is_active" .value=${this._is_active} class="form-control" maxlength="3">
                    </div>
                </div>
<div class="form-group">
                    <label for="invested">${this.texts.f13}</label>
                    <div id="field-invested">
                        <input type="text" id="invested" .value=${this._invested} class="form-control" maxlength="10">
                    </div>
                </div>
<div class="form-group">
                    <label for="returned">${this.texts.f14}</label>
                    <div id="field-returned">
                        <input type="text" id="returned" .value=${this._returned} class="form-control" maxlength="10">
                    </div>
                </div>
<div class="form-group">
                    <label for="notes">${this.texts.f15}</label>
                    <div id="field-notes">
                        <input type="text" id="notes" .value=${this._notes} class="form-control" maxlength="300">
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

if (!customElements.get("form-promotion-edit"))
  customElements.define("form-promotion-edit", FormPromotionEdit)
