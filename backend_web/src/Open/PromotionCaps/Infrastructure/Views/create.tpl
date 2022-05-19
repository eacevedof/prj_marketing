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
.wrapper header{
  border: 1px solid orange;
  <?=$bd->get_style_body()?>
}

</style>
<div class="wrapper">
  <header>
    la cabecera con el logo de la empresa
  </header>
  <main>
    <h1><?= $h1 ?></h1>
    <section>
    <?php
    if (isset($error)) {
      echo "<p>$error</p>";
      return;
    }
    $promotion = $result["promotion"];

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