import { html, LitElement, css } from "/assets/js/vendor/lit.dev/lit-bundle.js"
import get_cssrules from "/assets/js/common/cssrules.js"
import injson from "/assets/js/common/req.js"
import error from "/assets/js/common/fielderrors.js"
import { SNACK } from "/assets/js/common/snackbar.js"
import {cssformflex} from "/assets/js/common/formflex-lit-css.js"
import {cssfielderror} from "/assets/js/common/fielderrors-lit-css.js"
import {selector, get_formdata} from "/assets/js/common/shadowroot/shadowroot.js"
import {get_link, get_img_link} from "/assets/js/common/html/link.js"

const URL_UPDATE = "/restrict/users/:uuid/business-data/update"
const ACTION = "businessdata.update"

export class FormUserBusinessDataUpdate extends LitElement {
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

  _$get(idsel) { return selector(this.shadowRoot)(idsel) }

  _get_data() {
    return get_formdata(this.shadowRoot)(this.fields)(["id","uuid","id_user","timezones"])
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
    useruuid: {type:String},

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

    _id_user: {type: String, state:true},
    _id: {type: String, state:true},
    _uuid: {type: String, state:true},

    _id_tz: {type: String, state:true},
    _business_name: {type: String, state:true},
    _slug: {type: String, state:true},
    _user_logo_1: {type: String, state:true},
    _user_logo_2: {type: String, state:true},
    _user_logo_3: {type: String, state:true},
    _url_favicon: {type: String, state:true},
    _head_bgcolor: {type: String, state:true},
    _head_color: {type: String, state:true},
    _head_bgimage: {type: String, state:true},
    _body_bgcolor: {type: String, state:true},
    _body_color: {type: String, state:true},
    _body_bgimage: {type: String, state:true},
    _url_business: {type: String, state:true},
    _url_social_fb: {type: String, state:true},
    _url_social_ig: {type: String, state:true},
    _url_social_twitter: {type: String, state:true},
    _url_social_tiktok: {type: String, state:true},

    _timezones: {type: String, state:true},
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
    const urlslug = window.location.origin.concat("/account/").concat(this._slug)
    return html`
    <form @submit=${this.on_submit}>
      
      <div class="form-group">
        <label for="business_name">${this.texts.f03}</label>
        ${this._id
            ? html`<div style="color: #0a7ffb"><b>${this._business_name}</b></div>`
            : html`
          <div id="field-business_name">
            <input type="text" id="business_name" .value=${this._business_name} placeholder="${this.texts.tr05}"
                   class="form-control" maxlength="250" required>
          </div>`
        }
      </div>      
      ${this._id
          ? html`
          <div class="form-group">
            <label for="slug">${this.texts.f04}</label>
            <div id="field-slug">${this._slug}</div>
            ${html([get_link(urlslug.concat("?mode=test"), this.texts.f50)])}
          </div>  
          `
          : html``
      }
      <div class="form-group">
        <label for="id_tz">${this.texts.f51}</label>
        <div id="field-id_tz">
          <select id="id_tz" class="form-control">
            ${this._timezones.map((item) =>
                html`<option value=${item.key} ?selected=${item.key===this._id_tz}>${item.value}</option>`
            )}
          </select>
        </div>
      </div>      
      <div class="form-group">
        <label for="user_logo_1">${this.texts.f05}</label>
        <div id="field-user_logo_1">
          <input type="text" id="user_logo_1" .value=${this._user_logo_1} 
              @change=${e => this._handle_keyup(e, "_user_logo_1")}
              placeholder="link cloudinary" class="form-control" maxlength="300">
        </div>
        ${html([
          get_img_link(this._user_logo_1, this.texts.f05)
        ])}
      </div>
      <div class="form-group">
        <label for="user_logo_2">${this.texts.f06}</label>
        <div id="field-user_logo_2">
          <input type="text" id="user_logo_2" .value=${this._user_logo_2}
               @change=${e => this._handle_keyup(e, "_user_logo_2")}
               placeholder="link cloudinary" class="form-control" maxlength="300">
        </div>
        ${html([
          get_img_link(this._user_logo_2, this.texts.f06)
        ])}
      </div>
      <div class="form-group">
        <label for="user_logo_3">${this.texts.f07}</label>
        <div id="field-user_logo_3">
          <input type="text" id="user_logo_3" .value=${this._user_logo_3}
                 @change=${e => this._handle_keyup(e, "_user_logo_3")}    
                 placeholder="link cloudinary" class="form-control" maxlength="300">
        </div>
        ${html([
          get_img_link(this._user_logo_3, this.texts.f07)
        ])}        
      </div>
      <div class="form-group">
        <label for="url_favicon">${this.texts.f08}</label>
        <div id="field-url_favicon">
          <input type="text" id="url_favicon" .value=${this._url_favicon}
                 @change=${e => this._handle_keyup(e, "_url_favicon")}
                 placeholder="link cloudinary" class="form-control" maxlength="300">
        </div>
        ${html([
          get_img_link(this._url_favicon, this.texts.f08)
        ])}
      </div>        

      <div class="flex-row">
        <div class="form-group">
          <label for="head_bgcolor">${this.texts.f09}</label>
          <div id="field-head_bgcolor">
            <input type="color" id="head_bgcolor" .value=${this._head_bgcolor} class="form-control" maxlength="10">
          </div>
        </div>
        <div class="form-group">
          <label for="head_color">${this.texts.f10}</label>
          <div id="field-head_color">
            <input type="color" id="head_color" .value=${this._head_color} class="form-control" maxlength="10">
          </div>
        </div>
        <div class="form-group">
          <label for="head_bgimage">${this.texts.f11}</label>
          <div id="field-head_bgimage">
            <input type="text" id="head_bgimage" .value=${this._head_bgimage}
                  @change=${e => this._handle_keyup(e, "_head_bgimage")}
                  class="form-control" maxlength="300">
          </div>
          ${html([
            get_img_link(this._head_bgimage, this.texts.f11)
          ])}
        </div>        
      </div>

      <hr/>
      <div class="flex-row">
        <div class="form-group">
          <label for="body_bgcolor">${this.texts.f12}</label>
          <div id="field-body_bgcolor">
            <input type="color" id="body_bgcolor" .value=${this._body_bgcolor} class="form-control" maxlength="10">
          </div>
        </div>
        <div class="form-group">
          <label for="body_color">${this.texts.f13}</label>
          <div id="field-body_color">
            <input type="color" id="body_color" .value=${this._body_color} class="form-control" maxlength="10">
          </div>
        </div>
        <div class="form-group">
          <label for="body_bgimage">${this.texts.f14}</label>
          <div id="field-body_bgimage">
            <input type="text" id="body_bgimage" .value=${this._body_bgimage}
                   @change=${e => this._handle_keyup(e, "_body_bgimage")}
                   class="form-control" maxlength="300">
          </div>
          ${html([
            get_img_link(this._body_bgimage, this.texts.f14)
          ])}
        </div>  
      </div>

      <hr/>
      <div class="flex-row">
        <div class="form-group">
          <label for="url_business">${this.texts.f15}</label>
          <div id="field-url_business">
            <input type="text" id="url_business" .value=${this._url_business}
                   @change=${e => this._handle_keyup(e, "_url_business")}
                   class="form-control" maxlength="300">
          </div>
          ${html([
            get_link(this._url_business, this.texts.f15)
          ])}
        </div>        
        <div class="form-group">
          <label for="url_social_fb">${this.texts.f16}</label>
          <div id="field-url_social_fb">
            <input type="text" id="url_social_fb" .value=${this._url_social_fb}
                   @change=${e => this._handle_keyup(e, "_url_social_fb")}
                   class="form-control" maxlength="300">
          </div>
          ${html([
            get_link(this._url_social_fb, this.texts.f15)
          ])}          
        </div>
        <div class="form-group">
          <label for="url_social_ig">${this.texts.f17}</label>
          <div id="field-url_social_ig">
            <input type="text" id="url_social_ig" .value=${this._url_social_ig}
                   @change=${e => this._handle_keyup(e, "_url_social_ig")}
                   class="form-control" maxlength="300">
          </div>
          ${html([
            get_link(this._url_social_ig, this.texts.f15)
          ])}
        </div>
        <div class="form-group">
          <label for="url_social_twitter">${this.texts.f18}</label>
          <div id="field-url_social_twitter">
            <input type="text" id="url_social_twitter" .value=${this._url_social_twitter}
                   @change=${e => this._handle_keyup(e, "_url_social_twitter")}
                   class="form-control" maxlength="300">
          </div>
          ${html([
            get_link(this._url_social_twitter, this.texts.f15)
          ])}
        </div>
        <div class="form-group">
          <label for="url_social_tiktok">${this.texts.f19}</label>
          <div id="field-url_social_tiktok">
            <input type="text" id="url_social_tiktok" .value=${this._url_social_tiktok}
                   @change=${e => this._handle_keyup(e, "_url_social_tiktok")}
                   class="form-control" maxlength="300">
          </div>
          ${html([
            get_link(this._url_social_tiktok, this.texts.f15)
          ])}
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

  //5
  firstUpdated() {
    try {
      this._$get("user_logo_1").focus()
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
        URL_UPDATE.replace(":uuid", this.useruuid), {
          _action: ACTION,
          _csrf: this.csrf,

          id: this.fields.id,
          uuid: this.fields.uuid,
          id_user: this.fields.id_user,

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

    if (!this.fields.id) {
      this.fields.id = response.result.id
      this.fields.uuid = response.result.uuid
      this.fields.slug = response.result.slug

      this._id = this.fields.id
      this._uuid = this.fields.uuid
      this._slug = this.fields.slug
    }

    window.snack.set_time(4)
        .set_color(SNACK.SUCCESS)
        .set_inner(this.texts.tr04)
        .show()

  }//on_submit

}//FormEdit

if (!customElements.get("form-user-businessdata-update"))
  customElements.define("form-user-businessdata-update", FormUserBusinessDataUpdate)
