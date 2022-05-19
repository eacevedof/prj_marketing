<?php
/**
 * @var App\Shared\Infrastructure\Views\AppView $this
 * @var array $result
 */
use App\Shared\Infrastructure\Factories\HelperFactory as HF;
use App\Shared\Infrastructure\Helpers\Views\PromotionCap\BusinessDataHelper as BH;
use App\Shared\Infrastructure\Helpers\Views\PromotionCap\PromotionUiHelper as PH;
//dd($result);
$businessdata = $result["businessdata"] ?? [];
$promotion = $result["promotion"];
$promotionui = $result["promotionui"] ?? [];

$bd = HF::get(BH::class, $businessdata);
$ui = HF::get(PH::class, $promotionui)
?>
<style>
.wrapper {

}

.wrapper header{
  border: 1px solid green;
  <?=$bd->get_style_header()?>
}
.wrapper main{
  border: 1px solid orange;
  <?=$bd->get_style_body()?>
}

</style>
<div class="wrapper">
  <header>
    <h2><? $this->_echo($businessdata["business_name"], false) ?></h2>
  </header>
  <main>
    <h1><? $this->_echo($promotion["description"], false) ?></h1>
    <section>
    <?php
    if (isset($error)) {
      echo "<p>$error</p>";
      return;
    }

    $this->_element_view("promotion-cap-ui-form", [
      "promotionui" => $promotionui,
      "promotionuuid" => $promotion["uuid"],
      "languages" => $languages,
      "countries" => $countries,
      "genders" => $genders,
    ]);
    ?>
    </section>
  </main>
  <footer>
    enlaces al las redes sociales del prop
  </footer>
</div>