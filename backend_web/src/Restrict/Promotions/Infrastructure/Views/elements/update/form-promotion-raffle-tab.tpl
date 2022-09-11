<?php
/**
 * @var App\Shared\Infrastructure\Views\AppView $this
 * @var array $result
*/
$promotion = $result["promotion"];
$raffle = $result["raffle"] ?? null;
if (is_null($raffle)) return;
?>
<li>
  <a href="#raffle" data-bs-toggle="tab" aria-expanded="false">
    <span class="visible-xs"><i class="las la-images tx-15 me-1"></i></span>
    <span class="hidden-xs"><?=__("Raffle")?></span>
  </a>
</li>