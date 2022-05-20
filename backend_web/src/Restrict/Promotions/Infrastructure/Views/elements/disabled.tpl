<?php
/**
 * @var App\Shared\Infrastructure\Views\AppView $this
 * @var array $result
*/
if (!$result["promotion"]["disabled_date"]) return;
?>
<div class="row mt-2">
  <h5><?=__("Promotion disabled")?></h5>
  <p>
    <?$this->_echo_nohtml($result["promotion"]["disabled_reason"]);?>
  </p>
</div>