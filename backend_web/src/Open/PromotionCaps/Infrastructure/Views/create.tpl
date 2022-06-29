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

$businessdata = $result["businessdata"] ?? [];
$promotion = $result["promotion"];
$promotionui = $result["promotionui"] ?? [];

$bdhelp = HF::get(BH::class, $businessdata);
$uihelp = HF::get(PH::class, $promotionui)
?>
<!--view:promotincaps.create.tpl-->
<?php
$this->_element_view("promotioncap-style-rewrite", ["promotion"=>$promotion,"bdhelp"=>$bdhelp]);
?>
<main class="main-flex">
  <!-- nav to fixed -->
  <nav class="nav-flex">
    <a href="<?php $this->_echo_nohtml($businessdata["url_business"]) ?>" rel="nofollow" target="_blank">
      <img src="<?php $this->_echo_nohtml($businessdata["user_logo_1"]) ?>">
    </a>
    <h1><?php $this->_echo_nohtml($promotion["description"]) ?></h1>
  </nav>
  <section class="section">
  <?php
  $this->_element_view("promotioncap-ui-form", [
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
function has_scrollbar() {
  let elem = document.querySelector("body")
  console.log("w-h",window.innerHeight, "bd-h",elem.scrollHeight,window.innerHeight < elem.scrollHeight)
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

window.addEventListener("DOMContentLoaded", center_vertically)
window.addEventListener("resize", center_vertically)
</script>
<!--/promotincaps.create.tpl-->