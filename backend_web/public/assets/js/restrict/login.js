const App = {
  data() {
    return {
      email: "eaf@eaf.com",
      password: "eaf@eaf.com",
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
