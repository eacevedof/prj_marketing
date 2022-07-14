<?php
/**
 * @var App\Shared\Infrastructure\Views\AppView $this
 * @var string $pagetitle
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <link rel="icon" href="/themes/mypromo/images/provider-xxx-logo-orange.svg"/>
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php
  $this->_element("open/elem-css-common");
  ?>
  <link rel="stylesheet" href="/themes/mypromo/css/error.css" type="text/css" media="all" />
  <title><?php $this->_echo($pagetitle);?></title>
</head>
<body>
<main class="main-grid">
  <div class="div-wave-top">
    <svg viewBox="25 0 550 150" preserveAspectRatio="none" style="height: 300%; width: 110%;">
      <path d="M495 0H6.10352e-05V59C6.10352e-05 59 11.6918 42.2885 54.5 41.5C100.525 40.6522 171.967 19.5 218 19.5C257.5 19.5 329.036 29.6378 372.5 19.5C435.899 4.71257 495 0 495 0Z" fill="#3F3D56"/>
      <path d="M495 0H0V58C0 58 31.6918 32.7885 74.5 32C120.525 31.1522 140.967 45.5 187 45.5C226.5 45.5 303.536 28.1378 347 18C410.399 3.21257 495 0 495 0Z" fill="white" fill-opacity="0.5"/>
      <path d="M495 0H0V58C0 58 37.1918 25.2885 80 24.5C126.025 23.6522 166.967 36.5 213 36.5C252.5 36.5 295.536 25.1378 339 15C402.399 0.212575 495 0 495 0Z" fill="white"/>
    </svg>
  </div>

  <nav class="nav-flex center-x">
    <figure>
      <img id="top-mark" src="/themes/mypromo/images/logo-account-yyy.png" class="nav-icon">
    </figure>
  </nav>
<?php
$this->_element("open/elem-scrums");
?>
  <section class="section-grid center-x">
    <div class="div-texts">
      <img src="/themes/mypromo/images/icon-error.svg" class="icon">
      <h1><?php $this->_echo($h1); ?></h1>
      <?php
      if (is_string($error)) $this->_echo("<p>$error</p>");
      if (is_array($error)) {
        foreach ($error as $part) {
          if (is_string($part)) $this->_echo("<p>$part</p>");
          elseif (is_array($part)) {
            $h2 = $part["h2"] ?? "";
            if ($h2) $this->_echo("<h2>$h2</h2>");
            $h3 = $part["h3"] ?? "";
            if ($h3) $this->_echo("<h3>$h3</h3>");
            $p = $part["p"] ?? "";
            if ($p) $this->_echo("<p>$p</p>");
          }
        }
      }
      ?>
      <span class="code">[ <?php $this->_echo($code); ?> ]</span>
    </div>
  </section>
  <?php
  $this->_element("open/elem-footer");
  ?>
</main>
<?php
$this->_element("open/elem-animation");
?>
<div id="div-totop" class="div-totop">
  <a href="#top-mark">
    <svg width="60" height="60" viewBox="0 0 29 30" fill="none" xmlns="http://www.w3.org/2000/svg">
      <path d="M0 8.5H20V21H8V8.5Z" fill="black"/>
      <path d="M14.1417 4.99484C8.62572 4.99544 4.13776 9.4834 4.13644 15.0001C4.13725 20.516 8.62609 25.0049 14.1421 25.0057C19.658 25.0051 24.146 20.5171 24.1473 15.0005C24.1465 9.48307 19.6584 4.99494 14.1417 4.99484ZM19.1442 15.0004L15.1422 14.9996L15.143 20.0019L13.1423 20.0019L13.1415 14.9995L9.13951 14.9988L14.1411 9.9972L19.1442 15.0004Z" fill="#FFFF00FF"/>
    </svg>
  </a>
</div>
<?php
$this->_element("open/elem-js-center-y");
?>
</body>
</html>
<!-- /error.tpl -->