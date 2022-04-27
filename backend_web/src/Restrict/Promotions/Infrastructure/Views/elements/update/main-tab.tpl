<?php
/**
 * @var App\Shared\Infrastructure\Views\AppView $this
 * @var array $user
*/
if (empty($user)) return;
?>
<li>
  <a href="#main" class="active" data-bs-toggle="tab" aria-expanded="false">
    <span class="visible-xs"><i class="las la-images tx-15 me-1"></i></span>
    <span class="hidden-xs"><?=__("Promotion")?></span>
  </a>
</li>