<?php
/**
 * @var App\Shared\Infrastructure\Views\AppView $this
 * @var array $result
 * @var string $h1
 * @var string $csrf
 */

$texts = [
  "tr00" => __("Save"),
  "tr01" => __("Processing..."),
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
          <a href="#main" data-bs-toggle="tab" class="active" aria-expanded="true">
            <span class="visible-xs">
              <i class="las la-yyy tx-16 me-1"></i>
            </span>
            <span class="hidden-xs">
              <?=__("tr_tab_yyy")?>
            </span>
          </a>
        </li>
      </ul>
    </div><!--nav-->

    <div class="tab-content border-start border-bottom border-right border-top-0 p-2 br-dark">
      <div id="main" class="tab-pane active">
        <form-xxx-update
          csrf=<?$this->_echo_js($csrf);?>

          texts="<?$this->_echo_jslit($texts);?>"

          fields="<?$this->_echo_jslit($result);?>"
        />
      </div>
    </div><!--tab-content-->

  </div><!--card-body-->
</div>
<script type="module" src="/assets/js/restrict/xxxs/update.js"></script>