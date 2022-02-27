<?php
/**
 * @var App\Shared\Infrastructure\Views\AppView $this
 * @var string $uuid
 * @var array $result
 */
echo $this->_asset_css([
    "/assets/css/vendor/theme-min/icons",
    "/assets/css/common/tooltip"
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
$this->_element_view("update/main-tab");
$this->_element_view("update/permissions-tab");
$this->_element_view("update/businessdata-tab");
$this->_element_view("update/preferences-tab");
?>
      </ul>
    </div><!--nav-->

    <div class="tab-content border-start border-bottom border-right border-top-0 p-2 br-dark">
<?php
$this->_element_view("update/main-content");
$this->_element_view("update/permissions-content");
$this->_element_view("update/businessdata-content");
$this->_element_view("update/preferences-content");
?>
    </div><!--tab-content-->
  </div><!--card-body-->
</div>
<script type="module" src="/assets/js/restrict/users/update.js"></script>
<script type="module" src="/assets/js/restrict/users/permissions/update.js"></script>
<script type="module" src="/assets/js/restrict/users/businessdata/update.js"></script>
<script type="module" src="/assets/js/restrict/users/preferences/update.js"></script>
<script type="module">
import {show_tab} from "/assets/js/common/modal-launcher.js"
show_tab()
</script>