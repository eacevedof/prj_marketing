<?php
/**
 * @var App\Shared\Infrastructure\Views\AppView $this
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
  <link href="/themes/valex/assets/plugins/bootstrap/css/bootstrap.min.css" theme="valex" rel="stylesheet">
  <link href="/themes/valex/assets/css/style.css" theme="valex" rel="stylesheet">
</head>
<body class="main-body bg-primary-transparent">
<!-- error.tpl -->
<div class="page">
<?
$this->_template();
?>
</div>
</body>
</html>