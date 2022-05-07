<?php
/**
 * @var App\Shared\Infrastructure\Views\AppView $this
 * @var array $result
 * @var string $h1
 * @var string $error
 */
?>
<main>
<?php
if (isset($error))
  return $this->_element("common/elem-error", ["title"=>$h1, "description"=>$error]);
?>
  <h1><?=$business = $result["business"];?></h1>
  <p>
    <?=__("{0} have confirmed your subscription to <b>&ldquo;{1}&rdquo;</b>", $result["username"], $result["promotion"])?>
  </p>
  <p>
    <?=__("Please check your email inbox. You will receive a subscription code you have to show at <b>{0}</b>", $business)?>
  </p>
</main>