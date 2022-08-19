<?php
/**
 * @var App\Shared\Infrastructure\Views\AppView $this
 * @var array $result
*/
if (is_null($result["businessattributespace"])) return;
?>
<li>
  <a href="#businessattributespace" data-bs-toggle="tab" aria-expanded="true">
    <span class="visible-xs"><i class="las la-user-circle tx-16 me-1"></i></span>
    <span class="hidden-xs"><?=__("Business space")?></span>
  </a>
</li>