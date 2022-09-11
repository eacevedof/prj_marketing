import { html, LitElement, css } from "/assets/js/vendor/lit.dev/lit-bundle.js"
import get_cssrules from "/assets/js/common/cssrules.js"
import injson from "/assets/js/common/req.js"
import error from "/assets/js/common/fielderrors.js"
import { SNACK } from "/assets/js/common/snackbar.js"
import { cssformflex } from "/assets/js/common/formflex-lit-css.js"
import { cssfielderror } from "/assets/js/common/fielderrors-lit-css.js"
import { selector, get_formdata } from "/assets/js/common/shadowroot/shadowroot.js"

const ACTION = "promotion.raffle.update"

export class FormPromotionRaffleUpdate extends LitElement {
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
    return get_formdata(this.shadowRoot)(this.fields)(["uuid", "id", "id_promotion", "id_owner","notoryes","disabled_date"])
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
    url: { type: String },
    promotionuuid: {type:String},
    iseditable: {type:String},

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

  _handle_keyup(e, field) {
    const value = e.target.value
    this[field] = value
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
            <th>${this.texts.tr05}</th><th>${this.texts.tr06}</th>
          </tr>
        </thead>
        <tbody>
          ${this._winners.map(obj => html([`<tr><td>${obj.name}</td><td>${obj.email}</td></tr>`]))}
        </tbody>
      </table>
   
      ${this._disabled_date || ((new Date(this._date_raffle) > new Date()))
        ? null
        : html`
          <div class="form-group">
            <button id="btn-submit" ?disabled=${this._issending} class="btn btn-primary mt-3 mb-0">
              ${this._btnsend}
              ${
                  this._issending
                      ? html`<img src="/assets/images/common/loading.png" width="25" height="25"/>`
                      : null
              }
            </button>
          </div>
        `
    }
    </form>
    `
  }

  //5
  firstUpdated() {
    try {
      this._$get("pos_email").focus()
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

    const response = await injson.put(
      this.url, {
        _action: ACTION,
        _csrf: this.csrf,
        id: this.fields.id,
        uuid: this.fields.uuid,
        id_owner: this.fields.id_owner,

        ...this._get_data()
      }
    )

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

    //this.requestUpdate()
  }//on_submit

}

if (!customElements.get("form-promotion-raffle-update"))
  customElements.define("form-promotion-raffle-update", FormPromotionRaffleUpdate)
