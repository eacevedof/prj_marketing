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
        <form class="form-horizontal">
          <div class="form-group">
            <input type="email" class="form-control" id="inputEmail3" placeholder="Email">
          </div>
          <div class="form-group">
            <input type="password" class="form-control" id="inputPassword3" placeholder="Password">
          </div>
          <div class="form-group mb-0 mt-3 justify-content-end">
            <div>
              <button type="submit" class="btn btn-primary">Sign in</button>
              <button type="submit" class="btn btn-secondary">Cancel</button>
            </div>
          </div>
        </form>

        <form-login csrf="<?=$csrf?>" />
      </div>
    </div>
  </div>
</div>
<?= $this->_asset_js_module("restrict/login") ?>
