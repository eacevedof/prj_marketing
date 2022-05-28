import {html, LitElement} from "/assets/js/vendor/lit.dev/lit-bundle.js"
import injson from "/assets/js/common/req.js"
import error from "/assets/js/common/fielderrors.js"
import {SNACK} from "/assets/js/common/snackbar.js"
import {cssfielderror} from "/assets/js/common/fielderrors-lit-css.js"
import {selector, get_formdata} from "/assets/js/common/shadowroot/shadowroot.js"
import {get_parameter} from "/assets/js/common/url.js"
import {cssformsubscription} from "/assets/js/open/promotioncap/form-subscription-lit-css.js"
import validator, {PATTERNS} from "/assets/js/common/fields-validator.js"

const IS_TEST_MODE = get_parameter("mode") === "test" ? 1 : 0
const URL_POST = "/open/promotionscap/:promouuid/insert"
const ACTION = "promotioncap.insert"

export class FormPromotionCapInsert extends LitElement {
  static get styles() {
    return [
      cssformsubscription,
      cssfielderror
    ];
  }

  _$get(idsel) { return selector(this.shadowRoot)(idsel) }

  _get_data() {
    return get_formdata(this.shadowRoot)
            (this.fields.inputs.map(input => "input-".concat(input)))([])
  }

  //1
  constructor() {
    super()
    this._issending = false
    this.texts = {}
    this.fields = {}
  }

  get_inputs() {
    return {
      email: {
        label: html`<label for="input-email">${this.texts.email}</label>`,
        input: html`<input type="email" id="input-email" maxlength="100" required>`
      },
      name1: {
        label: html`<label for="input-name1">${this.texts.name1}</label>`,
        input: html`<input type="text" id="input-name1" maxlength="15" required>`
      },
      name2: {
        label: html`<label for="input-name2">${this.texts.name2}</label>`,
        input: html`<input type="text" id="input-name2" maxlength="15" required>`
      },
      country: {
        label: html`<label for="input-country">${this.texts.country}</label>`,
        input: html`
          <select id="input-country" class="form-control" required>
            ${this._countries.map(item => html`
              <option value=${item.key}>${item.value}</option>`)}
          </select>`
      },
      gender: {
        label: html`<label for="input-gender">${this.texts.gender}</label>`,
        input: html`
          <select id="input-gender" class="form-control" required>
            ${this._genders.map(item => html`
              <option value=${item.key}>${item.value}</option>`)}
          </select>`
      },
      language: {
        label: html`<label for="input-language">${this.texts.language}</label>`,
        input: html`
          <select id="input-language" class="form-control" required>
            ${this._languages.map(item => html`
              <option value=${item.key}>${item.value}</option>`)}
          </select>`
      },
      phone1: {
        label: html`<label for="input-phone1">${this.texts.phone1}</label>`,
        input: html`<input type="text" id="input-phone1" maxlength="20" required>`
      },
      birthdate: {
        label: html`<label for="input-birthdate">${this.texts.birthdate}</label>`,
        input: html`<input type="date" id="input-birthdate" required>`
      },
      address: {
        label: html`<label for="input-address">${this.texts.address}</label>`,
        input: html`<input type="text" id="input-address" maxlength="100" required>`
      },
      is_mailing: {
        label: html`<label for="input-is_mailing">
          <input type="checkbox" id="input-is_mailing">
          <span>${this.texts?.is_mailing}</span>
        </label>`,
      },
      is_terms: {
        label: html`<label for="input-is_terms">
          <input type="checkbox" id="input-is_terms" required>
          <span>${this.texts?.is_terms}</span>
        </label>`,
      },
    }
  }

  static properties = {
    csrf: {type: String},
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

    _issending: {type: Boolean, state:true},
    _btnsend: {type: String, state:true},
    _btncancel: {type: String, state:true},

    _languages: {type: Array, state:true},
    _genders: {type: Array, state:true},
    _countries: {type: Array, state:true},
  }

  connectedCallback() {
    super.connectedCallback()
    this._btnsend = this.texts.tr00
    this._btncancel = this.texts.tr02

    for(let p in this.fields) this["_".concat(p)] = this.fields[p]
  }

  _handle_keyup(e, field) {
    const value = e.target.value
    this[field] = value
  }

  render() {
    const inputs = this._inputs.map(field => this.get_inputs()[field])

    return html`
      <form @submit=${this.on_submit} class="form-subscription">
        ${inputs.map(obj => html`
          <div class="flex-row">
            <div class="form-group">
              ${obj?.label}
              ${obj?.input}
            </div>
          </div>
        `)}
<!-- botones -->
        <div class="form-group">
          <button id="btn-submit" ?disabled=${this._issending} class="btn-submit">
            ${this._btnsend}
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

  firstUpdated() {
    try {
      this._$get("input-email").focus()
    }
    catch(e) {
      console.log("input-email no focusable",e)
    }
  }

  snack_error(msg) {
    window.Snackbar.show({
      pos: "top-right",
      backgroundColor: "#ee335e",
      duration: 1500,
      textColor: "white",
      actionText: "Error",
      actionTextColor: "white",
      text: msg,
    })
  }

  snack_success(msg) {
    window.Snackbar.show({
      pos: "top-right",
      backgroundColor: "#22C03D",
      duration: 1500,
      textColor: "white",
      actionText: "Success",
      actionTextColor: "white",
      text: msg,
    })
  }

  get_client_errors(input) {
    validator.init(input)
    validator.add_rules("input-email","empty", data => {
      //return "not gooooood!"
    })
    return validator.get_errors()
  }

  async on_submit(e) {
    e.preventDefault()
    this._issending = true
    this._btnsend = this.texts.tr01

    const input = {
      wrapper: this.shadowRoot.querySelector("form"),
      fields: this.fields.inputs.map(input => `input-${input}`)
    }
    console.log(input)
    error.config(input)
    error.clear()

    let errors = this.get_client_errors(input)
    console.log("client-errors", errors)
    if(errors?.length) {
      this._issending = false
      this._btnsend = this.texts.tr00
      this.snack_error("Check errors 1")
      return error.append(errors)
    }

    const response = await injson.post(
      URL_POST.replace(":promouuid", this.promotionuuid), {
        _action: ACTION,
        _csrf: this.csrf,
        _test_mode: IS_TEST_MODE,
        ...this._get_data()
      })

    this._issending = false
    this._btnsend = this.texts.tr00

    if(response?.errors){
      let errors = response.errors[0]?.fields_validation.map( errfield => ({ ...errfield, field: `input-${errfield.field}`}))
      console.log("server-errors", errors)
      if(errors?.length) {
        this.snack_error("Check errors 2")
        return error.append(errors)
      }
    }

    this.snack_success("Check your email")
  }
}

if (!customElements.get("form-promotion-cap-insert"))
  customElements.define("form-promotion-cap-insert", FormPromotionCapInsert)
