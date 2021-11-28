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
  submitForm(e) {
    e.preventDefault();
    const form = this.shadowRoot.querySelector("form");
    console.log(e.target, form); // successfully logs <form> element
    window.setTimeout(() => {
      console.log(form); // successfully logs <form> element
      form.reset(); // resets form
    }, 2000);
  }

  render() {
    return html`
    <form @submit=${this.submitForm}>
      <div class="form-controls">
        <div>
          <label for="name">Name</label>
          <input type="text" id="name" name="name" />
        </div>
        <div>
          <label for="address">Address</label>
          <input type="text" id="address" name="address" />
        </div>
        <button type="submit">submit</button>
      </div>
    </form>
    `;
  }

}//FormLogin

customElements.define("form-login", FormLogin);
</script>

