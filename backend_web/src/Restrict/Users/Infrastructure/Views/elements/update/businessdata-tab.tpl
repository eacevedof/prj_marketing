<?php
/**
 * @var App\Shared\Infrastructure\Views\AppView $this
 * @var array $result
*/
if (is_null($result["businessdata"])) return;
?>
<li>
  <a href="#main" data-bs-toggle="tab" class="active" aria-expanded="true">
    <span class="visible-xs"><i class="las la-user-circle tx-16 me-1"></i></span>
    <span class="hidden-xs"><?=__("Profile")?></span>
  </a>
</li>