<?php
/**
 * @var App\Shared\Infrastructure\Views\AppView $this
 * @var array $result
 * @var bool $statspermission
*/
if (!$statspermission) return;
?>
<div class="row mt-2">
  <div class="col-3">
    <b><?=__("Viewed")?>:</b>&nbsp;<span><?=$result["promotion"]["num_viewed"]?></span>
  </div>
  <div class="col-3">
    <b><?=__("Subscribed")?>:</b>&nbsp;<span><?=$result["promotion"]["num_subscribed"]?></span>
  </div>
  <div class="col-3">
    <b><?=__("Confirmed")?>:</b>&nbsp;<span><?=$result["promotion"]["num_confirmed"]?></span>
  </div>
  <div class="col-3">
    <b><?=__("Executed")?>:</b>&nbsp;<span><?=$result["promotion"]["num_executed"]?></span>
  </div>
</div>