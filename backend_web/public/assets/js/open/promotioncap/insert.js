import {html, LitElement} from "/assets/js/vendor/lit.dev/lit-bundle.js"
import injson from "/assets/js/common/req.js"
import error from "/assets/js/common/fielderrors.js"
import {cssfielderror} from "/assets/js/common/fielderrors-lit-css.js"
import {selector, get_formdata} from "/assets/js/common/shadowroot/shadowroot.js"
import {get_parameter} from "/assets/js/common/url.js"
import {cssformsubscription} from "/assets/js/open/promotioncap/form-promotion-cap-insert-lit-css.js"
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
        input: html`
          <div class="cell-flex">
            <label for="input-email">${this.texts.email}</label>
            <input type="email" id="input-email" maxlength="100"/>
          </div>
          `
      },
      name1: {
        input: html`
          <div class="cell-flex">
            <label for="input-name1">${this.texts.name1}</label>
            <input type="text" id="input-name1" maxlength="30"/>
          </div>
          `
      },
      name2: {
        input: html`
          <div class="cell-flex">
            <label for="input-name2">${this.texts.name2}</label>
            <input type="text" id="input-name2" maxlength="30"/>
          </div>
          `
      },
      country: {
        input: html`
          <div class="cell-flex">
            <label for="input-country">${this.texts.country}</label>
            <select id="input-country">
              ${this._countries.map(item => html`
              <option value=${item.key}>${item.value}</option>`)}
            </select>
          </div>
        `
      },
      gender: {
        input: html`
          <div class="cell-flex">
            <label for="input-gender">${this.texts.gender}</label>
            <select id="input-gender">
              ${this._genders.map(item => html`
                <option value=${item.key}>${item.value}</option>`)}
            </select>
          </div>
        `
      },
      language: {
        input: html`
          <div class="cell-flex">
            <label for="input-language">${this.texts.language}</label>
            <select id="input-language">
              ${this._languages.map(item => html`
              <option value=${item.key}>${item.value}</option>`)}
            </select>
          </div>
        `
      },
      phone1: {
        input: html`
        <div class="cell-flex">
          <label for="input-phone1">${this.texts.phone1}</label>
          <input type="text" id="input-phone1" maxlength="20"/>
        </div>
        `
      },
      birthdate: {
        input: html`
        <div class="cell-flex">
          <label for="input-birthdate">${this.texts.birthdate}</label>
          <input type="date" id="input-birthdate"/>
        </div>
        `
      },
      address: {
        input: html`
        <div class="cell-flex">
          <label for="input-address">${this.texts.address}</label>
          <input type="text" id="input-address" maxlength="100"/>
        </div>
        `
      },
      is_mailing: {
        input: html`
        <div class="cell-flex cell-chk">
          <label for="input-is_mailing">
            <input type="checkbox" id="input-is_mailing" value="1">
            <span>${this.texts?.is_mailing}</span>
          </label>
        </div>        
        `,
      },
      is_terms: {
        input: html`
        <div class="cell-flex cell-chk">
          <label for="input-is_terms">
            <input type="checkbox" id="input-is_terms" value="1">
            <span>${html([this.texts?.is_terms])}</span>
          </label>
        </div>
        `,
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
      <form @submit=${this.on_submit} class="form-grid">
        ${inputs.map(obj => obj?.input)}
        <!-- botones -->
        <div class="cell-flex cell-btn">
          <button id="btn-submit" ?disabled=${this._issending} class="button button-glow">
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

  form_shake() {
    const $section = window.document.querySelector(".section")
    if (!$section) return;
    $section.classList.add("animation-h-shaking")
    setTimeout(
        () => $section.classList.remove("animation-h-shaking"),
        600
    )
  }

  on_success() {
    const $section = window.document.querySelector(".section")
    let name = this._$get("input-name1").value
    name = name[0].toUpperCase().concat(name.slice(1))
    const email = this._$get("input-email").value
    const message = this.texts.tr30.replace("%name%",name).replace("%email%",email)
    $section.innerHTML = `
    <div class="subscription-message">
      <p>${message}</p>
    </div>`
    this.snack_success("Check your email")
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
      this.form_shake()
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
      let errors = response.errors[0]?.fields_validation
      if (!errors) {
        this.snack_error(this.texts.tr04)
        return error.append_top(response.errors[0])
      }

      errors = errors[0]?.fields_validation.map( errfield => ({ ...errfield, field: `input-${errfield.field}`}))
      if(errors?.length) {
        this.snack_error(this.texts.tr04)
        return error.append(errors)
      }

    }
    this.on_success()
  }
}

if (!customElements.get("form-promotion-cap-insert"))
  customElements.define("form-promotion-cap-insert", FormPromotionCapInsert)
