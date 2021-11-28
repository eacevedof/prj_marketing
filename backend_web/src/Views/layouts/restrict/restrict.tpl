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
    <title><?=$pagetitle?></title>
    <?= $this->_asset_css("index") ?>
    <?= $this->_asset_css("restrict/restrict") ?>
    <?= $this->_asset_css("vendor/datatable/datatable-1.11.3") ?>
    <?= $this->_asset_css("common/modal-raw")?>
    <?= $this->_asset_css("common/snackbar")?>

    <script src="https://unpkg.com/vue@next"></script>

    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <?= $this->_asset_js("vendor/lit.dev/lit-bundle") ?>
    <?= $this->_asset_js("vendor/jquery/jquery-3.6.0") ?>
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
</body>
</html>
