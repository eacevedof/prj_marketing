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
    <?=__("<b>{0}</b> you have cancelled your subscription to <b>&ldquo;{1}&rdquo;</b>", $result["username"], $result["promotion"])?>
  </p>
</main>