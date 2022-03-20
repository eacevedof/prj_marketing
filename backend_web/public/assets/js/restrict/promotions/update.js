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

export class FormPromotionUpdate extends LitElement {
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
    return get_formdata(this.shadowRoot)(this.fields)(["id","id_owner","uuid","businessowners","notoryes","timezones"])
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
    _uuid: {type: String, state:true},
    _id_owner: {type: String, state:true},
    _id_tz: {type: String, state:true},
    _code_erp: {type: String, state:true},
    _description: {type: String, state:true},
    _slug: {type: String, state:true},
    _date_from: {type: String, state:true},
    _date_to: {type: String, state:true},
    _content: {type: String, state:true},
    _bgcolor: {type: String, state:true},
    _bgimage_xs: {type: String, state:true},
    _bgimage_sm: {type: String, state:true},
    _bgimage_md: {type: String, state:true},
    _bgimage_lg: {type: String, state:true},
    _bgimage_xl: {type: String, state:true},
    _bgimage_xxl: {type: String, state:true},
    _invested: {type: String, state:true},
    _returned: {type: String, state:true},
    _max_confirmed: {type: String, state:true},
    _is_raffleable: {type: Boolean, state:true},
    _is_cumulative: {type: Boolean, state:true},
    _is_published: {type: Boolean, state:true},
    _tags: {type: String, state:true},
    _notes: {type: String, state:true},
    _num_viewed: {type: String, state:true},
    _num_subscribed: {type: String, state:true},
    _num_confirmed: {type: String, state:true},
    _num_executed: {type: String, state:true},

