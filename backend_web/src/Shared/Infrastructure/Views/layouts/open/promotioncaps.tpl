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
  <link rel="icon" href="/themes/mypromos/images/provider-xxx-logo-orange.svg"/>
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
<body>
<!-- promotioncaps.tpl -->
<?php
$this->_element("open/elem-promotioncaps-style");
$this->_template();
?>
</body>
</html>
<!-- /promotioncaps.tpl -->