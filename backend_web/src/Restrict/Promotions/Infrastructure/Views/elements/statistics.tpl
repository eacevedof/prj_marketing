<?php
/**
 * @var App\Shared\Infrastructure\Views\AppView $this
 * @var array $result
 * @var bool $statspermission
*/
if (!$statspermission) return;
?>
<div class="table-responsive">
  <table class="table table-striped table-bordered mb-0 text-sm-nowrap text-lg-nowrap text-xl-nowrap">
    <thead>
    <tr>
      <th class="wd-lg-25p tx-center bg-danger"><?=__("Viewed")?></th>
      <th class="wd-lg-25p tx-center bg-warning"><?=__("Subscribed")?></th>
      <th class="wd-lg-25p tx-center bg-info"><?=__("Confirmed")?></th>
      <th class="wd-lg-25p tx-center bg-success"><?=__("Executed")?></th>
    </tr>
    </thead>
    <tbody>
    <tr>
      <td id="num-viewed" class="tx-center tx-medium tx-inverse"><?=$result["promotion"]["num_viewed"]?></td>
      <td id="num-subscribed" class="tx-center tx-medium tx-inverse"><?=$result["promotion"]["num_subscribed"]?></td>
      <td id="num-confirmed" class="tx-center tx-medium tx-info"><?=$result["promotion"]["num_confirmed"]?></td>
      <td id="num-executed" class="tx-center tx-medium tx-success"><?=$result["promotion"]["num_executed"]?></td>
    </tr>
    </tbody>
  </table>
</div>
