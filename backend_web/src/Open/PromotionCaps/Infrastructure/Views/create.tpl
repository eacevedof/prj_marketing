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
  height: 80vh;
  background-repeat: repeat-x;
  background-position: center;
  background-size: auto 100%;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
<?=$bdhelp->get_style_body()?>
}

.wrapper main{
}
.wrapper main section {
  /*
  border: 1px solid red;
   */
  margin: 0;
  padding: 0;

  background-repeat: no-repeat;
  background-position: center;
  background-size: cover;
  <?php
  BH::echo_style("background-color", $promotion["bgcolor"]);
  BH::echo_style("background-image", $promotion["bgimage_lg"]);
  ?>
}

.wrapper footer {

}
.wrapper footer ul {

}

.div-promotion {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%,-50%);
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
}

.div-promotion h1 {
  margin-bottom: 2em;
}


.social-footer {
  padding: 1rem;
  background: #8a8a8a;
  display: -webkit-flex;
  display: -ms-flexbox;
  display: flex;
  -webkit-align-items: center;
  -ms-flex-align: center;
  align-items: center;
  -webkit-justify-content: space-between;
  -ms-flex-pack: justify;
  justify-content: space-between;
}

.social-footer .social-footer-icons ul {
  list-style-type: none;
  margin: 0;
  padding: 0;
  overflow: hidden;
}
.social-footer .social-footer-icons ul li {
  float: left;
}

.social-footer .social-footer-icons li:last-of-type {
  margin-right: 0;
}

.social-footer .social-footer-icons .fa {
  font-size: 1.3rem;
  color: #fefefe;
}

.social-footer .social-footer-icons .fa:hover {
  color: #4a4a4a;
  transition: color 0.3s ease-in;
}
</style>
<div class="wrapper">
  <header>
    <h2><? $this->_echo_nohtml($businessdata["business_name"]) ?></h2>
  </header>
  <main>
    <section>
      <img src="<?$this->_echo($promotion["bgimage_lg"]);?>" style="visibility: hidden; margin: 0; height: 74vh" />
      <div class="div-promotion">
        <h1><? $this->_echo_nohtml($promotion["description"]) ?></h1>
        <?php
        $this->_element_view("promotion-cap-ui-form", [
          "uihelp" => $uihelp,
          "promotionuuid" => $promotion["uuid"],
          "languages" => $languages,
          "countries" => $countries,
          "genders" => $genders,
        ]);
        ?>
      </div>
    </section>
  </main>
  <footer class="social-footer">
    <div class="social-footer-left">
      <a href="https://test.eduardoaf.com">
        <img class="logo" src="https://resources.theframework.es/eduardoaf.com/20200917/161342-favicon.ico">
      </a>
    </div>
    <div class="social-footer-icons">
    <?=$bdhelp->get_footer_links()?>
    </div>
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