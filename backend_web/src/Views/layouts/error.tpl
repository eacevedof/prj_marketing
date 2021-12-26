<?php
/**
 * @var \App\Views\AppView $this
 * @var string $pagetitle
 */
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?=$pagetitle ?? ""?></title>
    <?= $this->_asset_css("vendor/normalize/normalize-8.0.1.min") ?>
    <link href="/themes/valex/assets/css/icons.css" theme="valex" rel="stylesheet">
    <link href="/themes/valex/assets/plugins/materialdesignicons/materialdesignicons.css" theme="valex" rel="stylesheet">
    <link href="/themes/valex/assets/plugins/bootstrap/css/bootstrap.min.css" theme="valex" rel="stylesheet">
    <link href="/themes/valex/assets/css/style.css" theme="valex" rel="stylesheet">
    <?= $this->_asset_css([
        "index",
    ]) ?>
</head>
<body class="main-body bg-primary-transparent">
<!-- error.tpl -->
<div class="page">
  <div class="main-error-wrapper  page page-h ">
    <img src="/themes/valex/assets/img/media/404.png" class="error-page" alt="error">
    <h2>Oopps. The page you were looking for doesn't exist.</h2>
    <h6>You may have mistyped the address or the page may have moved.</h6>
    <a class="btn btn-outline-danger" href="/">Back to Home</a>
  </div>
</div>
</body>
</html>