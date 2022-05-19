<?php
/**
 * @var App\Shared\Infrastructure\Views\AppView $this
 * @var array $result
 */
use App\Shared\Infrastructure\Factories\HelperFactory as HF;
use App\Shared\Infrastructure\Helpers\Views\PromotionCap\BusinessDataHelper;
use App\Shared\Infrastructure\Helpers\Views\PromotionCap\PromotionUiHelper;
//dd($result);
$businessdata = $result["businessdata"] ?? [];
$promotionui = $result["promotionui"] ?? [];

$bd = HF::get(BusinessDataHelper::class, $businessdata);
$ui = HF::get(PromotionUiHelper::class, $promotionui)
?>
<style>
.promotion-public {

}

.promotion-public header{
  border: 1px solid green;
  background-image: <?=$businessdata["head_bgimage"]?>;
  background-image: url("<?=$businessdata["head_bgimage"]?>");
}

</style>
<div class="promotion-public">
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