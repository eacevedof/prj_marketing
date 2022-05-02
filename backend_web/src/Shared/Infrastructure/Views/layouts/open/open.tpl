<?php
/**
 * @var \App\Shared\Infrastructure\Views\AppView $this
 * @var string $pagetitle
 */
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="shortcut icon" href="#">
  <title><?=$pagetitle ?? ""?></title>
  <?= $this->_asset_css("vendor/normalize/normalize-8.0.1.min") ?>
  <link href="/themes/valex/assets/css/icons.css" theme="valex" rel="stylesheet">
  <link href="/themes/valex/assets/plugins/materialdesignicons/materialdesignicons.css" theme="valex" rel="stylesheet">
  <link href="/themes/valex/assets/plugins/bootstrap/css/bootstrap.min.css" theme="valex" rel="stylesheet">
  <link href="/themes/valex/assets/css/style.css" theme="valex" rel="stylesheet">
  <?= $this->_asset_css([
      "index",
      "common/modal-raw",
      "common/snackbar",
      "common/fielderrors",
      "common/form-lit"
  ]) ?>
  <!-- js -->
  <script src="/themes/valex/assets/plugins/bootstrap/js/bootstrap.min.js"></script>
  <?= $this->_asset_js([
      "vendor/jquery/jquery-3.6.0"
  ]) ?>
  <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <?= $this->_asset_js_module([
      "index",
      "common/snackbar"
  ]) ?>
</head>
<body class="main-body">
  <div class="page">
<?
$this->_template();
?>
  </div>
</body>
</html>
<!-- open.tpl -->