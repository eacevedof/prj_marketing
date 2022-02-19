import { html, LitElement, css } from "/assets/js/vendor/lit.dev/lit-bundle.js"
import get_cssrules from "/assets/js/common/cssrules.js"
import injson from "/assets/js/common/req.js"
import error from "/assets/js/common/fielderrors.js"
import { SNACK } from "/assets/js/common/snackbar.js"
import {cssformflex} from "/assets/js/common/formflex-lit-css.js"
import {cssfielderror} from "/assets/js/common/fielderrors-lit-css.js"
import {selector, get_formdata} from "/assets/js/common/shadowroot/shadowroot.js"

const URL_UPDATE = "/restrict/users/:uuid/business-data/update"
const ACTION = "businessdata.update"

export class FormUserBusinessDataUpdate extends LitElement {
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
    _site: {type: String, state:true},
    _url_social_fb: {type: String, state:true},
    _url_social_ig: {type: String, state:true},
    _url_social_twitter: {type: String, state:true},
    _url_social_tiktok: {type: String, state:true},
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
      ${this._id
          ? html`
          <div class="flex-row">
            <div class="form-group">
              <label for="id">${this.texts.f00}</label>
              <div id="field-id">${this._id}</div>
            </div>
            <div class="form-group">
              <label for="uuid">${this.texts.f01}</label>
              <div id="field-uuid">${this._uuid}</div>
            </div>
          </div>
          <div class="form-group">
            <label for="slug">${this.texts.f03}</label>
            <div id="field-slug">${this._slug}</div>
          </div>  
          `
          : html``
      }

      <div class="flex-row">
         <div class="form-group">
          <label for="user_logo_1">${this.texts.f04}</label>
          <div id="field-user_logo_1">
            <input type="text" id="user_logo_1" .value=${this._user_logo_1} class="form-control" maxlength="100">
          </div>
        </div>
        <div class="form-group">
          <label for="user_logo_2">${this.texts.f05}</label>
          <div id="field-user_logo_2">
            <input type="text" id="user_logo_2" .value=${this._user_logo_2} class="form-control" maxlength="100">
          </div>
        </div>
        <div class="form-group">
          <label for="user_logo_3">${this.texts.f06}</label>
          <div id="field-user_logo_3">
            <input type="text" id="user_logo_3" .value=${this._user_logo_3} class="form-control" maxlength="100">
          </div>
        </div>
        <div class="form-group">
          <label for="url_favicon">${this.texts.f07}</label>
          <div id="field-url_favicon">
            <input type="text" id="url_favicon" .value=${this._url_favicon} class="form-control" maxlength="100">
          </div>
        </div>        
      </div>

      <div class="flex-row">
        <div class="form-group">
          <label for="head_bgcolor">${this.texts.f08}</label>
          <div id="field-head_bgcolor">
            <input type="color" id="head_bgcolor" .value=${this._head_bgcolor} class="form-control" maxlength="10">
          </div>
        </div>
        <div class="form-group">
          <label for="head_color">${this.texts.f09}</label>
          <div id="field-head_color">
            <input type="color" id="head_color" .value=${this._head_color} class="form-control" maxlength="10">
          </div>
        </div>
        <div class="form-group">
          <label for="head_bgimage">${this.texts.f10}</label>
          <div id="field-head_bgimage">
            <input type="text" id="head_bgimage" .value=${this._head_bgimage} class="form-control" maxlength="10">
          </div>
        </div>        
      </div>

      <div class="flex-row">
        <div class="form-group">
          <label for="body_bgcolor">${this.texts.f11}</label>
          <div id="field-body_bgcolor">
            <input type="color" id="body_bgcolor" .value=${this._body_bgcolor} class="form-control" maxlength="10">
          </div>
        </div>
        <div class="form-group">
          <label for="body_color">${this.texts.f12}</label>
          <div id="field-body_color">
            <input type="color" id="body_color" .value=${this._body_color} class="form-control" maxlength="10">
          </div>
        </div>
        <div class="form-group">
          <label for="body_bgimage">${this.texts.f13}</label>
          <div id="field-body_bgimage">
            <input type="text" id="body_bgimage" .value=${this._body_bgimage} class="form-control" maxlength="100">
          </div>
        </div>  
      </div>
      
      <div class="flex-row">
        <div class="form-group">
          <label for="site">${this.texts.f14}</label>
          <div id="field-site">
            <input type="text" id="site" .value=${this._site} class="form-control" maxlength="100">
          </div>
        </div>
        <div class="form-group">
          <label for="url_social_fb">${this.texts.f15}</label>
          <div id="field-url_social_fb">
            <input type="text" id="url_social_fb" .value=${this._url_social_fb} class="form-control" maxlength="100">
          </div>
        </div>
        <div class="form-group">
          <label for="url_social_ig">${this.texts.f16}</label>
          <div id="field-url_social_ig">
            <input type="text" id="url_social_ig" .value=${this._url_social_ig} class="form-control" maxlength="100">
          </div>
        </div>
        <div class="form-group">
          <label for="url_social_twitter">${this.texts.f17}</label>
          <div id="field-url_social_twitter">
            <input type="text" id="url_social_twitter" .value=${this._url_social_twitter} class="form-control" maxlength="100">
          </div>
        </div>
        <div class="form-group">
          <label for="url_social_tiktok">${this.texts.f18}</label>
          <div id="field-url_social_tiktok">
            <input type="text" id="url_social_tiktok" .value=${this._url_social_tiktok} class="form-control" maxlength="100">
          </div>
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

    if(response?.errors){
      let errors = response.errors[0]?.fields_validation
      if(errors) {
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

if (!customElements.get("form-user-businessdata-update"))
  customElements.define("form-user-businessdata-update", FormUserBusinessDataUpdate)
