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

          }
          else {
            console.log("xhr error:", xhr);

          }
        }

      }//onReady

    }//onSubmit

  }//methods
}

Vue.createApp(App).mount("#app")
