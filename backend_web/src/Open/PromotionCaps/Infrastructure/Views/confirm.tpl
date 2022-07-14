<?php
/**
 * @var App\Shared\Infrastructure\Views\AppView $this
 * @var array $result
 * @var string $pagetitle
 * @var string $h1
 * @var string $error
 */
?>
<h1><?=$business = $result["business"];?></h1>
<p>
  <?=__("<b>{0}</b> you have confirmed your subscription to <b>&ldquo;{1}&rdquo;</b>", $result["username"], $result["promotion"])?>
</p>
<p>
  <?=__("Please check your email inbox. You will receive a subscription code in order to show it at <b>{0}</b>", $business)?>
</p>