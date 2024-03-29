<?php
/**
 * @var App\Shared\Infrastructure\Views\AppView $this
 * @var array $space
 * @var array $result
 */
use App\Shared\Infrastructure\Factories\HelperFactory as HF;
use App\Shared\Infrastructure\Helpers\Views\PromotionCap\BusinessDataHelper as BH;
use App\Shared\Infrastructure\Helpers\PromotionUiHelper as PH;
use App\Shared\Infrastructure\Helpers\RoutesHelper as Routes;

$businessdata = $result["businessdata"] ?? [];
$promotion = $result["promotion"];
$promotionui = $result["promotionui"] ?? [];

$bdhelp = HF::get(BH::class, $businessdata);
$uihelp = HF::get(PH::class, $promotionui);

$this->_includeViewElement("promotioncap-style-rewrite", ["promotion"=>$promotion,"bdhelp"=>$bdhelp]);

$spaceurl = Routes::getUrlByRouteName("business.space", ["businessSlug" =>$businessdata["slug"]]);
?>
<main class="main-flex">
  <!-- nav to fixed -->
  <nav class="nav-flex">
    <a href="<?php $this->_echo($spaceurl) ?>">
      <img src="<?php $this->_echoHtmlEscaped($businessdata["user_logo_1"]) ?>">
    </a>
    <h1><?php $this->_echoHtmlEscaped($promotion["description"]) ?></h1>
  </nav>
  <section class="section">
  <?php
  $this->_includeViewElement("form-promotion-cap-insert", [
    "uihelp" => $uihelp,
    "promotionuuid" => $promotion["uuid"],
    "promotionSlug" => $promotion["slug"],
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
  console.log("window-h",window.innerHeight, "body-h",(elem.scrollHeight,window.innerHeight + 1)< elem.scrollHeight)
  const r = ((window.innerHeight + 1) < elem.scrollHeight)
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