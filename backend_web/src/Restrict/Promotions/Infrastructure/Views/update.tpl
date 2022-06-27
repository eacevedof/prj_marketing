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
    <?php $this->_element_view("disabled");?>
  </div>
  <div class="card-body p-2 pt-0">
    <div class="tabs-menu">
      <ul class="nav nav-tabs profile navtab-custom panel-tabs">
        <?php
        $this->_element_view("update/main-tab");
        $this->_element_view("update/ui-tab");
        ?>
      </ul>
    </div><!--nav-->

    <div class="tab-content border-start border-bottom border-right border-top-0 p-2 br-dark">
      <?php
      $this->_element_view("update/main-content");
      $this->_element_view("update/ui-content");
      $this->_element_view("statistics");
      ?>
    </div><!--tab-content-->

  </div><!--card-body-->
</div>
<script type="module" src="/assets/js/restrict/promotions/update.js"></script>
<script type="module" src="/assets/js/restrict/promotions/ui/update.js"></script>
<?php
$this->_element("restrict/elem-modal-launcher-showtab");
?>