import { html, LitElement, css } from "/assets/js/vendor/lit.dev/lit-bundle.js"
import get_cssrules from "/assets/js/common/cssrules.js"
import injson from "/assets/js/common/req.js"
import error from "/assets/js/common/fielderrors.js"
import { SNACK } from "/assets/js/common/snackbar.js"
import {cssformflex} from "/assets/js/common/formflex-lit-css.js"
import {cssfielderror} from "/assets/js/common/fielderrors-lit-css.js"
import {csstooltip} from "/assets/js/common/tooltip-lit-css.js"
import {selector, get_formdata} from "/assets/js/common/shadowroot/shadowroot.js"

const ACTION = "subscriptions.qr.update.status"

export class FormSubscriptionQRUpdate extends LitElement {
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

  static properties = {
    url: { type: String },
    texts: {
      converter: (strjson) => {
        if (strjson) return JSON.parse(strjson)
        return {}
      },
    },


    //_uuid: {type: String, state:true},
    //esto aplica reactividad a this._execode con lo cual cada vez que se actualize se re-renderiza
    _fullcode: "",
    _exec_code: "",
    _notes: "",
    /*
    _uuid: {type: String, state:true},
    _subs_status: { type: Boolean, state: true},
    _fullcode: {type: String, state:true},

*/
    //_issending: { type: Boolean, state: true},
    //_btnsend: { type: String, state: true},
    //_btncancel: { type: String, state: true},

    //mmm como que no es necesario inventariar estos atributos internos
    //_intervalid: { type: Number, state:true},
    //_barcode: {type: Object, state: true},
  }

  //1
  constructor() {
    super()
    //aqui las propiedades this no estan definidas, no tiene sentido inicializarlas
    console.log("constructor", "this.url:", this.url, "this.texts:", this.texts, "this._intervalid:", this._intervalid, "this._barcode:", this._barcode)

    //no hace falta inicializar estos atributos si no se rscribre: attributeChangedCallback
    //si no se habilita estas variables privadas
    //this.url = ""
    //si no se inicializa el objeto da error connected callback
    //this.texts = {}

    //aqui se puede definir estados iniciales sin contar con properties ya que en este punto no existen
    //si no se inician los atributos que se instancian en el render se mostraran como undefined
    this._fullcode = ""
    this._uuid = ""
    this._exec_code = ""
    this._intervalid = -1
    this._barcode = new BarcodeDetector({ formats: ["qr_code"] })
  }

  _$get(idsel) { return selector(this.shadowRoot)(idsel) }

  _get_data() {return get_formdata(this.shadowRoot)([])([])}
  _on_cancel() {window.modalraw.hide()}
  _load_response(result) {}

  _handle_keyup(e, field) {
    const value = e.target.value
    this[field] = value
  }

  //3 (aqui siempre hay datos)
  connectedCallback() {
    //necesario para la renderizacion si no no carga nada
    super.connectedCallback()
    //console.log("connectedCallback", FormSubscriptionQRUpdate.properties)
    console.log("connectedCallback", "this.url:",this.url,"this.texts:", this.texts,"this._intervalid:", this._intervalid,"this._barcode:", this._barcode)
    this._issending = false
    this._btnsend = this.texts.tr00
    this._btncancel = this.texts.tr02
    this._notes = ""
    //this._barcode = new BarcodeDetector({ formats: ["qr_code"] })
  }

  disconnectedCallback() {
    super.disconnectedCallback()
    console.log("disconnectedCallback","this.url:",this.url,"this.texts:", this.texts,"this._intervalid:", this._intervalid,"this._barcode:", this._barcode)
  }

/*
  //esto setean en blanco los atributos privados
    attributeChangedCallback() {

      super.attributeChangedCallback()
      console.log("attributeChangedCallback","this.url:",this.url,"this.texts:", this.texts,"this._intervalid:", this._intervalid,"this._barcode:", this._barcode)
    }
*/


  _close_camera() {
    this._fullcode = ""
    console.log("_close_camera","this.url:",this.url,"this.texts:", this.texts,"this._intervalid:", this._intervalid,"this._barcode:", this._barcode)
    if (this._intervalid) clearInterval(this._intervalid)
    const $camera = this._$get("camera")
    if (!$camera) return
    const stream = $camera.srcObject
    if (!stream) return
    const tracks = stream.getTracks()
    tracks.forEach(track => track.stop())
    $camera.srcObject = null
    $camera.style.display = "none"
    this._barcode = null
  }

