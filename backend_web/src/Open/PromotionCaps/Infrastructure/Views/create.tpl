<?php
/**
 * @var App\Shared\Infrastructure\Views\AppView $this
 * @var array $result
 */

if (isset($error))
  $this->_element("open/elem-error-exit", ["messages"=>$error]);

use App\Shared\Infrastructure\Factories\HelperFactory as HF;
use App\Shared\Infrastructure\Helpers\Views\PromotionCap\BusinessDataHelper as BH;
use App\Shared\Infrastructure\Helpers\Views\PromotionCap\PromotionUiHelper as PH;

//dd($result);
$businessdata = $result["businessdata"] ?? [];
$promotion = $result["promotion"];
$promotionui = $result["promotionui"] ?? [];

$bdhelp = HF::get(BH::class, $businessdata);
$uihelp = HF::get(PH::class, $promotionui)
?>
<!--promotincaps.create.tpl-->
<style>
body {
  font-size: 16px;
  font-family: "Roboto", "Helvetica Neue", "Helvetica", "Arial";
  margin: 0;
  padding: 0;
}
/*div wrapper*/
.wrapper {
}
.wrapper header{
  background-repeat: no-repeat;
  background-position: center;
  height: 6em;
  <?=$bdhelp->get_style_header()?>
}
.wrapper header h2 {
  padding: 0;
  margin: 0;
  padding-top: 1em;
  padding-left: 1em;
}

.wrapper main{
  border: 1px solid green;
  margin:0;
  padding:0;
  height: 75vh;
  background-repeat: repeat-x;
  background-position: center;
  background-size: auto 100%;

  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
 /*   opacity: 0.25; esto opaca todo el fondo y lo que hay sobre el*/
<?=$bdhelp->get_style_body()?>
}

.wrapper main h1 {
  height: 50px;
}
.wrapper main section {
  border: 1px solid red;
  margin: 0;
  padding: 2em;

  width: 50vw;

}

section.img-xs {

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
<script type="module">
Snackbar.show({
  pos: "top-right",
  backgroundColor: "yellow",
  duration: 1500,
  textColor: "green",

  text: "hola mundo",
})
</script>
<!--/promotincaps.create.tpl-->