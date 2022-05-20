<?php
/**
 * @var App\Shared\Infrastructure\Views\AppView $this
 * @var array $result
 * @var bool $statspermission
*/
if (!$statspermission) return;
?>
<div class="row">
  <div class="col-3">
    <div class="card bg-danger-gradient">
      <div class="card-body">
        <div class="counter-status md-mb-0">
          <div class="ms-auto">
            <h5 class="tx-lg-20 tx-white-8 mb-2"><?=__("Viewed")?></h5>
            <h2 class="counter mb-0 text-white"><?=$result["promotion"]["num_viewed"]?></h2>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-3">
    <div class="card bg-warning-gradient">
      <div class="card-body">
        <div class="counter-status md-mb-0">
          <div class="ms-auto">
            <h5 class="tx-lg-20 tx-white-8 mb-2"><?=__("Subscribed")?></h5>
            <h2 class="counter mb-0 text-white"><?=$result["promotion"]["num_subscribed"]?></h2>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-3">
    <div class="card bg-success-gradient">
      <div class="card-body">
        <div class="counter-status md-mb-0">
          <div class="ms-auto">
            <h5 class="tx-lg-20 tx-white-8 mb-2"><?=__("Confirmed")?></h5>
            <h2 class="counter mb-0 text-white"><?=$result["promotion"]["num_confirmed"]?></h2>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-3">
    <div class="card bg-info-gradient">
      <div class="card-body">
        <div class="counter-status md-mb-0">
          <div class="ms-auto">
            <h5 class="tx-lg-20 tx-white-8 mb-2"><?=__("Executed")?></h5>
            <h2 class="counter mb-0 text-white"><?=$result["promotion"]["num_executed"]?></h2>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
