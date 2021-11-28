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
import {html, css, LitElement} from "/assets/js/vendor/lit.dev/lit-bundle.js";
export class FormLogin extends LitElement {

  $(sel) {
    return this.shadowRoot.querySelector(sel)
  }

  submitForm(e) {
    e.preventDefault();
    //const email = $("#email")
    const email = this.shadowRoot.querySelector("#email")?.value
    //const password = $("#password")
    const password = this.shadowRoot.querySelector("#password")?.value
    console.log("email:",email,"password:",password)
  }

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

