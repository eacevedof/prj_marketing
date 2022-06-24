import {html, LitElement} from "/assets/js/vendor/lit.dev/lit-bundle.js"
import injson from "/assets/js/common/req.js"
import error from "/assets/js/common/fielderrors.js"
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
        input: html`<input type="email" id="input-email" maxlength="100" >`
      },
      name1: {
        label: html`<label for="input-name1">${this.texts.name1}</label>`,
        input: html`<input type="text" id="input-name1" maxlength="30" >`
      },
      name2: {
        label: html`<label for="input-name2">${this.texts.name2}</label>`,
        input: html`<input type="text" id="input-name2" maxlength="30" >`
      },
      country: {
        label: html`<label for="input-country">${this.texts.country}</label>`,
        input: html`
          <select id="input-country" class="form-control" >
            ${this._countries.map(item => html`
              <option value=${item.key}>${item.value}</option>`)}
          </select>`
      },
      gender: {
        label: html`<label for="input-gender">${this.texts.gender}</label>`,
        input: html`
          <select id="input-gender" class="form-control" >
            ${this._genders.map(item => html`
              <option value=${item.key}>${item.value}</option>`)}
          </select>`
      },
      language: {
        label: html`<label for="input-language">${this.texts.language}</label>`,
        input: html`
          <select id="input-language" class="form-control" >
            ${this._languages.map(item => html`
              <option value=${item.key}>${item.value}</option>`)}
          </select>`
      },
      phone1: {
        label: html`<label for="input-phone1">${this.texts.phone1}</label>`,
        input: html`<input type="text" id="input-phone1" maxlength="20" >`
      },
      birthdate: {
        label: html`<label for="input-birthdate">${this.texts.birthdate}</label>`,
        input: html`<input type="date" id="input-birthdate" >`
      },
      address: {
        label: html`<label for="input-address">${this.texts.address}</label>`,
        input: html`<input type="text" id="input-address" maxlength="100" >`
      },
      is_mailing: {
        label: html`<label for="input-is_mailing">
          <input type="checkbox" id="input-is_mailing" value="1">
          <span>${this.texts?.is_mailing}</span>
        </label>`,
      },
      is_terms: {
        label: html`<label for="input-is_terms">
          <input type="checkbox" id="input-is_terms" value="1">
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
    const texts = this.texts
    validator.reset()
    validator.init(input)
    const fields = input.fields

    if (fields.includes("input-email"))
    validator.add_rules("input-email","valid", (value) => {
      const v = value.trim()
      if (!v) return texts.tr10
      if (!v.match(PATTERNS.EMAIL)) return texts.tr11
    })
    if (fields.includes("input-name1"))
    validator.add_rules("input-name1","valid", (value) => {
      const v = value.trim()
      if (!v) return texts.tr10
      if (!v.match(PATTERNS.NAME)) return texts.tr12
    })
    if (fields.includes("input-phone1"))
    validator.add_rules("input-phone1","valid", (value) => {
      const v = value.trim()
      if (!v) return texts.tr10
      if (!v.match(PATTERNS.PHONE)) return texts.tr13
    })
    if (fields.includes("input-name2"))
    validator.add_rules("input-name2","valid", (value) => {
      const v = value.trim()
      if (!v) return texts.tr10
      if (!v.match(PATTERNS.NAME)) return texts.tr14
    })
    if (fields.includes("input-language"))
    validator.add_rules("input-language","valid", (value) => {
      const v = value.trim()
      if (!v) return texts.tr15
      if (!v.match(PATTERNS.INTEGER)) return texts.tr16
    })
    if (fields.includes("input-country"))
    validator.add_rules("input-country","valid", (value) => {
      const v = value.trim()
      if (!v) return texts.tr15
      if (!v.match(PATTERNS.INTEGER)) return texts.tr16
    })
    if (fields.includes("input-gender"))
    validator.add_rules("input-gender","valid", (value) => {
      const v = value.trim()
      if (!v) return texts.tr15
      if (!v.match(PATTERNS.GENDER)) return texts.tr16
    })
    if (fields.includes("input-birthdate"))
    validator.add_rules("input-birthdate","valid", (value) => {
      const v = value.trim()
      if (!v) return texts.tr10
      if (!v.match(PATTERNS.DATE)) return texts.tr17
    })
    if (fields.includes("input-address"))
    validator.add_rules("input-address","valid", (value) => {
      const v = value.trim()
      if (!v) return texts.tr10
      if (!v.match(PATTERNS.ADDRESS)) return texts.tr18
    })
    if (fields.includes("input-is_mailing"))
    validator.add_rules("input-is_mailing","valid", (value) => {
      const v = value.trim()
      if (!v) return texts.tr10
      if (!v.match(PATTERNS.ZERO_ONE)) return texts.tr19
    })
    if (fields.includes("input-is_terms"))
    validator.add_rules("input-is_terms","valid", (value) => {
      const v = value.trim()
      if (!v) return texts.tr15
      if (!v.match(PATTERNS.ZERO_ONE)) return texts.tr19
      if (!this._$get("input-is_terms")?.checked)
        return texts.tr20
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
    error.config(input)
    error.clear()

    let errors = this.get_client_errors(input)
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
