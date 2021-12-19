<?php
/**
 * @var \App\Views\AppView $this
 */
?>
<div class="row" style="justify-content: center; align-items: center;">
  <div class="col-lg-6 col-xl-6 col-md-12 col-sm-12">
    <div class="card box-shadow-0">
      <div class="card-header">
        <h1 class="card-title mb-1"><?= __("Login") ?></h1>
      </div>
      <div class="card-body pt-0">
        <form-login csrf="<?=$csrf?>" />
      </div>
    </div>
  </div>
</div>
<?= $this->_asset_js_module("restrict/login") ?>
