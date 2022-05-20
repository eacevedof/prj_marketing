<?php
/**
 * @var App\Shared\Infrastructure\Views\AppView $this
 * @var array $result
 * @var bool $statspermission
*/
if (!$statspermission) return;
?>
<div class="d-flex flex-row">
  <div class="card bg-primary-gradient">
    <div class="card-body">
      <div class="counter-status d-flex md-mb-0">
        <div class="ms-auto">
          <h5 class="tx-13 tx-white-8 mb-3"><?=__("Viewed")?></h5>
          <h2 class="counter mb-0 text-white"><?=$result["promotion"]["num_viewed"]?></h2>
        </div>
      </div>
    </div>
  </div>

  <div class="card bg-primary-gradient">
    <div class="card-body">
      <div class="counter-status d-flex md-mb-0">
        <div class="ms-auto">
          <h5 class="tx-13 tx-white-8 mb-3"><?=__("Subscribed")?></h5>
          <h2 class="counter mb-0 text-white"><?=$result["promotion"]["num_subscribed"]?></h2>
        </div>
      </div>
    </div>
  </div>

  <div class="card bg-primary-gradient">
    <div class="card-body">
      <div class="counter-status d-flex md-mb-0">
        <div class="ms-auto">
          <h5 class="tx-13 tx-white-8 mb-3"><?=__("Confirmed")?></h5>
          <h2 class="counter mb-0 text-white"><?=$result["promotion"]["num_confirmed"]?></h2>
        </div>
      </div>
    </div>
  </div>

  <div class="card bg-primary-gradient">
    <div class="card-body">
      <div class="counter-status d-flex md-mb-0">
        <div class="ms-auto">
          <h5 class="tx-13 tx-white-8 mb-3"><?=__("Executed")?></h5>
          <h2 class="counter mb-0 text-white"><?=$result["promotion"]["num_executed"]?></h2>
        </div>
      </div>
    </div>
  </div>
</div>