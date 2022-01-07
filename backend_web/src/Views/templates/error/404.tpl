<?php
/**
 * @var \App\Views\AppView $this
 */
if (!isset($urlback)) $urlback = "#";
if (!isset($ismodal)) $ismodal = 0;
?>
<!--404 not found-->
<div class="main-error-wrapper  page page-h ">
  <img src="/themes/valex/assets/img/media/404.png" class="error-page" alt="error">
  <h2>Oopps. The page you were looking for doesn't exist.</h2>
  <h6>You may have mistyped the address or the page may have moved.</h6>
  <?
  if(!$ismodal):
  ?>
  <a class="btn btn-outline-danger" href="<?=$urlback?>">Back to Home</a>
  <?
  endif;
  ?>
</div>
