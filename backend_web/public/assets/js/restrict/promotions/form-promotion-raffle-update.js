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

  //1
  constructor() {
    super()
    this.texts = {}
    this.fields = {}
  }

  static properties = {
    csrf: { type: String },
    url: { type: String },

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
    _winners: {type: Array}

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

    //console.log("THIS -",this._date_raffle, this._winners, this._timezone)
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
        <b>${this._promotion}</b><br/><br/>
        <b>${this.texts.tr05} ${this._timezone}</b>:
        <span>${new Date().toLocaleString("es-ES", { timeZone: this._timezone })}</span>
        <br/><br/>
        <b>${this.texts.tr06} ${this._timezone}</b>:
        <span>${new Date(this._date_raffle).toLocaleString("es-ES", { timeZone: this._timezone })}</span>
      </div>
      ${//this._disabled_date || ((new Date(this._date_raffle) > new Date()) || !this._winners)
        this._disabled_date || !this._winners
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
      <hr/>
      <h6>${this.texts.tr07}</h6>
      <table class="">
        <thead>
          <tr>
            <th>${this.texts.f00}</th>
            <th>${this.texts.f01}</th>
            <th>${this.texts.f02}</th>
            <th>${this.texts.f03}</th>
            <th>${this.texts.f04}</th>
            <th>${this.texts.f05}</th>            
          </tr>
        </thead>
        <tbody>
          ${this._winners.map(obj => html([
            `<tr>
                <td>${obj.id}</td>
                <td>${obj.uuid}</td>
                <td>${obj.name1}</td>
                <td>${obj.email}</td>
                <td>${obj.phone1}</td>
                <td>xx</td>
            </tr>`
          ]))}
        </tbody>
      </table>
    </form>
    `
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

    this._winners = response.result?.winners ?? []

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
