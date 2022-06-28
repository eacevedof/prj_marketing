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
  <link rel="shortcut icon" href="#">
  <title><?=$pagetitle ?? ""?></title>
  <?= $this->_asset_css("vendor/normalize/normalize-8.0.1.min") ?>
  <!-- js -->
  <?= $this->_asset_js([
      "vendor/jquery/jquery-3.6.0"
  ]) ?>
  <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="main-body">
<div class="page">
<?php
$this->_template();
?>
</div>
</body>
</html>
<!-- business.tpl -->