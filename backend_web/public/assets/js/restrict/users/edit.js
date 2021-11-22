import set_config, {field_errors, clear_errors} from "/assets/js/common/fielderrors.js"

const ID_WRAPPER = "#vue-users-create"
const URL_POST = "/restrict/users/insert"
const URL_REDIRECT = "/restrict/users"
const ACTION = "users.insert"
let CSRF = ""
let $wrapper = null

let texts = {}

let fields = {
  email: "eaf@eaf.com",
  password: "1234",
  password2: "1234",
  fullname: "",
  address: "",
  birthdate: "",
  phone: "888999777",
}

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

//si esto se ejecuta desde aqui solo se ve bien en la primera llamada
// en las siguientes no carga el form con vue. Por eso es mejor usar export
//Vue.createApp(App).mount(ID_WRAPPER)

export default options => {
  texts = options.texts
  fields = options.fields
  $wrapper = document.querySelector(ID_WRAPPER)
  CSRF = $wrapper.querySelector("#_csrf")?.value ?? ""
  set_config({
    fields: Object.keys(fields),
    wrapper: $wrapper,
  })
  Vue.createApp(App).mount(ID_WRAPPER)
}