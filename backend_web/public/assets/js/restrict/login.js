const URL = "/login/access"

const App = {
  data() {
    return {
      email: "eaf@eaf.com",
      password: "eaf@eaf.com",
      issending: false,
      btnsend: "enviar"
    }
  },

  methods: {
    onSubmit() {
      const data = {
        email: this.email,
        password: this.password
      }

      const xhr = new XMLHttpRequest()
      xhr.open("POST", URL, true)
      xhr.setRequestHeader("Content-Type", "application/json")
      xhr.send(JSON.stringify(data))

      xhr.onreadystatechange = () => {
        if (xhr.readyState === XMLHttpRequest.DONE){
          console.log("status",xhr.status)
          if (xhr.status === 200) {
            console.log(xhr.responseText)
            Swal.fire({
              icon: 'success',
              title: 'Gracias por contactar conmigo!',
              html: 'En breves momentos recibirás una copia del mensaje en tu email.',
            })
          }
          else {
            console.log("xhr error:", xhr);
            Swal.fire({
              icon: 'warning',
              title: 'Proceso incompleto',
              html: 'No se ha podido procesar tu mensaje. Por favor inténtalo más tarde. Disculpa las molestias. <br/>',
            })
          }
        }

      }//onReady

      xhr.onerror = (err) => {
        console.log("ON ERROR", err)
        Swal.fire({
          icon: 'error',
          title: 'Vaya! Algo ha ido mal (c)',
          html: 'No se ha podido procesar tu mensaje. Por favor inténtalo más tarde. Disculpa las molestias. \n',
        })
      }

    }//onSubmit

  }//methods
}

Vue.createApp(App).mount("#app")
