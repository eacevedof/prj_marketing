<?php
/**
 * @var App\Shared\Infrastructure\Views\AppView $this
 * @var array $result
 */
$businessdata = $result["promotioncap"]["businessdata"] ?? [];
?>
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
  $promotion = $result["promotioncap"]["promotion"];
  $promotionui = $result["promotioncap"]["promotionui"];

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