const App = {
  data() {
    return {
      counter: 0
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
