//probar lit: https://stackoverflow.com/questions/68614776/using-lit-with-javascript-and-no-build-tools
import set_config, {field_errors, clear_errors} from "/assets/js/common/fielderrors.js"

const ID_WRAPPER = "#vue-users-create"
const URL_POST = "/restrict/users/insert"
const URL_REDIRECT = "/restrict/users"
const ACTION = "users.insert"
let CSRF = ""
let $wrapper = null

let texts = {}

let fields = {}

const App = {
  data() {
    return {
      ...fields,
      issending: false,
      btnsend: texts.tr00,
    }
  },

  methods: {
    onSubmit() {
      this.issending = true
      this.btnsend = texts.tr01
      clear_errors()

      fetch(URL_POST, {
        method: "post",
        headers: {
          "Accept": "application/json, text/plain, */*",
          "Content-Type": "application/json"
        },
        body: JSON.stringify({
          _action: ACTION,
          _csrf: CSRF,
          email: this.email,
          password: this.password,
          password2: this.password2,
          fullname: this.fullname,
          address: this.address,
          birthdate: this.birthdate,
          phone: this.phone,
        })
      })
      .then(response => response.json())
      .then(response => {
        console.log("response",response)
        this.issending = false
        this.btnsend = texts.tr01

        if(response?.errors?.length){
          const errors = response.errors[0]?.fields_validation
          if(errors) {
            return field_errors(errors)
          }
          return Swal.fire({
            icon: "warning",
            title: texts.tr03,
            html: texts.tr04.concat(response.errors[0]),
          })
        }
        window.location = URL_REDIRECT
      })
      .catch(error => {
        Swal.fire({
          icon: "error",
          title: texts.tr05,
          html: texts.tr06,
        })
      })
      .finally(()=>{
        this.issending = false
        this.btnsend = texts.tr02
      })

    }//onSubmit

  }//methods

}// App

export class FormCreate extends LitElement {

  static properties = {
    csrf: {type: String},
    issending: {type: Boolean},
    btnsend: {type: String},
  }

  constructor() {
    super()
    this.email = ""
    this.password = ""
    this.issending = false
    this.btnsend = "Enviar"
  }

  $get = sel => this.shadowRoot.querySelector(sel)

  submitForm(e) {
    e.preventDefault()
    this.issending = true
    this.btnsend = "...enviando"

    fetch(URL, {
      method: "post",
      headers: {
        "Accept": "application/json, text/plain, */*",
        "Content-Type": "application/json"
      },
      body: JSON.stringify({
        _csrf: this.csrf,
        email: this.$get("#email").value,
        password: this.$get("#password").value,
      })
    })
    .then(response => response.json())
    .then(response => {
      this.issending = false
      this.btnsend = "Enviar"

      if(response?.errors?.length){
        console.error(response.errors)
        return Swal.fire({
          icon: "warning",
          title: "Errores",
          html: response.errors.join("<br/>"),
        })
      }

      console.log("reponse ok",response)
      set_cookie("lang", response.data.lang)

      Swal.fire({
        icon: "success",
        title: "Acceso concedido",
        showConfirmButton: false,
        html: "...redirigiendo al panel de control",
      })

      setTimeout(() => window.location = URL_ON_ACCESS, 1000)
    })
    .catch(error => {
      Swal.fire({
        icon: "error",
        title: "Vaya! Algo ha ido mal",
        html: `<b>${error}</b>`,
      })
    })
    .finally(()=>{
      this.issending = false
      this.btnsend = "Enviar"
    })
  }//submit

  render() {
    return html`
    <form @submit.prevent="onSubmit">
      <div>
        <label for="email"><?=__("Email")?> *</label>
        <div id="field-email">
          <input type="email" .value="${this.email}" required="required">
        </div>
      </div>
      <div>
        <label for="password"><?=__("Password")?> *</label>
        <div id="field-password">
          <input type="password" id="password"  v-model="password" required>
        </div>
      </div>
      <div>
        <label for="password2"><?=__("Password confirm")?> *</label>
        <div id="field-password2">
          <input type="password" id="password2" v-model="password2" required>
        </div>
      </div>
      <div>
        <label for="fullname"><?=__("Full name")?> *</label>
        <div id="field-fullname">
          <input type="text" id="fullname" v-model="fullname" required>
        </div>
      </div>
      <div>
        <label for="address"><?=__("Address")?> *</label>
        <div id="field-address">
          <input type="text" id="address" v-model="address">
        </div>
      </div>
      <div>
        <label for="birthdate"><?=__("Birthdate")?> *</label>
        <div id="field-birthdate">
          <input type="date" id="birthdate" v-model="birthdate">
        </div>
      </div>
      <div>
        <button id="btn-submit" :disabled="issending" >
          {{btnsend}}
          <img v-if="issending" src="/assets/images/common/loading.png" width="25" height="25"/>
        </button>
      </div>
    </form>
    `
  }

}//FormCreate
customElements.define("form-create", FormCreate)

export default options => {

}