import set_cookie from "/assets/js/common/cookie.js"
const ID_WRAPPER = "#vue-users-create"
const URL = "/login/access"

const App = {
  data() {
    return {
      email: "eaf@eaf.com",
      password: "1234",
      fullname: "",
      address: "",
      birthdate: "",

      issending: false,
      btnsend: "enviar"
    }
  },

  methods: {
    onSubmit() {
      this.issending = true
      this.btnsend = "Enviando..."

      fetch(URL, {
        method: "post",
        headers: {
          "Accept": "application/json, text/plain, */*",
          "Content-Type": "application/json"
        },
        body: JSON.stringify({
          csrf: document.getElementById("_csrf")?.value ?? "",
          email: this.email,
          password: this.password,
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
              title: "Proceso incompleto",
              html: "No se ha podido procesar esta acción. Por favor vuelve a intentarlo. <br/>"+response.errors[0],
            })
          }
          window.location = "/restrict/users"
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

export default () => Vue.createApp(App).mount(ID_WRAPPER)