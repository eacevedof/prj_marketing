<?php
/**
 * @var \App\Views\AppView $this
 * @var string $pagetitle
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?=$pagetitle?></title>
    <?= $this->_asset_css("index") ?>
    <?= $this->_asset_css("restrict/index") ?>
    <script src="https://unpkg.com/vue@next"></script>
    <?= $this->_asset_js("index") ?>
</head>
<body>
<?
$this->_template();
?>
</body>
</html>
