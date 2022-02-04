<?php
/**
 * @var App\Shared\Infrastructure\Views\AppView $this
 * @var array $result
 * @var string $h1
 * @var string $csrf
 */

$texts = [
  "tr00" => __("send"),
  "tr01" => __("Sending..."),
  "tr02" => __("Cancel"),
  "tr03" => __("Error"),
  "tr04" => __("<b>Data updated</b>"),

  %FIELD_LABELS%
];

$result = [
  %FIELD_KEY_AND_VALUES%
];
?>
<div class="modal-form">
  <div class="card-header">
    <h4 class="card-title mb-1"><?=$h1?></h4>
  </div>
  <div class="card-body p-2 pt-0">
    <div class="tabs-menu ">
      <ul class="nav nav-tabs profile navtab-custom panel-tabs">
        <li>
          <a href="#tab-1" data-bs-toggle="tab" class="active" aria-expanded="true">
            <span class="visible-xs">
              <i class="las la-xxx-circle tx-16 me-1"></i>
            </span>
            <span class="hidden-xs">
              <?=__("tr_tab_1")?>
            </span>
          </a>
        </li>
          <li>
            <a href="#tab-2" data-bs-toggle="tab" aria-expanded="false">
              <span class="visible-xs"><i class="las la-images tx-15 me-1"></i></span>
              <span class="hidden-xs">
                <?=__("tr_tab_2")?>
              </span>
            </a>
          </li>
      </ul>
    </div><!--nav-->

    <div class="tab-content border-start border-bottom border-right border-top-0 p-2 br-dark">
      <div class="tab-pane active" id="profile">
        <form-xxx-edit
          csrf=<?$this->_echo_js($csrf);?>

          texts="<?$this->_echo_jslit($texts);?>"

          fields="<?$this->_echo_jslit($result);?>"
        />
      </div>

      <div class="tab-pane" id="permissions">

      </div>
    </div><!--tab-content-->

  </div><!--card-body-->
</div>
<script type="module" src="/assets/js/restrict/xxxs/update.js"></script>