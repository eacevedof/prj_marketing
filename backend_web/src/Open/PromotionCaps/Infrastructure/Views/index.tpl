<?php
/**
 * @var App\Shared\Infrastructure\Views\AppView $this
 * @var array $result
 */
$promotion = $result["promotion"];
$promotionui = $result["promotionui"];
?>
<body>
  <header></header>
  <main>
    <h1><?= $h1 ?></h1>
    <section>
      <?php
      $this->_element_view("promotion-cap-ui-form", [
        "promotionui"=>$promotionui,
        "promotionuuid" => $promotion["uuid"],
        "languages" => $languages,
        "countries" => $countries,
        "genders" => $genders,
      ]);
      ?>
    </section>
  </main>
  <footer></footer>
</body>