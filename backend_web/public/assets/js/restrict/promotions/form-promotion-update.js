import { html, LitElement, css } from "/assets/js/vendor/lit.dev/lit-bundle.js"
import get_cssrules from "/assets/js/common/cssrules.js"
import injson from "/assets/js/common/req.js"
import error from "/assets/js/common/fielderrors.js"
import { SNACK } from "/assets/js/common/snackbar.js"
import {cssformflex} from "/assets/js/common/formflex-lit-css.js"
import {cssfielderror} from "/assets/js/common/fielderrors-lit-css.js"
import {csstooltip} from "/assets/js/common/tooltip-lit-css.js"
import {selector, get_formdata} from "/assets/js/common/shadowroot/shadowroot.js"
import {get_link_local, get_img_link} from "/assets/js/common/html/link.js"

const URL_UPDATE = "/restrict/promotions/update"
const ACTION = "promotions.update"

export class FormPromotionUpdate extends LitElement {
  static get styles() {
    const globalStyle = css([get_cssrules([
      "/themes/valex/assets/plugins/bootstrap/css/bootstrap.min.css",
      "/themes/valex/assets/css/style.css",
    ])])
    return [
      globalStyle,
      cssformflex,
      cssfielderror,
      csstooltip
    ];
  }

  _$get(idsel) { return selector(this.shadowRoot)(idsel) }

  _get_data() {
    return get_formdata(this.shadowRoot)(this.fields)([
        "id","id_owner","uuid","businessowners","notoryes","timezones","promotionlink"
    ])
  }

  _on_cancel() {
    window.modalraw.hide()
  }

  _load_response(result) {
    const promotion = result?.promotion

    this._is_published = parseInt(promotion.is_published)
    this._is_launched = parseInt(promotion.is_launched)
    this._disabled_date = promotion.disabled_date!== "" ? promotion.disabled_date : null

    this._slug = promotion.slug
    this._num_viewed = parseInt(promotion.num_viewed)
    this._num_subscribed = parseInt(promotion.num_subscribed)
    this._num_confirmed = parseInt(promotion.num_confirmed)
    this._num_executed = parseInt(promotion.num_executed)

    let td = window.document.getElementById("num-viewed")
    if (td) td.innerText = this._num_viewed

    td = window.document.getElementById("num-subscribed")
    if (td) td.innerText = this._num_subscribed

    td = window.document.getElementById("num-confirmed")
    if (td) td.innerText = this._num_confirmed

    td = window.document.getElementById("num-executed")
    if (td) td.innerText = this._num_executed
  }

  _handle_keyup(e, field) {
    const value = e.target.value
    this[field] = value
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

    _id: {type: String, state:true},
    _uuid: {type: String, state:true},
    _id_owner: {type: String, state:true},
    _id_tz: {type: String, state:true},
    _code_erp: {type: String, state:true},
    _description: {type: String, state:true},
    _slug: {type: String, state:true},
    _date_from: {type: String, state:true},
    _date_to: {type: String, state:true},
    _date_execution: {type: String, state:true},
    _content: {type: String, state:true},
    _bgcolor: {type: String, state:true},
    _bgimage_xs: {type: String, state:true},
    _bgimage_sm: {type: String, state:true},
    _bgimage_md: {type: String, state:true},
    _bgimage_lg: {type: String, state:true},
    _bgimage_xl: {type: String, state:true},
    _bgimage_xxl: {type: String, state:true},
    _invested: {type: String, state:true},
    _returned: {type: String, state:true},
    _max_confirmed: {type: String, state:true},
    _is_raffleable: {type: Boolean, state:true},
    _date_raffle: {type: String, state:true},
    _is_cumulative: {type: Boolean, state:true},
    _is_published: {type: Boolean, state:true},
    _is_launched: {type: Boolean, state:true},
    _tags: {type: String, state:true},
    _notes: {type: String, state:true},
    _num_viewed: {type: String, state:true},
    _num_subscribed: {type: String, state:true},
    _num_confirmed: {type: String, state:true},
    _num_executed: {type: String, state:true},
    _disabled_date: {type: String, state:true},
    _disabled_user: {type: String, state:true},
    _disabled_reason: {type: String, state:true},

    _businessowners: {type: Array, state:true},
    _notoryes: {type: Array, state:true},
    _timezones: {type: Array, state:true},

    _timezone: {type: Array, state:true},
  }

