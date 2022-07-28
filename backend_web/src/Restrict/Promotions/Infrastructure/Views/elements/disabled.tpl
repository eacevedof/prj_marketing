<?php
/**
 * @var App\Shared\Infrastructure\Views\AppView $this
 * @var array $result
*/
if (!$result["promotion"]["disabled_date"]) return;
?>
<div class="alert alert-warning mt-3 rounded-5">
  <strong><?=__("Promotion disabled")?></strong>
  <p>
    <?php $this->_echo_nohtml($result["promotion"]["disabled_reason"]);?>
  </p>
  <p>
    <?=__("Please, contact <a href=\"mailto:{0}\">{1}</a> to resolve this issue", "support@yyy.xxx")?>
  </p>
</div>