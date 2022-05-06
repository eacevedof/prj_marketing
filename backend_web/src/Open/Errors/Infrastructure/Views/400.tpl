<?php
/**
 * @var App\Shared\Infrastructure\Views\AppView $this
 */
if (!isset($urlback)) $urlback = "";
if (!isset($ismodal)) $ismodal = 0;
?>
<!--400 bad rquest-->
<div class="main-error-wrapper page page-h">
  <img src="/themes/valex/assets/img/media/400.png" class="error-page" alt="error">
  <h2><?=$h1?></h2>
  <p><?=$description?></p>
  <?php
  if(!$ismodal){
    if($urlback) echo "<a class=\"btn btn-outline-danger\" href=\"$urlback\">".__("Back to home")."</a>";
    if($authuser) echo "<a class=\"btn btn-outline-danger\" href=\"/restrict\">".__("Dashboard")."</a>";
  }
  ?>
</div>