  //3 (aqui siempre hay datos)
  connectedCallback() {
    super.connectedCallback()
    this._issending = false
    this._btnsend = this.texts.tr00
    this._btncancel = this.texts.tr02

    //console.log("this.fields", this.fields)
    for (let p in this.fields) this["_".concat(p)] = this.fields[p]

    this._is_launched = parseInt(this._is_launched)
    this._is_published = parseInt(this._is_published)
    this._is_raffleable = parseInt(this._is_raffleable)
    this._date_raffle = !this._is_raffleable ? null : this._date_raffle
    this._disabled_date = this._disabled_date !== "" ? this._disabled_date : null
    this._num_viewed = parseInt(this._num_viewed)
    this._num_subscribed = parseInt(this._num_subscribed)
    this._num_confirmed = parseInt(this._num_confirmed)
    this._num_executed = parseInt(this._num_executed)

    this._timezone = this._timezones.filter(item => parseInt(item.key)===parseInt(this._id_tz)).map(item => item.value).join("")
  }

  _on_change(e, field) {
    const fields = FormPromotionUpdate.properties
    const ints = Object.keys(fields).filter( key=> fields[key]?.type === Boolean)
    const value = ints.includes(field) ? parseInt(e.target.value) : e.target.value
    this[field] = value
  }

