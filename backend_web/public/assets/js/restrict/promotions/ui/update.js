import { html, LitElement, css } from "/assets/js/vendor/lit.dev/lit-bundle.js"
import get_cssrules from "/assets/js/common/cssrules.js"
import injson from "/assets/js/common/req.js"
import error from "/assets/js/common/fielderrors.js"
import { SNACK } from "/assets/js/common/snackbar.js"
import { cssformflex } from "/assets/js/common/formflex-lit-css.js"
import { cssfielderror } from "/assets/js/common/fielderrors-lit-css.js"
import { selector, get_formdata } from "/assets/js/common/shadowroot/shadowroot.js"

const URL_UPDATE = "/restrict/promotions/:uuid/ui/update"
const ACTION = "promotionuis.update"

export class FormPromotionUiUpdate extends LitElement {
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
    promotionuuid: {type:String},

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

    _id: { type: String, state: true },
    _uuid: { type: String, state: true },
    _id_owner: { type: String, state: true },
    _code_erp: { type: String, state: true },
    _description: { type: String, state: true },
    _id_promotion: { type: String, state: true },
    _input_email: { type: String, state: true },
    _pos_email: { type: String, state: true },
    _input_name1: { type: String, state: true },
    _pos_name1: { type: String, state: true },
    _input_name2: { type: String, state: true },
    _pos_name2: { type: String, state: true },
    _input_language: { type: String, state: true },
    _pos_language: { type: String, state: true },
    _input_country: { type: String, state: true },
    _pos_country: { type: String, state: true },
    _input_phone1: { type: String, state: true },
    _pos_phone1: { type: String, state: true },
    _input_birthdate: { type: String, state: true },
    _pos_birthdate: { type: String, state: true },
    _input_gender: { type: String, state: true },
    _pos_gender: { type: String, state: true },
    _input_address: { type: String, state: true },
    _pos_address: { type: String, state: true },
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
      <div>
        <label for="uuid">${this._uuid}</label>
      </div>
      <table>
        <thead>
          <tr>
            <th>${this.texts.tr05}</th><th>${this.texts.tr06}</th><th>${this.texts.tr07}</th>
          </tr>
        </thead>
        <tbody>
          <!--email-->
          <tr>
            <td>${this.texts.f06}</td>
            <td>
              <select id="input_email" class="form-control" required disabled>
                ${this._notoryes.map((item) =>
                    html`<option value=${item.key} ?selected=${item.key===this._input_email}>${item.value}</option>`
                )}
              </select>    
            </td>
            <td>
              <input type="number" id="pos_email" .value=${this._pos_email} min="1" max="100" class="form-control" maxlength="10">
            </td>
          </tr>
          
          <!--input_name1-->
          <tr>
            <td>${this.texts.f08}</td>
            <td>
              <select id="input_name1" class="form-control" required>
                ${this._notoryes.map((item) =>
                    html`<option value=${item.key} ?selected=${item.key===this._input_name1}>${item.value}</option>`
                )}
              </select>
            </td>
            <td>
              <input type="number" id="pos_name1" .value=${this._pos_name1} min="1" max="100" class="form-control" maxlength="10">
            </td>
          </tr>

          <!--input_phone1-->
          <tr>
            <td>${this.texts.f16}</td>
            <td>
              <select id="input_phone1" class="form-control" required>
                ${this._notoryes.map((item) =>
                    html`<option value=${item.key} ?selected=${item.key===this._input_phone1}>${item.value}</option>`
                )}
              </select>
            </td>
            <td>
              <input type="number" id="pos_phone1" .value=${this._pos_phone1} min="1" max="100" class="form-control" maxlength="10">
            </td>
          </tr>
          
          <!--input_name2-->
          <tr>
            <td>${this.texts.f10}</td>
            <td>
              <select id="input_name2" class="form-control" required>
                ${this._notoryes.map((item) =>
                    html`<option value=${item.key} ?selected=${item.key===this._input_name2}>${item.value}</option>`
                )}
              </select>
            </td>
            <td>
              <input type="number" id="pos_name2" .value=${this._pos_name2} min="1" max="100" class="form-control" maxlength="10">
            </td>
          </tr>
          
          <!--input_language-->
          <tr>
            <td>${this.texts.f12}</td>
            <td>
              <select id="input_language" class="form-control" required>
                ${this._notoryes.map((item) =>
                    html`<option value=${item.key} ?selected=${item.key===this._input_language}>${item.value}</option>`
                )}
              </select>
            </td>
            <td>
              <input type="number" id="pos_language" .value=${this._pos_language} min="1" max="100" class="form-control" maxlength="10">
            </td>
          </tr>

          <!--input_country-->
          <tr>
            <td>${this.texts.f14}</td>
            <td>
              <select id="input_country" class="form-control" required>
                ${this._notoryes.map((item) =>
                    html`<option value=${item.key} ?selected=${item.key===this._input_country}>${item.value}</option>`
                )}
              </select>
            </td>
            <td>
              <input type="number" id="pos_country" .value=${this._pos_country} min="1" max="100" class="form-control" maxlength="10">
            </td>
          </tr>
          
          <!--input_birthdate-->
          <tr>
            <td>${this.texts.f18}</td>
            <td>
              <select id="input_birthdate" class="form-control" required>
                ${this._notoryes.map((item) =>
                    html`<option value=${item.key} ?selected=${item.key===this._input_birthdate}>${item.value}</option>`
                )}
              </select>
            </td>
            <td>
              <input type="number" id="pos_birthdate" .value=${this._pos_birthdate} min="1" max="100" class="form-control" maxlength="10">
            </td>
          </tr>

          <!--input_gender-->
          <tr>
            <td>${this.texts.f20}</td>
            <td>
              <select id="input_gender" class="form-control" required>
                ${this._notoryes.map((item) =>
                    html`<option value=${item.key} ?selected=${item.key===this._input_gender}>${item.value}</option>`
                )}
              </select>
            </td>
            <td>
              <input type="number" id="pos_gender" .value=${this._pos_gender} min="1" max="100" class="form-control" maxlength="10">
            </td>
          </tr>

          <!--input_address-->
          <tr>
            <td>${this.texts.f22}</td>
            <td>
              <select id="input_address" class="form-control" required>
                ${this._notoryes.map((item) =>
                    html`<option value=${item.key} ?selected=${item.key===this._input_address}>${item.value}</option>`
                )}
              </select>
            </td>
            <td>
              <input type="number" id="pos_address" .value=${this._pos_address} min="1" max="100" class="form-control" maxlength="10">
            </td>
          </tr>        
        </tbody>
      </table>
   
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

    if (response?.errors) {
      let errors = response.errors[0]?.fields_validation
      if (errors) {
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

if (!customElements.get("form-promotion-ui-update"))
  customElements.define("form-promotion-ui-update", FormPromotionUiUpdate)
