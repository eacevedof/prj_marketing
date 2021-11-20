const ID_WRAPPER = "#vue-users-create"
const URL_POST = "/restrict/users/insert"
const URL_REDIRECT = "/restrict/users"
const ACTION = "users.insert"
let CSRF = ""
let $wrapper = null

const fields = {
  email: "eaf@eaf.com",
  password: "1234",
  password2: "1234",
  fullname: "",
  address: "",
  birthdate: "",

  issending: false,
  btnsend: "enviar",
  phone: "x",
}

function clear_errors(){
  const errors = Array.from($wrapper.querySelectorAll(`[approle="field-error"]`))
  errors.forEach($div => $div.parentNode.removeChild($div))
}

function fields_errors(errors) {
  const fieldsid = Object.keys(fields)
  const nonfieldsid = errors.filter(objerr => !fieldsid.includes(objerr.field)).map(objerr => objerr.field)
  
  const fielderrors = []
  fieldsid.forEach(id => {
    fielderrors.push({
      id,
      messages : errors.filter(objerr => objerr.field === id).map(objerr => objerr.message)
    })
  })

  const tpl = `
    <div approle="field-error" class="%css%">
      <ul>%lis%</ul>
    </div>
  `
  fielderrors.forEach(objfield => {
    const lis = objfield.messages.map(message => `<li>${message}</li>`).join("")
    const html = tpl.replace("%lis%",lis).replace("%css%","")
    let $input = $wrapper.querySelector(`#${objfield.id}`)
    if ($input) {
      $input.insertAdjacentHTML("afterend", html)
      $input.classList.add("form-error")
      $input.focus()
    }
  })

  const nonerrors = []
  nonfieldsid.forEach(id => {
    nonerrors.push({
      id,
      label: errors.filter(objerr => objerr.field === id).map(objerr => objerr.label).join(""),
      messages : errors.filter(objerr => objerr.field === id).map(objerr => objerr.message)
    })
  })
  console.log(nonerrors)
  nonerrors.forEach(objfield => {
    const lis = objfield.messages.map(message => `<li>${message}</li>`).join("")
    const html = tpl
                  .replace("%lis%",`<li class="li-label">${objfield.label}</li>${lis}`)
                  .replace("%css%"," error-top")
    $wrapper.insertAdjacentHTML("afterbegin", html)
  })
}

const App = {
  data() {
    return fields
  },

  methods: {
    onSubmit() {
      this.issending = true
      this.btnsend = "Enviando..."

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
        clear_errors()
        this.issending = false
        this.btnsend = "Enviar"

        if(response?.errors?.length){
          const errors = response.errors[0]?.fields_validation
          if(errors){
            return fields_errors(errors)
          }
          //vendria errors[0].fields_validation
          return Swal.fire({
            icon: "warning",
            title: "Proceso incompleto",
            html: "No se ha podido procesar esta acción. Por favor vuelve a intentarlo. <br/>"+response.errors[0],
          })
        }
        window.location = URL_REDIRECT
      })
      .catch(error => {
        console.error("catch.error", error)
        Swal.fire({
          icon: "error",
          title: "Vaya! Algo ha ido mal (c)",
          html: "No se ha podido procesar tu mensaje. Por favor inténtalo más tarde. Disculpa las molestias.",
        })
      })
      .finally(()=>{
        this.issending = false
        this.btnsend = "Enviar"
      })

    }//onSubmit

  }//methods
}

//si esto se ejecuta desde aqui solo se ve bien en la primera llamada
// en las siguientes no carga el form con vue. Por eso es mejor usar export
//Vue.createApp(App).mount(ID_WRAPPER)

export default () => {
  $wrapper = document.querySelector(ID_WRAPPER)
  CSRF = $wrapper.querySelector("#_csrf")?.value ?? ""
  Vue.createApp(App).mount(ID_WRAPPER)
}