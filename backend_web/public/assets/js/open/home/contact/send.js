import {html, LitElement} from "/assets/js/vendor/lit.dev/lit-bundle.js"
import injson from "/assets/js/common/req.js"
import error from "/assets/js/common/fielderrors.js"
import {cssfielderror} from "/assets/js/common/fielderrors-lit-css.js"
import {selector, get_formdata} from "/assets/js/common/shadowroot/shadowroot.js"
import {cssformcontact} from "/assets/js/open/home/contact/form-contact-lit-css.js"
import validator, {PATTERNS} from "/assets/js/common/fields-validator.js"

const URL_POST = "/contact/send"
const ACTION = "home.contact.send"

export class FormHomeContactSend extends LitElement {
  static get styles() {
    return [
      cssformcontact,
      cssfielderror
    ];
  }

  _$get(idsel) { return selector(this.shadowRoot)(idsel) }

  _get_data() {
    return get_formdata(this.shadowRoot)(Object.keys(this.get_inputs()))([])
  }

  constructor() {
    super()
    this._issending = false
    this.texts = {}
  }

  get_inputs() {
    return {
      email: {
        input: html`
          <div>
            <label for="email">${this.texts.email}</label>
            <input type="email" id="email" maxlength="35" placeholder="" required value="eaf@eaf.com"/>
          </div>
          `
      },
      name: {
        input: html`
          <div>
            <label for="name">${this.texts.name}</label>
            <input type="text" id="name" maxlength="25" placeholder="" required value="Some Name"/>
          </div>
          `
      },

      subject: {
        input: html`
        <div>
          <label for="subject">${this.texts.subject}</label>
          <input type="text" id="subject" maxlength="50" placeholder="" required value="A little subject"/>
        </div>
        `
      },
      
      message: {
        input: html`
        <div>
          <label for="message">${this.texts.message}</label>
          <textarea type="text" id="message" maxlength="2000" required>
            Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.
          </textarea>
        </div>
        `
      },
    }
  }

  static properties = {
    csrf: {type: String},
    texts: {
      converter: (strjson) => {
        if (strjson) return JSON.parse(strjson)
        return {}
      },
    },
  }

  connectedCallback() {
    super.connectedCallback()
    this._btnsend = this.texts.tr00
    this._btncancel = this.texts.tr02
  }

  render() {
    const inputs = Object.keys(this.get_inputs()).map(field => this.get_inputs()[field])

    return html`
      <form @submit=${this.on_submit} class="form-flex">
        ${inputs.map(obj => obj?.input)}
        
        <!-- botones -->
        <div class="form-buttons">
          <button id="btn-submit" ?disabled=${this._issending}>
            ${this._btnsend}
            ${
                this._issending
                  ? html`<img src="/assets/images/common/loading.png" width="25" height="25"/>`
                  : html``
            }
          </button>
        </div>
        <button type="button" id="button-exit" class="button-exit"><img src="/themes/mypromos/images/icon-close-modal.svg"></button>
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
    let name = this._$get("name").value
    name = name[0].toUpperCase().concat(name.slice(1))
    const email = this._$get("email").value
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
      fields: Object.keys(this.get_inputs())
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
        ...this._get_data()
      })

    this._issending = false
    this._btnsend = this.texts.tr00

    if(response?.errors){
      console.log(response.errors, "errors")
      let errors = response.errors[0]?.fields_validation
      if (!errors) {
        this.snack_error(this.texts.tr04)
        return error.append_top(response.errors[0])
      }

      console.log(errors[0]?.fields_validation, "fields_validation")
      return;
      errors = errors[0]?.fields_validation.map( errfield => ({ ...errfield, field: `${errfield?.field}`}))
      if(errors?.length) {
        this.snack_error(this.texts.tr04)
        return error.append(errors)
      }

    }
    this.on_success()
  }
}

if (!customElements.get("form-home-contact-send"))
  customElements.define("form-home-contact-send", FormHomeContactSend)
