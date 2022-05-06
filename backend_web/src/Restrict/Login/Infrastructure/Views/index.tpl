<?php
/**
 * @var App\Shared\Infrastructure\Views\AppView $this
 */
?>
<div class="card box-shadow-0">
  <div class="card-header">
    <h1 class="card-title mb-1"><?= __("Login") ?></h1>
  </div>
  <div class="card-body pt-0">
    <form-login csrf="<?=$csrf?>" />
  </div>
</div>
<?= $this->_asset_js_module("restrict/login") ?>
