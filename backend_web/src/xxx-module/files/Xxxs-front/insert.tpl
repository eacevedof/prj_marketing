<?php
/**
 * @var App\Shared\Infrastructure\Views\AppView $this
 * @var string $h1
 * @var string $csrf
 */
$texts = [
  "tr00" => __("Save"),
  "tr01" => __("Processing..."),
  "tr02" => __("Cancel"),
  "tr03" => __("Error"),
  "tr04" => __("<b>Data created</b>"),

  %FIELD_LABELS%
];

$result = [
  %FIELD_KEY_AND_VALUES%

  "xxxs" => $xxxs,
  "notoryes" => $notoryes,
  "businessowners" => $businessowners,
];
?>
<div class="modal-form">
  <div class="card-header">
    <h4 class="card-title mb-1"><?=$h1?></h4>
  </div>
  <div class="card-body pt-0">
    <form-xxx-insert
      csrf=<?php $this->_echo_js($csrf);?>

      texts="<?php $this->_echo_jslit($texts);?>"

      fields="<?php $this->_echo_jslit($result);?>"
    />
  </div>
</div>
<script type="module" src="/assets/js/restrict/xxxs/insert.js"></script>