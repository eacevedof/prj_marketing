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
  </div>
  <div class="card-body p-2 pt-0">
    <div class="tab-content border-start border-bottom border-right border-top-0 p-2 br-dark">
      <?php
      $this->_element_view("update-status/form-subscription-qr-update");
      ?>
    </div>
  </div>
</div>
<?php
$this->_element("restrict/elem-modal-launcher-showtab");
?>