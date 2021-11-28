<?php
/**
 * @var \App\Views\AppView $this
 */

?>
<h1><?= __("Login") ?></h1>
<div id="app">
  <form-login csrf="<?=$csrf?>" />
</div>
<?= $this->_asset_js_module("restrict/login") ?>
