<?php
/**
 * @var App\Shared\Infrastructure\Views\AppView $this
 * @var array $result
 */
//dd($result);
$businessdata = $result["businessdata"] ?? [];
?>
<style>
.promotion-public {

}

.promotion-public header{
  border: 1px solid green;
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
  <footer>
    enlaces al las redes sociales del prop
  </footer>
</div>