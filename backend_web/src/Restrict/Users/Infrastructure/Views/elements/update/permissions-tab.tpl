<?php
/**
 * @var App\Shared\Infrastructure\Views\AppView $this
 * @var array $result
*/
if (is_null($result["permissions"])) return;
?>
<li>
  <a href="#permissions" data-bs-toggle="tab" aria-expanded="false">
    <span class="visible-xs"><i class="las la-images tx-15 me-1"></i></span>
    <span class="hidden-xs"><?=__("Permissions")?></span>
  </a>
</li>