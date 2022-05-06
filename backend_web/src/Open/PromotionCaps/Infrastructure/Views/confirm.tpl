<?php
/**
 * @var App\Shared\Infrastructure\Views\AppView $this
 * @var array $result
 */
?>
<main>
<?php
if (isset($error)) {
  $this->_element("common/elem-error", ["title"=>$h1, "description"=>$description]);
  return;
}
?>
  <h1><?=$business = $result["business"];?></h1>
  <p>
    <?=__("{0} have confirmed your subscription to <b>{1}</b>", $result["user"], $result["promotion"])?>
  </p>
  <p>
    <?=__("Please check your email inbox. You will receive a subscription code you have to show at <b>{0}</b>", $business)?>
  </p>
</main>