  //4
  render() {
    return html`
      <form @submit=${this.on_submit}>
        <div class="flex-row">
          <div class="form-group">
            <label>${this.texts.f00}</label>
            <span>${this._id}</span> | 
            <label>${this.texts.f01}</label>
            <span>${this._uuid}</span>
            <br/>
            <label>${this.texts.tr05} ${this._timezone}</label>:
            <span>${new Date().toLocaleString("es-ES", { timeZone: this._timezone })}</span>
          </div>
        </div>
<!--row-2-->
        <div class="flex-row">
          ${this._businessowners.length > 0
              ? html`<div class="form-group col-12">
                <label for="id_owner">${this.texts.f02}</label>
                <div id="field-id_owner">
                  <select id="id_owner" class="form-control" 
                          ?disabled=${this._is_launched || this._disabled_date}
                  >
                  ${this._businessowners.map((item) =>
                    html`<option value=${item.key} ?selected=${item.key===this._id_owner}>${item.value}</option>`
                  )}
                  </select>
                </div>
              </div>`
              : null
          }
          <div class="form-group col-12">
            <label for="description">${this.texts.f05}</label>
            <div class="tt-tooltip">
              <span class="tt-span">i</span>
              <p class="tt-tooltiptext">
                Description will not be editable after first publish
              </p>
            </div>
            <div id="field-description">
              <input type="text" id="description" class="form-control" maxlength="250" required
                     .value=${this._description}
                     ?disabled=${this._is_launched || this._disabled_date}
              >
            </div>
            <label>${this.texts.f06}: </label>
            <span>
            ${html([
              get_link_local(this._promotionlink.concat("?mode=test"), this._slug)
            ])}
            </span>
          </div>
        </div>
<!--/row-2-->
        
        <div class="flex-row">
          <div class="form-group">
            <label for="id_tz">${this.texts.f03}</label>
            <div class="tt-tooltip">
              <span class="tt-span">i</span>
              <p class="tt-tooltiptext">
                Read only after first publish
              </p>
            </div>            
            <div id="field-id_tz">
              <select id="id_tz" class="form-control" 
                required 
                ?disabled=${this._is_launched || this._disabled_date}
              >
                ${this._timezones.map((item) =>
                    html`<option value=${item.key} ?selected=${parseInt(item.key)===parseInt(this._id_tz)}>${item.value}</option>`
                )}
              </select>
            </div>
          </div>
          <div class="form-group">
            <label for="date_from">${this.texts.f07}</label>
            <div class="tt-tooltip">
              <span class="tt-span">i</span>
              <p class="tt-tooltiptext">
                Date and time when promotion starts. Read only after first publish
              </p>
            </div>            
            <div id="field-date_from">
              <input type="datetime-local" step="1" id="date_from" class="form-control" 
                     .value=${this._date_from}  
                     ?disabled=${this._num_subscribed || this._disabled_date}
              >
            </div>
          </div>
          <div class="form-group">
            <label for="date_to">${this.texts.f08}</label>
            <div class="tt-tooltip">
              <span class="tt-span">i</span>
              <p class="tt-tooltiptext">
                Date and time when promotion ends. Read only after first publish
              </p>
            </div>
            <div id="field-date_to">
              <input type="datetime-local" step="1" id="date_to" class="form-control" 
                     .value=${this._date_to} 
                     ?disabled=${this._num_subscribed || this._disabled_date}
              >
            </div>
          </div>
          
          <div class="form-group">
            <label for="is_raffleable">${this.texts.f20}</label>
            <div class="tt-tooltip">
              <span class="tt-span">i</span>
              <p class="tt-tooltiptext">
                In case you want subscribers with acquisition to gather points for a future gift raffle
              </p>
            </div>
            <div id="field-is_raffleable">
              <select id="is_raffleable" class="form-control" required 
                      ?disabled=${this._num_subscribed || this._disabled_date} 
                      @change=${e => this._on_change(e, "_is_raffleable")}
              >
                ${this._notoryes.map((item) =>
                    html`<option value=${item.key} ?selected=${parseInt(item.key)===this._is_raffleable}>${item.value}</option>`
                )}
              </select>
            </div>
          </div>

          ${this._is_raffleable ?
            html`
            <div class="form-group">
              <label for="date_raffle">${this.texts.f31}</label>
              <div id="field-date_raffle">
                <input type="datetime-local" step="1" id="date_raffle" class="form-control"
                   .value=${this._date_raffle}
                   ?disabled=${this._num_subscribed || this._disabled_date}
                   @change=${e => this._on_change(e, "_date_raffle")}
                >
              </div>
            </div>`
            : null
          }  
          
          <div class="form-group">
            <label for="date_to">${this.texts.f30}</label>
            <div class="tt-tooltip">
              <span class="tt-span">i</span>
              <p class="tt-tooltiptext">
                Limit time to validate a confirmed subscription (voucher validation). Read only after first publish
              </p>
            </div>
            
            <div id="field-date_execution">
              <input type="datetime-local" step="1" id="date_execution" class="form-control" 
                     .value=${this._date_execution} 
                     ?disabled=${this._num_subscribed || this._disabled_date}
              >
            </div>
          </div> 
          

        </div>

<!-- layout -->
        <div class="flex-row">
          <div class="form-group">
            <label for="bgcolor">${this.texts.f10}</label>
            <div id="field-bgcolor">
              <input type="color" id="bgcolor" .value=${this._bgcolor} 
                     ?disabled=${this._is_published || this._disabled_date} 
                     class="form-control" maxlength="10">
            </div>
          </div>
          <div class="form-group col-10">
            <label for="bgimage_xs">${this.texts.f11}</label>
            <div id="field-bgimage_xs">
              <input type="text" id="bgimage_xs" .value=${this._bgimage_xs}
                     @change=${e => this._handle_keyup(e, "_bgimage_xs")}
                     ?disabled=${this._is_published || this._disabled_date}
                     placeholder="cloudinary.com link" class="form-control" maxlength="500">
            </div>
            ${html([
              get_img_link(this._bgimage_xs)
            ])}
          </div>
        </div>
        
<!--         
        <div class="flex-row">
          <div class="form-group col-5">
            <label for="bgimage_sm">${this.texts.f12}</label>
            <div id="field-bgimage_sm">
              <input type="text" id="bgimage_sm" .value=${this._bgimage_sm}
                     @change=${e => this._handle_keyup(e, "_bgimage_sm")}
                     ?disabled=${this._is_published || this._disabled_date}
                     placeholder="cloudinary.com link" class="form-control" maxlength="500">
            </div>
            ${html([
              get_img_link(this._bgimage_sm)
            ])}         
          </div>
          <div class="form-group col-5">
            <label for="bgimage_md">${this.texts.f13}</label>
            <div id="field-bgimage_md">
              <input type="text" id="bgimage_md" .value=${this._bgimage_md}
                     @change=${e => this._handle_keyup(e, "_bgimage_md")}
                     ?disabled=${this._is_published || this._disabled_date}
                     placeholder="cloudinary.com link" class="form-control" maxlength="500">
            </div>
            ${html([
              get_img_link(this._bgimage_md)
            ])}
          </div>
        </div>
        <div class="flex-row">
          <div class="form-group col-5">
            <label for="bgimage_lg">${this.texts.f14}</label>
            <div id="field-bgimage_lg">
              <input type="text" id="bgimage_lg" .value=${this._bgimage_lg}
                     @change=${e => this._handle_keyup(e, "_bgimage_lg")}
                     ?disabled=${this._is_published || this._disabled_date}
                     placeholder="cloudinary.com link" class="form-control" maxlength="500">
            </div>
            ${html([
              get_img_link(this._bgimage_lg)
            ])}            
          </div>
          <div class="form-group col-5">
            <label for="bgimage_xl">${this.texts.f15}</label>
            <div id="field-bgimage_xl">
              <input type="text" id="bgimage_xl" .value=${this._bgimage_xl} placeholder="cloudinary.com link"
                     @change=${e => this._handle_keyup(e, "_bgimage_xl")}
                     ?disabled=${this._is_published || this._disabled_date}
                     class="form-control" maxlength="500">
            </div>
            ${html([
              get_img_link(this._bgimage_xl)
            ])}            
          </div>
        </div>
-->
        <div class="flex-row">
<!--
          <div class="form-group col-4">
            <label for="bgimage_xxl">${this.texts.f16}</label>
            <div id="field-bgimage_xxl">
              <input type="text" id="bgimage_xxl" .value=${this._bgimage_xxl}
                     @change=${e => this._handle_keyup(e, "_bgimage_xxl")}
                     ?disabled=${this._is_published || this._disabled_date}
                     placeholder="cloudinary.com link" class="form-control" maxlength="500">
            </div>
            ${html([
              get_img_link(this._bgimage_xxl)
            ])}            
          </div>
-->
<!-- /layout -->
          <div class="form-group">
            <label for="max_confirmed">${this.texts.f19}</label>
            <div class="tt-tooltip">
              <span class="tt-span">i</span>
              <p class="tt-tooltiptext">
                Max number of subscribers that have confirmed their subscription. -1 means no limit. 0 disable your promotion.
              </p>
            </div>            
            <div id="field-max_confirmed">
              <input type="number" min="-1" id="max_confirmed" class="form-control" maxlength="10" 
                     .value=${this._max_confirmed} 
                     ?disabled=${this._is_published || this._disabled_date}
              >
            </div>
          </div>

          <div class="form-group">
            <label for="is_cumulative">${this.texts.f21}</label>
            <div class="tt-tooltip">
              <span class="tt-span">i</span>
              <p class="tt-tooltiptext">
                In case you want subscribers with acquisition to gather points for a future confirmed gift
              </p>
            </div>
            <div id="field-is_cumulative">
              <select id="is_cumulative" class="form-control" required ?disabled=${this._num_subscribed || this._disabled_date}>>
                ${this._notoryes.map((item) =>
                    html`<option value=${item.key} ?selected=${item.key===this._is_cumulative}>${item.value}</option>`
                )}
              </select>
            </div>
          </div>          
        </div>          
        
        <div class="flex-row">
          <div class="form-group">
            <label for="invested">${this.texts.f17}</label>
            <div id="field-invested">
              <input type="number" min="0" step="any" id="invested" 
                 class="form-control" maxlength="10"
                 .value=${this._invested} 
                 ?disabled=${this._is_published || this._disabled_date}
              >
            </div>
          </div>
          <div class="form-group">
            <label for="returned">${this.texts.f18}</label>
            <div id="field-returned">
              <input type="number" min="0" step="any" id="returned"
                 class="form-control" maxlength="10"
                 .value=${this._returned} 
                 ?disabled=${this._num_subscribed || this._disabled_date}
              >
            </div>
          </div>
          <div class="form-group">
            <label for="is_published">${this.texts.f28}</label>
            <div class="tt-tooltip">
              <span class="tt-span">i</span>
              <p class="tt-tooltiptext">
                Once this promotion is published some fields will become readonly
              </p>
            </div>
            <div id="field-is_published">
              <select id="is_published" class="form-control" required ?disabled=${this._disabled_date}>
                ${this._notoryes.map((item) =>
                    html`<option value=${item.key} ?selected=${parseInt(item.key)===this._is_published}>${item.value}</option>`
                )}
              </select>
            </div>
          </div>
        </div>
        
        <div class="flex-row">
          <div class="form-group col-12">
            <label for="content">${this.texts.f09}</label>
            <div id="field-content">
              <textarea type="text" id="content" .value=${this._content} class="form-control" maxlength="2000" 
                        ?disabled=${this._is_published || this._num_subscribed || this._disabled_date}></textarea>
            </div>
          </div>
        </div>
        
        <div class="flex-row">
          <div class="form-group col-12">
            <label for="tags">${this.texts.f22}</label>
            <div class="tt-tooltip">
              <span class="tt-span">i</span>
              <p class="tt-tooltiptext">
                Used to group your promotions while searching
              </p>
            </div>
            <div id="field-tags">
              <textarea id="tags" .value=${this._tags} class="form-control" maxlength="500" 
                        ?disabled=${this._disabled_date}></textarea>
            </div>
          </div>
        </div>
        
        <div class="flex-row">
          <div class="form-group col-sm-12">
            <label for="code_erp">${this.texts.f04}</label>
            <div class="tt-tooltip">
              <span class="tt-span">i</span>
              <p class="tt-tooltiptext">
                Optional code for your promotion.
              </p>
            </div>
            <div id="field-code_erp">
              <input type="text" id="code_erp" class="form-control" maxlength="25"
                     .value=${this._code_erp}
                     ?disabled=${this._is_published || this._disabled_date}
              >
            </div>
          </div>  
          
          <div class="form-group col-sm-12">
            <label for="notes">${this.texts.f23}</label>
            <div class="tt-tooltip">
              <span class="tt-span">i</span>
              <p class="tt-tooltiptext">
                User observations about this promotion
              </p>
            </div>
            <div id="field-notes">
              <textarea type="text" id="notes" .value=${this._notes} class="form-control" maxlength="300" 
                        ?disabled=${this._disabled_date}></textarea>
            </div>
          </div>
        </div>

        ${this._disabled_date 
          ? null
          :html`
          <div class="form-group">
            <button id="btn-submit" ?disabled=${this._issending} class="btn btn-primary mt-3 mb-0">
              ${this._btnsend}
              ${
                  this._issending
                    ? html`<img src="/assets/images/common/loading.png" width="25" height="25"/>`
                    : null
              }
            </button>
            <button type="button" ?disabled=${this._issending} @click=${this._on_cancel} class="btn btn-secondary mt-3 mb-0">
              ${this._btncancel}
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
      this._$get("description").focus()
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
        URL_UPDATE.concat(`/${this.fields.uuid}`), {
          _action: ACTION,
          _csrf: this.csrf,
          id: this.fields.id,
          uuid: this.fields.uuid,
          //to-do esto no puede estar fijo pq cuando se muestra el select y se escoge
          //el owner deberia refrescarse. Tengo que hacer el doble binding en el onchange (quitarlo de la exclusion)
          id_owner: this.fields.id_owner,
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
    this._load_response(response.result)
    window.snack.set_time(4)
        .set_color(SNACK.SUCCESS)
        .set_inner(this.texts.tr04)
        .show()

  }

}

if (!customElements.get("form-promotion-update"))
  customElements.define("form-promotion-update", FormPromotionUpdate)
