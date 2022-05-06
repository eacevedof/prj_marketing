<?php
/**
 * @var App\Shared\Infrastructure\Views\AppView $this
 * @var string $business
 * @var string $user
 * @var string $promotion
 */
?>
<main>
  <h1><?=$business?></h1>
  <p>
    <?=__("{0} have confirmed your subscription to <b>{1}</b>", $user, $promotion)?>
  </p>
  <p>
    <?=__("Please check your email inbox. You will receive a subscription code you have to show at <b>{0}</b>", $business)?>
  </p>
</main>