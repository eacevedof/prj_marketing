import {html, LitElement} from "/assets/js/vendor/lit.dev/lit-bundle.js"
import injson from "/assets/js/common/req.js"
import error from "/assets/js/common/fielderrors.js"
import {cssfielderror} from "/assets/js/common/fielderrors-lit-css.js"
import {selector, get_formdata} from "/assets/js/common/shadowroot/shadowroot.js"
import {cssformcontact} from "/assets/js/open/home/form-home-contact-send-lit-css.js"
import validator, {PATTERNS} from "/assets/js/common/fields-validator.js"

const URL_POST = "/contact/send"
const ACTION = "home.contact.send"

export class FormHomeContactSend extends LitElement {
  static get styles() {
    return [
      cssfielderror,
      cssformcontact,
    ];
  }

  _$get(idsel) { return selector(this.shadowRoot)(idsel) }

  _get_data() {
    return get_formdata(this.shadowRoot)(Object.keys(this.get_inputs()))([])
  }

  static properties = {
    csrf: {type: String},
    texts: {
      converter: (strjson) => {
        if (strjson) return JSON.parse(strjson)
        return {}
      },
    },

    _issending: { type: Boolean},
    _btnsend: { type: String},

    _success: {type: String},
    _email: {type: String},
    _name: {type: String},
    _subject: {type: String},
    _message: {type: String},
  }

  constructor() {
    super()
    this.texts = {}
    this._success = ""
  }

  connectedCallback() {
    super.connectedCallback()
    this._btnsend = this.texts.tr00
    this._btncancel = this.texts.tr02

    this._success = ""
    this._email = ""
    this._name = ""
    this._subject = ""
    this._message = ""
  }

  _handle_keyup(e, field) {
    const value = e.target.value
    this[field] = value
  }

  get_inputs() {
    return {
      email: {
        input: html`
          <div class="cell-flex">
            <label for="email">${this.texts.email}</label>
            <input type="email" id="email" .value=${this._email} 
                   @change=${e => this._handle_keyup(e, "_email")}
                   maxlength="35" placeholder="" required
            />
          </div>
          `
      },
      name: {
        input: html`
          <div class="cell-flex">
            <label for="name">${this.texts.name}</label>
            <input type="text" id="name"
                   .value=${this._name}
                   @change=${e => this._handle_keyup(e, "_name")}
                   maxlength="25" placeholder="" required/>
          </div>
          `
      },

      subject: {
        input: html`
        <div class="cell-flex">
          <label for="subject">${this.texts.subject}</label>
          <input type="text" id="subject" 
                 .value=${this._subject}
                 @change=${e => this._handle_keyup(e, "_subject")}
                 maxlength="50" placeholder="" required />
        </div>
        `
      },

      message: {
        input: html`
        <div class="cell-flex">
          <label for="message">${this.texts.message}</label>
          <textarea type="text" id="message"                  
                    .value=${this._message}
                    @change=${e => this._handle_keyup(e, "_message")}
                    maxlength="2000" placeholder="" required
          /></textarea>
        </div>
        `
      },
    }
  }

  render() {
    const inputs = Object.keys(this.get_inputs()).map(field => this.get_inputs()[field])

    return html`
      <form @submit=${this.on_submit} class="form-grid">
        ${
            this._success
                ? html`<div class="success-top">${this._success}</div>`
                : null
        }
        ${inputs.map(obj => obj?.input)}
        <!-- botones -->
        <div class="cell-btn">
          <button id="btn-submit" ?disabled=${this._issending} class="button">
            <span>${this._btnsend}</span>&nbsp;
            ${
                this._issending //|| true
                  ? html`<img src="/assets/images/common/loading-2.png" />`
                  : null
            }
          </button>
        </div>
        
        <button type="button" class="button-exit" @click="${this.close_dialog}">
          <img src="/themes/mypromos/images/icon-close-modal.svg">
        </button>
      </form>
    `
  }

  firstUpdated() {
    try {
      this._$get("email").focus()
    }
    catch(e) {
      console.log("email no focusable",e)
    }
  }

  get_client_errors(input) {
    const texts = this.texts
    validator.reset()
    validator.init(input)
    const fields = input.fields

    if (fields.includes("email"))
    validator.add_rules("email","valid", (value) => {
      const v = value.trim()
      if (!v) return texts.tr10
      if (!v.match(PATTERNS.EMAIL)) return texts.tr11
    })

    if (fields.includes("name"))
    validator.add_rules("name","valid", (value) => {
      const v = value.trim()
      if (!v) return texts.tr10
      if (!v.match(PATTERNS.NAME)) return texts.tr12
    })

    if (fields.includes("subject"))
    validator.add_rules("subject","valid", (value) => {
      const v = value.trim()
      if (!v) return texts.tr10
    })

    if (fields.includes("message"))
    validator.add_rules("message","valid", (value) => {
      const v = value.trim()
      if (!v) return texts.tr10
    })
    return validator.get_errors()
  }

  form_shake() {
    const $form = this.shadowRoot.querySelector(".form-grid")
    if (!$form) return;
    $form.classList.add("animation-shaking-x")
    setTimeout(
        () => $form.classList.remove("animation-shaking-x"),
        600
    )
  }

  on_success(message) {
    this._success = message
    this._email = ""
    this._name = ""
    this._subject = ""
    this._message = ""
    setTimeout(()=> {
        this.close_dialog()
        this._success = ""
      },
        5000)
  }

  close_dialog() {
    const dialog = window.document.querySelector("dialog")
    dialog.close()
  }

  async on_submit(e) {
    e.preventDefault()
    this._issending = true
    this._btnsend = this.texts.tr01

    const input = {
      wrapper: this.shadowRoot.querySelector("form"),
      fields: Object.keys(this.get_inputs())//los ids de los campos
    }

    error.config(input)
    error.clear()

    let errors = this.get_client_errors(input)
    if(errors?.length) {
      this._issending = false
      this._btnsend = this.texts.tr00
      this.form_shake()
      return error.append(errors)
    }

    const response = await injson.post(URL_POST, {
        _action: ACTION,
        _csrf: this.csrf,
        ...this._get_data()
      })

    this._issending = false
    this._btnsend = this.texts.tr00

    if(response?.errors){
      let errors = response.errors[0]?.fields_validation
      //si no es error de campos es un error superior
      if (!errors) {
        this.form_shake()
        return error.append_top(response.errors[0])
      }

      if(errors?.length) {
        this.form_shake()
        //este errors debe llevar nodos field (field-id) y message (el error)
        return error.append(errors)
      }
    }
    this.on_success(response.message)
  }
}

if (!customElements.get("form-home-contact-send"))
  customElements.define("form-home-contact-send", FormHomeContactSend)
