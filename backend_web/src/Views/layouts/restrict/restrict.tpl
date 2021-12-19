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
    <link rel="shortcut icon" href="#">
    <title><?=$pagetitle?></title>
<!-- css -->
    <?= $this->_asset_css("vendor/normalize/normalize-8.0.1.min") ?>
    <link href="/themes/valex/assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="/themes/valex/assets/css/style.css" rel="stylesheet">
    <?= $this->_asset_css("index") ?>
    <?= $this->_asset_css("restrict/restrict") ?>
    <?= $this->_asset_css("vendor/datatable/datatable-1.11.3") ?>
    <?= $this->_asset_css("common/modal-raw")?>
    <?= $this->_asset_css("common/snackbar")?>

<!-- js -->
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <?= $this->_asset_js("vendor/jquery/jquery-3.6.0") ?>
    <?= $this->_asset_js("vendor/datatable/datatable-1.11.3") ?>
    <?= $this->_asset_js_module("index") ?>
    <?= $this->_asset_js_module("common/snackbar") ?>
</head>
<body>
<main>
<?
$this->_element("common/elem-nav-menu");
$this->_template();
?>
</main>
<?=$this->_element("common/elem-modal-raw")?>
<?=$this->_element("common/elem-snackbar")?>
<?=$this->_element("common/elem-spinner")?>
</body>
</html>
