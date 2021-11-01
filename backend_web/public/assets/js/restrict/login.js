const URL = "/login/access"

const App = {
  data() {
    return {
      email: "eaf@eaf.com",
      password: "Abcd3343980",
      issending: false,
      btnsend: "enviar"
    }
  },

  methods: {
    onSubmit() {
      const self = this

      this.issending = true
      this.btnsend = "Enviando..."

      fetch(URL, {
        method: "post",
        headers: {
          "Accept": "application/json, text/plain, */*",
          "Content-Type": "application/json"
        },
        body: JSON.stringify({
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
            icon: 'warning',
            title: 'Proceso incompleto',
            html: 'No se ha podido procesar tu mensaje. Por favor inténtalo más tarde. Disculpa las molestias. <br/>'+response.errors[0],
          })
        }

        console.log("reponse ok",response)

        Swal.fire({
          icon: 'success',
          title: 'Gracias por contactar conmigo!',
          html: 'En breves momentos recibirás una copia del mensaje en tu email.',
        })


      })
      .catch(error => {
        console.log("catch.error",error)
        Swal.fire({
          icon: 'error',
          title: 'Vaya! Algo ha ido mal (c)',
          html: 'No se ha podido procesar tu mensaje. Por favor inténtalo más tarde. Disculpa las molestias. \n'+error,
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
