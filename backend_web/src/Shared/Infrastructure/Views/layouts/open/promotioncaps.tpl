<?php
/**
 * @var App\Shared\Infrastructure\Views\AppView $this
 * @var array $space
 */
?>
<!doctype html>
<html lang="en">
<head>
  <?php $this->_element("common/elem-gtag-js"); ?>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="icon" href="<?php $this->_echo_nohtml($space["businessfavicon"] ?? "/themes/mypromos/images/mypromos-logo-orange.svg")?>"/>
  <title><?php $this->_echo_nohtml($pagetitle ?? "")?></title>
  <?php
  $this->_element("common/elem-cookiebot");
  echo $this->_asset_css([
      "vendor/snackbar/snackbar.min"
  ]);
  echo $this->_asset_js([
      "vendor/jquery/jquery-3.6.0",
      "vendor/snackbar/snackbar.min"
  ])
  ?>
</head>
<body>
<!-- promotioncaps.tpl -->
<?php
$this->_element("common/elem-band-env");
$this->_element("open/elem-promotioncaps-style");
$this->_template();
?>
</body>
</html>
<!-- /promotioncaps.tpl -->