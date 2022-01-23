<?php
/**
 * @var App\Views\AppView $this
 * @var string $h1
 * @var string $csrf
 * @var array $businessowners
 */
$texts = [
  "tr00" => __("send"),
  "tr01" => __("Sending..."),
  "tr02" => __("Cancel"),
  "tr03" => __("Error"),
  "tr04" => __("<b>Data created</b>"),

  "f02" => __("Owner"),
  "f03" => __("External code"),
  "f04" => __("Description"),
  "f05" => __("Slug"),
  "f06" => __("Content"),
  "f07" => __("Type"),
  "f08" => __("Date from"),
  "f09" => __("Date to"),
  "f10" => __("Url social"),
  "f11" => __("Url design"),
  "f12" => __("Enabled"),
  "f13" => __("Invested"),
  "f14" => __("Inv returned"),
  "f15" => __("Notes"),
];


$result = [
  "id_owner" => "",
  "code_erp" => "",
  "description" => "",
  "slug" => "",
  "content" => "",
  "id_type" => "",
  "date_from" => ($date = date("Y-m-d")),
  "date_to" => $date,
  "url_social" => "",
  "url_design" => "",
  "is_active" => "0",
  "invested" => "0.00",
  "returned" => "0.00",
  "notes" => "",

  "businessowners" => $businessowners
];
?>
<div class="modal-form">
  <div class="card-header">
    <h4 class="card-title mb-1"><?=$h1?></h4>
  </div>
  <div class="card-body pt-0">
    <form-promotion-create
      csrf=<?$this->_echo_js($csrf);?>

      texts="<?$this->_echo_jslit($texts);?>"

      fields="<?$this->_echo_jslit($result);?>"
    />
  </div>
</div>
<script type="module" src="/assets/js/restrict/promotions/create.js"></script>