    _businessowners: {type: Array, state:true},
    _notoryes: {type: Array, state:true},
    _timezones: {type: Array, state:true},
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
    console.log("FIELDS:",this.fields)
  }

  //4
  render() {
    return html`
      <form @submit=${this.on_submit}>
        <div class="flex-row">
          <div class="form-group">
            <label>${this.texts.f00}</label>
            <span>${this._id}</span> | 
            <label>${this.texts.f01}</label>
            <span>${this._uuid}</span>
          </div>
        </div>
        
        <div class="flex-row">
          ${this._businessowners.length > 0
              ? html`<div class="form-group">
                <label for="id_owner">${this.texts.f02}</label>
                <div id="field-id_owner">
                  <select id="id_owner" class="form-control">
                  ${this._businessowners.map((item) =>
                    html`<option value=${item.key} ?selected=${item.key===this._id_owner}>${item.value}</option>`
                  )}
                  </select>
                </div>
              </div>`
              : null
          }
          <div class="form-group col-5">
            <label for="description">${this.texts.f05}</label>
            <div id="field-description">
              <input type="text" id="description" .value=${this._description} class="form-control" maxlength="250" required>
            </div>
            <label>${this.texts.f06}: </label><span>${this._slug}</span>
          </div>
          <div class="form-group col-2">
            <label for="code_erp">${this.texts.f04}</label>
            <div id="field-code_erp">
              <input type="text" id="code_erp" .value=${this._code_erp} class="form-control" maxlength="25">
            </div>
          </div>
        </div>

        <div class="flex-row">
          <div class="form-group">
            <label for="id_tz">${this.texts.f03}</label>
            <div id="field-id_tz">
              <select id="id_tz" class="form-control" required>
                ${this._timezones.map((item) =>
                    html`<option value=${item.key} ?selected=${parseInt(item.key)===parseInt(this._id_tz)}>${item.value}</option>`
                )}
              </select>
            </div>
          </div>
          <div class="form-group">
            <label for="date_from">${this.texts.f07}</label>
            <div id="field-date_from">
              <input type="datetime-local" id="date_from" .value=${this._date_from} class="form-control">
            </div>
          </div>
          <div class="form-group">
            <label for="date_to">${this.texts.f08}</label>
            <div id="field-date_to">
              <input type="datetime-local" id="date_to" .value=${this._date_to} class="form-control">
            </div>
          </div>
        </div>

        <div class="flex-row">
          <div class="form-group">
            <label for="bgcolor">${this.texts.f10}</label>
            <div id="field-bgcolor">
              <input type="color" id="bgcolor" .value=${this._bgcolor} class="form-control" maxlength="10">
            </div>
          </div>
          <div class="form-group col-10">
            <label for="bgimage_xs">${this.texts.f11}</label>
            <div id="field-bgimage_xs">
              <input type="text" id="bgimage_xs" .value=${this._bgimage_xs} placeholder="cloudinary.com link" class="form-control" maxlength="500">
            </div>
          </div>
        </div>

        <div class="flex-row">
          <div class="form-group col-5">
            <label for="bgimage_sm">${this.texts.f12}</label>
            <div id="field-bgimage_sm">
              <input type="text" id="bgimage_sm" .value=${this._bgimage_sm} placeholder="cloudinary.com link" class="form-control" maxlength="500">
            </div>
          </div>
          <div class="form-group col-5">
            <label for="bgimage_md">${this.texts.f13}</label>
            <div id="field-bgimage_md">
              <input type="text" id="bgimage_md" .value=${this._bgimage_md} placeholder="cloudinary.com link" class="form-control" maxlength="500">
            </div>
          </div>
        </div>
        <div class="flex-row">
          <div class="form-group col-5">
            <label for="bgimage_lg">${this.texts.f14}</label>
            <div id="field-bgimage_lg">
              <input type="text" id="bgimage_lg" .value=${this._bgimage_lg} placeholder="cloudinary.com link" class="form-control" maxlength="500">
            </div>
          </div>
          <div class="form-group col-5">
            <label for="bgimage_xl">${this.texts.f15}</label>
            <div id="field-bgimage_xl">
              <input type="text" id="bgimage_xl" .value=${this._bgimage_xl} placeholder="cloudinary.com link" class="form-control" maxlength="500">
            </div>
          </div>
        </div>

        <div class="flex-row">
          <div class="form-group col-4">
            <label for="bgimage_xxl">${this.texts.f16}</label>
            <div id="field-bgimage_xxl">
              <input type="text" id="bgimage_xxl" .value=${this._bgimage_xxl} placeholder="cloudinary.com link" class="form-control" maxlength="500">
            </div>
          </div>
          <div class="form-group">
            <label for="max_confirmed">${this.texts.f19}</label>
            <div id="field-max_confirmed">
              <input type="number" id="max_confirmed" .value=${this._max_confirmed} class="form-control" maxlength="10">
            </div>
          </div>

          <div class="form-group">
            <label for="is_raffleable">${this.texts.f20}</label>
            <div id="field-is_raffleable">
              <select id="is_raffleable" class="form-control" required>
                ${this._notoryes.map((item) =>
                    html`<option value=${item.key} ?selected=${item.key===this._is_raffleable}>${item.value}</option>`
                )}
              </select>
            </div>
          </div>

          <div class="form-group">
            <label for="is_cumulative">${this.texts.f21}</label>
            <div id="field-is_cumulative">
              <select id="is_cumulative" class="form-control" required>
                ${this._notoryes.map((item) =>
                    html`<option value=${item.key} ?selected=${item.key===this._is_cumulative}>${item.value}</option>`
                )}
              </select>
            </div>
          </div>
        </div>
        
        <div class="flex-row">
          <div class="form-group">
            <label for="invested">${this.texts.f17}</label>
            <div id="field-invested">
              <input type="number" step="any" id="invested" .value=${this._invested} class="form-control" maxlength="10">
            </div>
          </div>
          <div class="form-group">
            <label for="returned">${this.texts.f18}</label>
            <div id="field-returned">
              <input type="number" step="any" id="returned" .value=${this._returned} class="form-control" maxlength="10">
            </div>
          </div>
          <div class="form-group">
            <label for="is_published">${this.texts.f28}</label>
            <div id="field-is_published">
              <select id="is_published" class="form-control" required>
                ${this._notoryes.map((item) =>
                    html`<option value=${item.key} ?selected=${item.key===this._is_published}>${item.value}</option>`
                )}
              </select>
            </div>
          </div>          
          
        </div>
        
        <div class="flex-row">
          <div class="form-group col-12">
            <label for="content">${this.texts.f09}</label>
            <div id="field-content">
              <textarea type="text" id="content" .value=${this._content} class="form-control" maxlength="2000"></textarea>
            </div>
          </div>
        </div>
        <div class="flex-row">
          <div class="form-group col-12">
            <label for="tags">${this.texts.f22}</label>
            <div id="field-tags">
              <textarea id="tags" .value=${this._tags} class="form-control" maxlength="500"></textarea>
            </div>
          </div>
        </div>
        <div class="flex-row">
          <div class="form-group col-12">
            <label for="notes">${this.texts.f23}</label>
            <div id="field-notes">
              <textarea type="text" id="notes" .value=${this._notes} class="form-control" maxlength="300"></textarea>
            </div>
          </div>
        </div>
        </div><!--/flex-row-->

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
      </form>
    `
  }

  //5
  firstUpdated() {
    try {
      this._$get("description").focus()
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
          id: this.fields.id,
          uuid: this.fields.uuid,
          //to-do esto no puede estar fijo pq cuando se muestra el select y se escoge
          //el owner deberia refrescarse. Tengo que hacer el doble binding en el onchange (quitarlo de la exclusion)
          id_owner: this.fields.id_owner,
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

if (!customElements.get("form-promotion-update"))
  customElements.define("form-promotion-update", FormPromotionUpdate)
