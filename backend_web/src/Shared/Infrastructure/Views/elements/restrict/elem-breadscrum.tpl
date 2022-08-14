<?php
/**
 * @var App\Shared\Infrastructure\Views\AppView $this
 */
use App\Shared\Infrastructure\Helpers\RoutesHelper as Routes;

if (!isset($h1)) return;
$url = Routes::url("dashboard")
?>
<div class="breadcrumb-header justify-content-between">
  <div class="my-auto">
    <div class="d-flex">
      <span class="text-muted mt-1 tx-13 ms-2 mb-0">
        <a href="<?php $this->_echo($url);?>"><?=__("Home")?></a>
      </span>
      <span class="text-muted mt-1 tx-13 ms-2 mb-0">/</span>
      <span class="text-muted mt-1 tx-13 ms-2 mb-0"><?=$h1?></span>
    </div>
  </div>
</div>