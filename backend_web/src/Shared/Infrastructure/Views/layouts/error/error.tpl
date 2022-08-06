<?php
/**
 * @var App\Shared\Infrastructure\Views\AppView $this
 * @var string $pagetitle
 */
?>
<!doctype html>
<html lang="en">
<head>
  <?php $this->_element("open/elem-gtag-js"); ?>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="shortcut icon" href="#">
  <title><?=$pagetitle ?? ""?></title>
  <?php $this->_element("common/elem-cookiebot");?>
  <?= $this->_asset_css("vendor/normalize/normalize-8.0.1.min") ?>
  <link href="/themes/valex/assets/plugins/bootstrap/css/bootstrap.min.css" theme="valex" rel="stylesheet">
  <link href="/themes/valex/assets/css/style.css" theme="valex" rel="stylesheet">
</head>
<body class="main-body bg-primary-transparent">
<!-- error.tpl -->
<div class="page">
<?php
$this->_template();
?>
</div>
</body>
</html>