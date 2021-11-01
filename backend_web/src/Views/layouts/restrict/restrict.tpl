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
    <script src="https://unpkg.com/vue@next"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <?= $this->_asset_js("index") ?>
</head>
<body>
<main>
<?
$this->_element("common/nav-menu");
$this->_template();
?>
</main>
</body>
</html>
