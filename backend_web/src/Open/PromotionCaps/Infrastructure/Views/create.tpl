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
<?php
$this->_element_view("promotion-cap-style-rewrite", ["promotion"=>$promotion,"bdhelp"=>$bdhelp]);
?>
<main class="main-flex">
  <!-- nav to fixed -->
  <nav class="nav-flex">
    <a href="<? $this->_echo_nohtml($businessdata["url_business"]) ?>" rel="nofollow" target="_blank">
      <img src="<? $this->_echo_nohtml($businessdata["user_logo_1"]) ?>">
    </a>
    <h1><? $this->_echo_nohtml($promotion["description"]) ?></h1>
  </nav>
  <section class="section">
  <?php
  $this->_element_view("promotion-cap-ui-form", [
    "uihelp" => $uihelp,
    "promotionuuid" => $promotion["uuid"],
    "languages" => $languages,
    "countries" => $countries,
    "genders" => $genders,
  ]);
  ?>
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