<?php
/**
 * @var App\Shared\Infrastructure\Views\AppView $this
 * @var string $uuid
 * @var array $result
 */
echo $this->_asset_css([
    "vendor/theme-min/icons",
    "common/tooltip"
]);
?>
<div class="modal-form">
  <div class="card-header">
    <h4 class="card-title mb-1"><?=$h1?></h4>
  </div>
  <div class="card-body p-2 pt-0">

    <div class="tabs-menu">
      <ul class="nav nav-tabs profile navtab-custom panel-tabs">
<?php
$this->_element_view("update/form-user-update-tab");
$this->_element_view("update/form-user-permissions-update-tab");
$this->_element_view("update/form-user-businessdata-update-tab");
$this->_element_view("update/form-user-businessattribute-space-update-tab");
$this->_element_view("update/form-user-preferences-update-tab");
?>
      </ul>
    </div><!--nav-->

    <div class="tab-content border-start border-bottom border-right border-top-0 p-2 br-dark">
<?php
$this->_element_view("update/form-user-update");
$this->_element_view("update/form-user-permissions-update");
$this->_element_view("update/form-user-businessdata-update");
$this->_element_view("update/form-user-businessattribute-space-update");
$this->_element_view("update/form-user-preferences-update");
?>
    </div><!--tab-content-->
  </div><!--card-body-->
</div>
<?php
$this->_element("restrict/elem-modal-launcher-showtab");
?>