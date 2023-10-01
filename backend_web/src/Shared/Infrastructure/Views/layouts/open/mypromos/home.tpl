<?php
/**
 * @var App\Shared\Infrastructure\Views\AppView $this
 * @var string $pagetitle
 */
use App\Shared\Infrastructure\Helpers\Views\EnvIconHelper;
?>
<!doctype html>
<html lang="en">
<head>
  <?php $this->_element("common/elem-gtag-js"); ?>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="icon" href="<?=EnvIconHelper::getIconPath()?>" />
  <title><?php $this->_echoHtmlEscaped($pagetitle ?? "")?></title>
  <meta name="description" content="">
  <?php $this->_element("common/elem-cookiebot"); ?>
  <link rel="stylesheet" href="/themes/mypromos/css/global.css" type="text/css" media="all" />
  <link rel="stylesheet" href="/themes/mypromos/css/footer.css" type="text/css" media="all" />
  <?php
  echo $this->_getAssetCssTag([
      "vendor/snackbar/snackbar.min"
  ]);
  echo $this->_getAssetJsTag([
      "vendor/jquery/jquery-3.6.0",
      "vendor/snackbar/snackbar.min"
  ])
  ?>
  <!-- js -->
  <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <?php
  echo $this->_getAssetJsTagAsModule(["common/snackbar"]);
  ?>
</head>
<body>
<?php
$this->_element("common/elem-band-env");
$this->_template();
?>
</body>
</html>
<!-- open.tpl -->