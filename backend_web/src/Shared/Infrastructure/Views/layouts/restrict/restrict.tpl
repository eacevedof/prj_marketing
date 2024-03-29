<?php
/**
 * @var App\Shared\Infrastructure\Views\AppView $this
 * @var string $pagetitle
 */
use App\Shared\Infrastructure\Helpers\RoutesHelper as Routes;
$requri = $_SERVER["REQUEST_URI"];
?>
<!doctype html>
<html lang="en">
<head>
  <?php $this->_element("common/elem-gtag-js"); ?>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php $this->_element("common/elem-favicon") ?>
  <title><?=$pagetitle?></title>
<!-- css -->
  <?= $this->_getAssetCssTag("vendor/normalize/normalize-8.0.1.min") ?>
  <link href="/themes/valex/assets/css/icons.css" theme="valex" rel="stylesheet">
  <link href="/themes/valex/assets/plugins/materialdesignicons/materialdesignicons.css" theme="valex" rel="stylesheet">
  <link href="/themes/valex/assets/plugins/bootstrap/css/bootstrap.min.css" theme="valex" rel="stylesheet">
  <link href="/themes/valex/assets/css/style.css" theme="valex" rel="stylesheet">
  <?= $this->_getAssetCssTag([
      "index",
      "restrict/restrict",
      "common/modal-raw",
      "common/snackbar",
      //"common/fielderrors",
      //"common/formflex"
  ]) ?>

<!-- js -->
  <script src="/themes/valex/assets/plugins/bootstrap/js/bootstrap.min.js"></script>
  <?= $this->_getAssetJsTag([
    "vendor/jquery/jquery-3.6.0"
  ]) ?>
  <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <?= $this->_getAssetJsTagAsModule([
    "index",
    "common/snackbar"
  ])?>
</head>
<body>
<?php
$this->_element("common/elem-snackbar");
$this->_element("common/elem-band-env");
?>
<main>
<?php
$this->_element("common/elem-nav-menu");
?>
<div class="horizontalMenucontainer mmm">
  <div class="main-content horizontal-content">
    <div class="container">
    <?php
    $this->_element("restrict/elem-breadscrum");
    $this->_template();
    ?>
    </div>
  </div>
  <?php
  $this->_element("restrict/elem-footer");
  if (!strstr(Routes::getUrlByRouteName("login"), $requri)):
  ?>
  <a href="#top" id="back-to-top" style="display: block;"><i class="las la-angle-double-up"></i></a>
  <?php
  endif;
  ?>
</div>
</main>
<?php
$this->_element("common/elem-modal-raw");
$this->_element("common/elem-spinner");
?>
</body>
</html>
