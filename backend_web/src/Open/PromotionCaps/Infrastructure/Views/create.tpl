<?php
/**
 * @var App\Shared\Infrastructure\Views\AppView $this
 * @var array $result
 */
?>
<body>
  <header></header>
  <main>
    <h1><?= $h1 ?></h1>
    <section>
    <?php
    if (isset($error)) {
      echo "<p>$error</p>";
      return;
    }
    $promotion = $result["promotion"];
    $promotionui = $result["promotionui"];

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
  <footer></footer>
</body>