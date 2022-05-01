<?php
/**
 * @var \App\Shared\Infrastructure\Views\AppView $this
 * @var array $business
 */
$promotion = $business["promotion"];
$promotionui = $business["promotionui"];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pagetitle ?></title>
</head>
<body>
  <header></header>
  <main>
    <h1><?= $h1 ?></h1>
    <section>
      <?php
      $this->_element_view("inputs", ["promotionui"=>$promotionui]);
      ?>
    </section>
  </main>
  <footer></footer>
</body>
</html>