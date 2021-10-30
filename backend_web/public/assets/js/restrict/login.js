const App = {
  data() {
    return {
      name: "xxx",
      email: "eaf@eaf.com",
      subject: "sss",
      message: "mmmm",
      issending: false,
      btnsend: "enviar"
    }
  },

  mounted() {},

  methods: {
    onSubmit() {
      alert("checking")
    }

  }//methods
}

Vue.createApp(App).mount("#app")
