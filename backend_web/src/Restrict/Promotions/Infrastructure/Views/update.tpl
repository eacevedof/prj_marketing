<?php
/**
 * @var App\Shared\Infrastructure\Views\AppView $this
 * @var array $result
 * @var string $h1
 * @var string $csrf
 */
?>
<div class="modal-form">
  <div class="card-header">
    <h4 class="card-title mb-1"><?=$h1?></h4>
    <?php $this->_includeViewElement("disabled");?>
  </div>
  <div class="card-body p-2 pt-0">
    <div class="tabs-menu">
      <ul class="nav nav-tabs profile navtab-custom panel-tabs">
        <?php
        $this->_includeViewElement("update/form-promotion-update-tab");
        $this->_includeViewElement("update/form-promotion-ui-update-tab");
        ?>
      </ul>
    </div><!--nav-->

    <div class="tab-content border-start border-bottom border-right border-top-0 p-2 br-dark">
      <?php
      $this->_includeViewElement("update/form-promotion-update");
      $this->_includeViewElement("update/form-promotion-ui-update");
      $this->_includeViewElement("statistics");
      ?>
    </div><!--tab-content-->

  </div><!--card-body-->
</div>
<?php
$this->_element("restrict/elem-modal-launcher-showtab");
?>