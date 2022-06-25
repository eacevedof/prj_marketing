<?php
/**
 * @var App\Shared\Infrastructure\Views\AppView $this
 * @var array $result
 */

if (isset($error))
  $this->_element("open/elem-error-exit", ["messages"=>$error]);

use App\Shared\Infrastructure\Factories\HelperFactory as HF;
use App\Shared\Infrastructure\Helpers\Views\PromotionCap\BusinessDataHelper as BH;
use App\Shared\Infrastructure\Helpers\PromotionUiHelper as PH;

//dd($result);
$businessdata = $result["businessdata"] ?? [];
$promotion = $result["promotion"];
$promotionui = $result["promotionui"] ?? [];

$bdhelp = HF::get(BH::class, $businessdata);
$uihelp = HF::get(PH::class, $promotionui)
?>
<!--view:promotincaps.create.tpl-->
<main class="main-flex">
  <!-- nav to fixed -->
  <nav class="nav-flex">
    <a href="#"><img src="./logo.png"></a>
    <h1>Eaf Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s</h1>
  </nav>
  <section class="section">
    <form class="form-grid">
      <div class="message">
        ha ocurrido un error en la red es muy dificil su conexión. Es probable
        que su punto de acceso no tenga ancho de banda suficiente
      </div>
      <div class="cell-flex cell1">
        <label for="email">Email</label>
        <input type="text" id="email" name="email" autofocus>
        <div approle="field-error" class="">
          <ul><li>Empty value is not allowed</li><li>Me gustaria recibir mensajes de promociones y sorteos especiales en mi correo</li></ul>
        </div>
      </div>
      <div class="cell-flex cell2">
        <label for="first-name">First name</label>
        <input type="text" id="first-name" name="first-name">
        <div approle="field-error" class="">
          <ul><li>Empty value is not allowed</li></ul>
        </div>
      </div>
<!-- opcional -->
      <div class="cell-flex cell3">
        <label for="phone">Phone</label>
        <input type="text" id="phone" name="phone">
        <div approle="field-error" class="">
          <ul><li>Empty value is not allowed</li></ul>
        </div>
      </div>
      <div class="cell-flex cell4">
        <label for="last-name">Last name</label>
        <input type="text" id="last-name" name="last-name">
        <div approle="field-error" class="">
          <ul><li>Empty value is not allowed</li></ul>
        </div>
      </div>
      <div class="cell-flex cell5">
        <label for="language">Language</label>
        <select id="language" name="language">
          <option>select one</option>
          <option value="en">English</option>
          <option value="es">Spanish</option>
        </select>
        <div approle="field-error" class="">
          <ul><li>Empty value is not allowed</li></ul>
        </div>
      </div>
      <div class="cell-flex cell6">
        <label for="country">Country</label>
        <select id="country" name="country">
          <option>select one</option>
          <option value="aua">Aruba</option>
          <option value="es">Spain</option>
        </select>
        <div approle="field-error" class="">
          <ul><li>Empty value is not allowed</li></ul>
        </div>
      </div>
      <div class="cell-flex cell7">
        <label for="birthdate">Birthdate</label>
        <input type="date" id="birthdate" name="birthdate" />
      </div>
      <div class="cell-flex cell8">
        <label for="gender">Gender</label>
        <select id="gender" name="gender">
          <option>select one</option>
          <option value="male">Male</option>
          <option value="female">Female</option>
        </select>
        <div approle="field-error" class="">
          <ul><li>Empty value is not allowed</li></ul>
        </div>
      </div>
      <div class="cell-flex cell9">
        <label for="address">Address</label>
        <textarea id="address" name="address"></textarea>
        <div approle="field-error" class="">
          <ul><li>Empty value is not allowed</li><li>Me gustaria recibir mensajes de promociones y sorteos especiales en mi correo</li></ul>
        </div>
      </div>
<!--/opcional -->
      <div class="cell-flex cell-chk">
        <label for="chk-mailing">
          <input type="checkbox" id="chk-mailing" name="chk-mailing" value="1">
          <span>Me gustaria recibir mensajes de promociones y sorteos especiales en mi correo</span>
        </label>
        <div approle="field-error" class="">
          <ul><li>Empty value is not allowed</li></ul>
        </div>
      </div>

      <div class="cell-flex cell-chk">
        <label for="chk-terms">
          <input type="checkbox" id="chk-terms" name="chk-terms" class="fix-chk-size" value="1">
          <span>He leido y acepto los terminos y condiciones <a href="#" target="_blank">generales y relacionadas</a> con esta promoción</span>
        </label>
        <div approle="field-error" class="">
          <ul><li>In order to finish your subscription you have to read and accept terms and conditions</li></ul>
        </div>
      </div>
      <div class="cell-flex cell-btn">
        <button type="button" class="button button-glow">Subscribirme</button>
      </div>

    </form>
  </section>
</main>
<script type="module">
const $btn = document.querySelector(".cell-btn button[type=button]")
$btn.addEventListener("click", () => {
  const $section = document.querySelector(".section")
  $btn.setAttribute("disabled","")
  $section.classList.add("animation-h-shaking")
  setTimeout(() => {
    $section.classList.remove("animation-h-shaking")
    $btn.removeAttribute("disabled")
  }, 600)
})

function has_scrollbar() {
  let elem = document.querySelector("body")
  const r = window.innerHeight < elem.scrollHeight
  return r
}

function center_vertically() {
  const $section = document.querySelector(".section")
  $section.style.position = null
  $section.style.top = null

  if (has_scrollbar()) return
  const $nav = document.querySelector(".nav-flex")
  const bgZone = window.innerHeight - $nav.offsetHeight
  const fromTop = (bgZone - $section.offsetHeight)/2
  $section.style.position = "relative"
  $section.style.top = fromTop.toString().concat("px")
}

window.addEventListener("load", center_vertically)
window.addEventListener("resize", center_vertically)
</script>
<!--/promotincaps.create.tpl-->