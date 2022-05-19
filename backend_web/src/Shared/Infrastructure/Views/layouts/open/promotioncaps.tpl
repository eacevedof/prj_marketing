<?php
/**
 * @var App\Shared\Infrastructure\Views\AppView $this
 */
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="apple-touch-icon" sizes="57x57" href="/favicon/apple-icon-57x57.png">
  <link rel="apple-touch-icon" sizes="60x60" href="/favicon/apple-icon-60x60.png">
  <link rel="apple-touch-icon" sizes="72x72" href="/favicon/apple-icon-72x72.png">
  <link rel="apple-touch-icon" sizes="76x76" href="/favicon/apple-icon-76x76.png">
  <link rel="apple-touch-icon" sizes="114x114" href="/favicon/apple-icon-114x114.png">
  <link rel="apple-touch-icon" sizes="120x120" href="/favicon/apple-icon-120x120.png">
  <link rel="apple-touch-icon" sizes="144x144" href="/favicon/apple-icon-144x144.png">
  <link rel="apple-touch-icon" sizes="152x152" href="/favicon/apple-icon-152x152.png">
  <link rel="apple-touch-icon" sizes="180x180" href="/favicon/apple-icon-180x180.png">
  <link rel="icon" type="image/png" sizes="192x192" href="/favicon/android-icon-192x192.png">
  <link rel="icon" type="image/png" sizes="32x32" href="/favicon/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="96x96" href="/favicon/favicon-96x96.png">
  <link rel="icon" type="image/png" sizes="16x16" href="/favicon/favicon-16x16.png">
  <title><?=($pagetitle ?? "")?></title>
  <?= $this->_asset_css([
      "vendor/normalize/normalize-8.0.1.min",
      "vendor/snackbar/snackbar.min"
  ])?>
  <!-- js -->
  <?= $this->_asset_js([
      "vendor/jquery/jquery-3.6.0",
      "vendor/snackbar/snackbar.min"
  ]) ?>
  <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="main-body">
<div class="page">
<?php
$this->_template();
?>
</div>
<script>
  Snackbar.show({text: "hola mundo"})
</script>
</body>
</html>
<!-- promotioncaps.tpl -->