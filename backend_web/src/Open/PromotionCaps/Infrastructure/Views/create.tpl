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
$this->_element_view("promotioncap-style-rewrite", ["promotion"=>$promotion,"bdhelp"=>$bdhelp]);
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
<!--/promotincaps.create.tpl-->