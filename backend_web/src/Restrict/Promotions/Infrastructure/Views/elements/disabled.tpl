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
    <?$this->_echo_nohtml($result["promotion"]["disabled_reason"]);?>
  </p>
  <p>
    <?=__("Please, contact <a href=\"mailto:support@yyy.xxx\">support@yyy.xxx</a> to resolve this issue")?>
  </p>
</div>