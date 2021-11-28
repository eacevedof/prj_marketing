<?php
/**
 * @var \App\Views\AppView $this
 */

?>
<h1><?= __("Login") ?></h1>
<div id="app">
  <form-login />
</div>
<script type="module">
import {html, css, LitElement} from "/assets/js/vendor/lit.dev/lit-bundle.js"
import set_cookie from "/assets/js/common/cookie.js"

const URL = "/login/access"
const URL_ON_ACCESS = "/restrict/users"

export class FormLogin extends LitElement {

  static get properties() {
    return {
      email: {type: String},
      password: {type: String},
      issending: {type: Boolean},
      btnsend: {type: String}
    }
  }

  constructor() {
    super()
    this.email = ""
    this.password = ""
    this.issending = false
    this.btnsend = "Enviar"
  }
  
  $get = sel => this.shadowRoot.querySelector(sel)

  submitForm(e) {
    e.preventDefault();
    this.issending = true
    this.btnsend = "...enviando"

    fetch(URL, {
      method: "post",
      headers: {
        "Accept": "application/json, text/plain, */*",
        "Content-Type": "application/json"
      },
      body: JSON.stringify({
        _csrf: document.getElementById("_csrf")?.value ?? "",
        email: this.$get("#email").value,
        password: this.$get("#password").value,
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
  }//submit

  render() {
    return html`
    <form @submit=${this.submitForm}>
      <div class="form-controls">
        <div>
          <label for="email">Email</label>
          <input type="email" id="email"/>
        </div>
        <div>
          <label for="password">Password</label>
          <input type="password" id="password"/>
        </div>
        <button type="submit">submit</button>
      </div>
    </form>
    `;
  }

}//FormLogin

customElements.define("form-login", FormLogin);
</script>

