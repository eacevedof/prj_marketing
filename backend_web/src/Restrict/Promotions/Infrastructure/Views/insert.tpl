<?php
/**
 * @var App\Shared\Infrastructure\Views\AppView $this
 * @var App\Shared\Infrastructure\Components\Date\DateComponent $date;
 * @var string $h1
 * @var string $csrf
 * @var array $businessowners
 * @var array $promotions
 * @var array $notoryes
 */
use App\Shared\Infrastructure\Factories\ComponentFactory as CF;
use App\Shared\Infrastructure\Components\Date\DateComponent;
$date = CF::get(DateComponent::class);

$datefrom = $date->set_date1(date("Y-m-d H:i:s"))->explode()->to_js()->get();
$dateto = $date->set_date1(date("Y-m-d")." 23:59:00")->explode()->to_js()->get();

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
    "date_from" => $datefrom,
    "date_to" => $dateto,
    "url_social" => "",
    "url_design" => "",
    "is_active" => "0",
    "invested" => "0.00",
    "returned" => "0.00",
    "notes" => "",

    "promotions" => $promotions,
    "notoryes" => $notoryes,
    "businessowners" => $businessowners,
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
<script type="module" src="/assets/js/restrict/promotions/insert.js"></script>