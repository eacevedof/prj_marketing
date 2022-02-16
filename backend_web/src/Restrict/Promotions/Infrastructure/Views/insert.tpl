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
    "f06" => __("Date from"),
    "f07" => __("Date to"),
    "f08" => __("Content"),
    "f09" => __("Bg color"),
    "f10" => __("Bg image xs"),
    "f11" => __("Bg image sm"),
    "f12" => __("Bg image md"),
    "f13" => __("Bg image lg"),
    "f14" => __("Bg image xl"),
    "f15" => __("Bg image xxl"),
    "f16" => __("Enabled"),
    "f17" => __("Invested"),
    "f18" => __("Inv returned"),
    "f19" => __("Max confirmed"),
    "f20" => __("Notes"),
];

$result = [
    "id_owner" => "",
    "code_erp" => "",
    "description" => "",
    "slug" => "",
    "date_from" => $datefrom,
    "date_to" => $dateto,
    "content" => "",
    "bgcolor" => "",
    "bgimage_xs" => "",
    "bgimage_sm" => "",
    "bgimage_md" => "",
    "bgimage_lg" => "",
    "bgimage_xl" => "",
    "bgimage_xxl" => "",
    "is_active" => "0",
    "invested" => "0.00",
    "returned" => "0.00",
    "max_confirmed" => "",
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