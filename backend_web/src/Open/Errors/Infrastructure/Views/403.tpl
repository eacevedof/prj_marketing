<?php
/**
 * @var App\Shared\Infrastructure\Views\AppView $this
 */
if (!isset($urlback)) $urlback = "";
if (!isset($ismodal)) $ismodal = 0;
?>
<!--403 forbidden-->
<div class="main-error-wrapper page page-h">
  <img src="/themes/valex/assets/img/media/403.png" class="error-page" alt="error">
  <h2>Sorry!. You are not allowed to see this content</h2>
  <h6>Contact the site admin to request for access</h6>
  <?
  if(!$ismodal){
    if($urlback) echo "<a class=\"btn btn-outline-danger\" href=\"$urlback\">".__("Back to home")."</a>";
    if($authuser) echo "<a class=\"btn btn-outline-danger\" href=\"/restrict\">".__("Dashboard")."</a>";
  }
  ?>
</div>