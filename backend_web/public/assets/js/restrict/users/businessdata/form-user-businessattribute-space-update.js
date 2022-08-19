import { html, LitElement, css } from "/assets/js/vendor/lit.dev/lit-bundle.js"
import get_cssrules from "/assets/js/common/cssrules.js"
import injson from "/assets/js/common/req.js"
import error from "/assets/js/common/fielderrors.js"
import { SNACK } from "/assets/js/common/snackbar.js"
import {cssformflex} from "/assets/js/common/formflex-lit-css.js"
import {cssfielderror} from "/assets/js/common/fielderrors-lit-css.js"
import {selector, get_formdata} from "/assets/js/common/shadowroot/shadowroot.js"
import {get_link, get_img_link, get_link_local} from "/assets/js/common/html/link.js"

const ACTION = "businessattributespace.update"

export class FormUserBusinessAttributeSpaceUpdate extends LitElement {
  static get styles() {
    const globalStyle = css([get_cssrules([
      "/themes/valex/assets/plugins/bootstrap/css/bootstrap.min.css",
      "/themes/valex/assets/css/style.css",
      "/assets/css/common/tooltip.css",
    ])])
    return [
      globalStyle,
      cssformflex,
      cssfielderror
    ];
  }

  _get_data() {
    const r = get_formdata(this.shadowRoot)(this._fieldsorder)([])
    console.log(r)
    return r
  }

  _on_cancel() {
    window.modalraw.hide()
  }

  static properties = {
    csrf: {type: String},
    id_user: {type: String},
    url: {type: String},
    spaceurl: {type: String},

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
  }

  //1
  constructor() {
    super()
    this.texts = {}
    this.fields = {}
    this._fieldsorder = [
      "space_about_title",
      "space_about",
      "space_plan_title",
      "space_plan",
      "space_location_title",
      "space_location",
      "space_contact_title",
      "space_contact",
    ]
  }

  //2
  requestUpdate() {super.requestUpdate()}

  //3 (aqui siempre hay datos)
  connectedCallback() {
    super.connectedCallback()
    this._issending = false
    this._btnsend = this.texts.tr00
    this._btncancel = this.texts.tr02

    this._fieldsorder.forEach(name => {
      const objfield = this.fields.filter( obj => obj.attr_key === name)[0] ?? null
      const prop = "_".concat(name)
      if (objfield.attr_key) this[prop] = objfield.attr_value
    })
  }

  _handle_keyup(e, field) {
    const value = e.target.value
    this["_"+field] = value
  }

  //4
  render() {
    return html`
    <form @submit=${this.on_submit}>
      <div class="form-group">
        Businessurl: ${html([get_link_local(this.spaceurl, this.spaceurl)])}
      </div>
      <div class="form-group">
        <label for="space_about_title">About</label>
        <div id="field-space_about">
          <textarea type="text" id="space_about" .value=${this._space_about}
                  @change=${e => this._handle_keyup(e, "_space_about")}
                  class="form-control" maxlength="2000" required></textarea>
        </div>
      </div>
      
      <div class="form-group">
        <label for="space_plan">Points program</label>
        <div id="field-space_plan">
          <textarea type="text" id="space_plan" .value=${this._space_plan}
                    @change=${e => this._handle_keyup(e, "_space_plan")}
                    class="form-control" maxlength="2000" required></textarea>
        </div>
      </div>
      
      <div class="form-group">
        <label for="space_location">Location</label>
        <div id="field-space_location">
          <textarea type="text" id="space_location" .value=${this._space_location}
                    @change=${e => this._handle_keyup(e, "_space_location")}
                    class="form-control" maxlength="2000" required></textarea>
        </div>
      </div>

      <div class="form-group">
        <label for="space_contact">Contact</label>
        <div id="field-space_contact">
          <textarea type="text" id="space_contact" .value=${this._space_contact}
                  @change=${e => this._handle_keyup(e, "_space_contact")}
                  class="form-control" maxlength="2000" required></textarea>
        </div>
      </div>
            
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

  async on_submit(e) {
    e.preventDefault()
    error.config({
      wrapper: this.shadowRoot.querySelector("form"),
      fields: this._fieldsorder,
    })

    this._issending = true
    this._btnsend = this.texts.tr01
    error.clear()

    const response = await injson.put(
        this.url, {
          _action: ACTION,
          _csrf: this.csrf,
          _ajax_html: true,
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

    window.snack.set_time(4)
        .set_color(SNACK.SUCCESS)
        .set_inner(this.texts.tr04)
        .show()

  }
}

if (!customElements.get("form-user-businessattribute-space-update"))
  customElements.define("form-user-businessattribute-space-update", FormUserBusinessAttributeSpaceUpdate)
