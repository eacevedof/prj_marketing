<?php
/**
 * @var \App\Shared\Infrastructure\Views\AppView $this
 */
if (!isset($urlback)) $urlback = "";
if (!isset($ismodal)) $ismodal = 0;
?>
<!--500 not found-->
<div class="main-error-wrapper page page-h">
  <img src="/themes/valex/assets/img/media/500.png" class="error-page" alt="error">
  <h2>Oopps. Internal server error 500.</h2>
  <?
  if(!$ismodal){
    if($urlback) echo "<a class=\"btn btn-outline-danger\" href=\"$urlback\">".__("Back to home")."</a>";
    if($authuser) echo "<a class=\"btn btn-outline-danger\" href=\"/restrict\">".__("Dashboard")."</a>";
  }
  ?>
</div>
