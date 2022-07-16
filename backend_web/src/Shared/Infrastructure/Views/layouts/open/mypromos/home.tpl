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
  <link rel="icon" href="/themes/mypromos/images/provider-xxx-logo-orange.svg"/>
  <title><?=$pagetitle ?? ""?></title>
  <link rel="stylesheet" href="/themes/mypromos/css/global.css" type="text/css" media="all" />
  <link rel="stylesheet" href="/themes/mypromos/css/footer.css" type="text/css" media="all" />
  <?php
  echo $this->_element("open/elem-gtag-js");
  ?>
  <!-- js -->
  <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <?php
  echo $this->_asset_js_module(["common/snackbar"]);
  ?>
</head>
<?php
$this->_template();
?>
</body>
</html>
<!-- open.tpl -->