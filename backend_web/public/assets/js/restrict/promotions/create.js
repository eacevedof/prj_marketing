import {html, LitElement, css} from "/assets/js/vendor/lit.dev/lit-bundle.js"
import get_cssrules from "/assets/js/common/cssrules.js"
import injson from "/assets/js/common/req.js"
import error from "/assets/js/common/fielderrors.js"
import {SNACK} from "/assets/js/common/snackbar.js"
import {cssformflex} from "/assets/js/common/formflex-lit-css.js"
import {cssfielderror} from "/assets/js/common/fielderrors-lit-css.js"
import {selector, get_formdata} from "/assets/js/common/shadowroot/shadowroot.js"

const URL_POST = "/restrict/promotions/insert"
const ACTION = "promotions.insert"

export class FormPromotionCreate extends LitElement {
  static get styles() {
    const globalStyle = css([get_cssrules([
      "/themes/valex/assets/css/icons.css",
      "/themes/valex/assets/plugins/materialdesignicons/materialdesignicons.css",
      "/themes/valex/assets/plugins/bootstrap/css/bootstrap.min.css",
      "/themes/valex/assets/css/style.css",
    ])])
    return [
      globalStyle,
      cssformflex,
      cssfielderror
    ]
  }

  _$get(idsel) { return selector(this.shadowRoot)(idsel) }

  _get_data() {return get_formdata(this.shadowRoot)(this.fields)(["uuid","promotions","businessowners"])}

  _on_cancel() {window.modalraw.hide()}

  //1
  constructor() {
    super()
    this.texts = {}
    this.fields = {}
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

    _issending: {type: Boolean, state:true},
    _btnsend: {type: String, state:true},
    _btncancel: {type: String, state:true},

    _description: {type: String, state:true},
    _code_erp: {type: String, state:true},
    _id_owner: {type: String, state:true},
    _slug: {type: String, state:true},
    _content: {type: String, state:true},
    _id_type: {type: Number, state:true},
    _date_from: {type: String, state:true},
    _date_to: {type: String, state:true},
    _url_social: {type: String, state:true},
    _url_design: {type: String, state:true},
    _is_active: {type: String, state:true},
    _invested: {type: Number, state:true},
    _returned: {type: Number, state:true},
    _notes: {type: String, state:true},

    _promotions: {type: Array, state:true},
    _businessowners: {type: Array, state:true},
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

    for(let p in this.fields) this["_".concat(p)] = this.fields[p]

  }

  //4
  render() {
    return html`
      <form @submit=${this.on_submit}>
        <div class="flex-row">
          ${this._businessowners.length > 0
              ? html`<div class="form-group">
                <label for="id_owner">${this.texts.f02}</label>
                <div id="field-id_owner">
                  <select id="id_owner" class="form-control">
                    ${this._businessowners.map((item) =>
                      html`<option value=${item.key} ?selected=${item.key===this._id_parent}>${item.value}</option>`
                    )}
                  </select>
                </div>
              </div>`
              : html ``
          }
          <div class="form-group">
            <label for="description">${this.texts.f04}</label>
            <div id="field-description">
              <input type="text" id="description" .value=${this._description} class="form-control" maxlength="250">
            </div>
          </div>
          <div class="form-group">
            <label for="code_erp">${this.texts.f03}</label>
            <div id="field-code_erp">
              <input type="text" id="code_erp" .value=${this._code_erp} class="form-control" maxlength="25">
            </div>
          </div>
          <div class="form-group">
            <label for="content">${this.texts.f06}</label>
            <div id="field-content">
              <textarea type="text" id="content" .value=${this._content} class="form-control" maxlength="2000"></textarea>
            </div>
          </div>
          <div class="form-group">
            <label for="id_type">${this.texts.f07}</label>
            <div id="field-id_type">
              <select id="id_type" class="form-control">
                ${this._promotions.map((item) =>
                    html`<option value=${item.key} ?selected=${item.key===this._id_type}>${item.value}</option>`
                )}
              </select>
            </div>
          </div>
          <div class="form-group">
            <label for="date_from">${this.texts.f08}</label>
            <div id="field-date_from">
              <input type="datetime-local" id="date_from" .value=${this._date_from} class="form-control">
            </div>
          </div>
          <div class="form-group">
            <label for="date_to">${this.texts.f09}</label>
            <div id="field-date_to">
              <input type="datetime-local" id="date_to" .value=${this._date_to} class="form-control">
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
              <input type="number" id="is_active" .value=${this._is_active} class="form-control" min="0" max="1" maxlength="3">
            </div>
          </div>
          <div class="form-group">
            <label for="invested">${this.texts.f13}</label>
            <div id="field-invested">
              <input type="number" id="invested" .value=${this._invested} class="form-control" maxlength="10">
            </div>
          </div>
          <div class="form-group">
            <label for="returned">${this.texts.f14}</label>
            <div id="field-returned">
              <input type="number" id="returned" .value=${this._returned} class="form-control" maxlength="10">
            </div>
          </div>
          <div class="form-group">
            <label for="notes">${this.texts.f15}</label>
            <div id="field-notes">
              <textarea type="text" id="notes" .value=${this._notes} class="form-control" maxlength="300"></textarea>
            </div>
          </div>
          
        </div><!--/flex-row-->
        
        <div class="form-group">
          <button id="btn-submit" ?disabled=${this._issending} class="btn btn-primary mt-3 mb-0">
            ${this._btnsend}
            ${
              this._issending
                ? html`<img src="/assets/images/common/loading.png" width="25" height="25"/>`
                : html``
            }
          </button>
          <button type="button" ?disabled=${this._issending} @click=${this.on_cancel} class="btn btn-secondary mt-3 mb-0">
            ${this._btncancel}
            ${
                this._issending
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
    try {
      this._$get("description").focus()
    }
    catch(e) {
      console.log("description no focusable",e)
    }
  }

  //6
  updated(){
    //aqui se deberia de setear la prpiedad despues de una llamada async
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

    const response = await injson.post(
      URL_POST, {
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
    window.modalraw.hide()
    window.snack.set_time(4)
      .set_color(SNACK.SUCCESS)
      .set_inner(this.texts.tr04)
      .show()

  }//on_submit

}//FormCreate

if (!customElements.get("form-promotion-create"))
  customElements.define("form-promotion-create", FormPromotionCreate)
