import set_cookie from "../common/cookie.js"

const URL = "/login/access"
const URL_ON_ACCESS = "/restrict/users"

const App = {
  data() {
    return {
      email: "",
      password: " ",
      issending: false,
      btnsend: "Enviar"
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
          _csrf: document.getElementById("_csrf")?.value ?? "",
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

    }//onSubmit

  }//methods
}

Vue.createApp(App).mount("#app")
