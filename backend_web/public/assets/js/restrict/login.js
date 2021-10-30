const App = {
  data() {
    return {
      name: "",
      email: "",
      subject: "",
      message: "",
      issending: false,

    }
  },
  mounted() {
    setInterval(() => {
      this.counter++
    }, 1000)
  },

  methods: {
    checkform(e) {
      e.preventDefault()
      alert("checking")
    }

  }//methods
}
Vue.createApp(App).mount("#form-login")
