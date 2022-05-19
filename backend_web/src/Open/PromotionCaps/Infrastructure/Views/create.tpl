<?php
/**
 * @var App\Shared\Infrastructure\Views\AppView $this
 * @var array $result
 */
use App\Shared\Infrastructure\Factories\HelperFactory as HF;
use App\Shared\Infrastructure\Helpers\Views\PromotionCap\BusinessDataHelper as BH;
use App\Shared\Infrastructure\Helpers\Views\PromotionCap\PromotionUiHelper as PH;

$businessdata = $result["businessdata"] ?? [];
$promotion = $result["promotion"];
$promotionui = $result["promotionui"] ?? [];

$bdhelp = HF::get(BH::class, $businessdata);
$uihelp = HF::get(PH::class, $promotionui)
?>
<style>
body {
  font-size: 15px;
  font-family: "Roboto", "Helvetica Neue", "Helvetica", "Arial";
}
.wrapper {
  min-height: 100%;
  border: 1px solid black;
}
.wrapper header{
  border: 1px solid green;
  <?=$bdhelp->get_style_header()?>
}
.wrapper main{
  border: 1px solid orange;
  <?=$bdhelp->get_style_body()?>
}
.wrapper main h1 {

}
.wrapper main section {

}
.wrapper footer {

}
.wrapper footer ul {

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
      "uihelp" => $uihelp,
      "promotionuuid" => $promotion["uuid"],
      "languages" => $languages,
      "countries" => $countries,
      "genders" => $genders,
    ]);
    ?>
    </section>
  </main>
  <footer>
    <?=$bdhelp->get_footer_links()?>
  </footer>
</div>