  _load_camera() {
    console.log("barcode",this._barcode)
    if (!this._barcode) return;
    const $camera = this._$get("camera")
    if (!$camera) {
      window.snack.set_time(10)
        .set_inner("Camera device not found!")
        .set_color(SNACK.ERROR)
        .show()
      return
    }

    window.modalraw.on_hide = () => this._close_camera()

    $camera.style.display = "block"

    if (!(navigator.mediaDevices && navigator.mediaDevices.getUserMedia)) {
      window.snack.set_time(10)
        .set_inner("Your browser does not allow media devices")
        .set_color(SNACK.ERROR)
        .show()
      return
    }

    const options = {
      audio: false,
      //el video se cargara con el stream de la camara trasera
      video: {
        facingMode: "environment"
      }
    }

    //genera el stream de datos a partir de la camara
    navigator.mediaDevices.getUserMedia(options).then(stream => $camera.srcObject = stream);

    const detect = () => {
      if (!this._barcode) {
        if(this._intervalid) clearInterval(this._intervalid)
        return
      }

      this._barcode.detect($camera).
        then(codes => {
          console.log("then.codes", codes)
          if (!codes) return

          codes.forEach( objcode => {
            this._fullcode = objcode.rawValue
            const parts = this._fullcode.split("-")
            console.log("parts", parts)
            this._uuid = parts[0] ?? "" //subs-uuid
            this._exec_code = parts[1] ?? ""
            this._$get("notes").focus()
            clearInterval(this._intervalid)
            this._close_camera()
          })
        }).
        catch(err => console.error(err))
    }

    this._intervalid = setInterval(detect, 200)

  }

  //after render
  updated() {
    //console.log("updated", FormSubscriptionQRUpdate.properties)
    console.log("updated","this.url:",this.url,"this.texts:", this.texts,"this._intervalid:", this._intervalid,"this._barcode:", this._barcode)
    this._load_camera()
  }

  //4
  render() {
    //console.log("render", FormSubscriptionQRUpdate.properties)
    console.log("render","this.url:",this.url,"this.texts:", this.texts,"this._intervalid:", this._intervalid,"this._barcode:", this._barcode)
    return html`
      <form @submit=${this.on_submit}>
        <div class="flex-row">
          <div class="form-group col-12 align-content-center">
            <label for="fullcode">${this.texts.f00}</label>
            <div id="field-fullcode">
              <video id="camera" width="300" height="300" autoplay style="display:none"></video>
              <b>QR code: ${this._exec_code}</b>
              <input type="hidden" id="fullcode" .value=${this._fullcode} required class="form-control" maxlength="15">
              <input type="hidden" id="exec_code" .value=${this._exec_code} required class="form-control" maxlength="15">
            </div>
          </div>
        </div>
        
        <div class="flex-row">
          <div class="form-group col-8">
            <label for="notes">${this.texts.f01}</label>
            <div id="field-notes">
              <textarea id="notes" .value=${this._notes} class="form-control" maxlength="300" rows="3"></textarea>
            </div>
          </div>          
        </div>

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
      </form>
    `
  }

  //5
  firstUpdated() {
    //console.log("firstUpdated", FormSubscriptionQRUpdate.properties)
    console.log("firstUpdated","this.url:",this.url,"this.texts:", this.texts,"this._intervalid:", this._intervalid,"this._barcode:", this._barcode)
  }

  async on_submit(e) {
    e.preventDefault()
    error.config({
      wrapper: this.shadowRoot.querySelector("form"),
      fields: ["fullcode"]
    })

    this._issending = true
    this._btnsend = this.texts.tr01
    error.clear()

    const response = await injson.put(this.url, {
      _action: ACTION,
      uuid: this._uuid,
      exec_code: this._exec_code,
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
    window.modalraw.hide()

  }//on_submit

}

if (!customElements.get("form-subscription-qr-update"))
  customElements.define("form-subscription-qr-update", FormSubscriptionQRUpdate)
