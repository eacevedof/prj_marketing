<?php
/**
 * @var App\Shared\Infrastructure\Views\AppView $this
 * @var App\Shared\Infrastructure\Components\Date\DateComponent $date;
 * @var string $h1
 * @var string $csrf
 * @var array $businessowners
 * @var array $timezones
 * @var array $notoryes
 */

use App\Shared\Infrastructure\Factories\ComponentFactory as CF;
use App\Shared\Infrastructure\Components\Date\DateComponent;
use App\Shared\Infrastructure\Components\Date\UtcComponent;
$tzfrom = date_default_timezone_get();
$tzto = $authuser["tz"];

$datefrom = date("Y-m-d H:i:s");
$dateto = date("Y-m-d")." 23:59:59";

if ($tzfrom !== $tzto) {
  $utc = CF::get(UtcComponent::class);
  $datefrom = $utc->get_dt_into_tz($datefrom, $tzfrom, $tzto);
  $dateto = $utc->get_dt_into_tz($datefrom, $tzfrom, $tzto);
}

$date = CF::get(DateComponent::class);
$datefrom = $date->get_jsdt($datefrom);
$dateto = $date->get_jsdt($dateto);

$texts = [
    "tr00" => __("Save"),
    "tr01" => __("Processing..."),
    "tr02" => __("Cancel"),
    "tr03" => __("Error"),
    "tr04" => __("<b>Data created</b>"),

    "f00" => __("Nº"),
    "f01" => __("Code"),
    "f02" => __("Owner"),
    "f03" => __("Timezone"),
    "f04" => __("External code"),
    "f05" => __("Description"),
    "f07" => __("Date from"),
    "f08" => __("Date to"),
    "f09" => __("Content"),
    "f10" => __("Bg color"),
    "f11" => __("Bg image xs"),
    "f12" => __("Bg image sm"),
    "f13" => __("Bg image md"),
    "f14" => __("Bg image lg"),
    "f15" => __("Bg image xl"),
    "f16" => __("Bg image xxl"),
    "f19" => __("Max. confirmed"),
    "f20" => __("Raffleable"),
    "f21" => __("Cumulative"),
    "f22" => __("Tags"),
    "f23" => __("Notes"),
];

$result = [
    "id_owner" => "",
    "id_tz" => $tzto,
    "code_erp" => "",
    "description" => "",
    "date_from" => $datefrom,
    "date_to" => $dateto,
    "content" => "",
    "bgcolor" => "#ffffff",
    "bgimage_xs" => "",
    "bgimage_sm" => "",
    "bgimage_md" => "",
    "bgimage_lg" => "",
    "bgimage_xl" => "",
    "bgimage_xxl" => "",
    "max_confirmed" => 0,
    "is_raffleable" => 0,
    "is_cumulative" => 0,
    "tags" => "",
    "notes" => "",

    "timezones" => $timezones ?? [],
    "notoryes" => $notoryes,
    "businessowners" => $businessowners,
];
?>
<div class="modal-form">
  <div class="card-header">
    <h4 class="card-title mb-1"><?=$h1?></h4>
  </div>
  <div class="card-body pt-0">
    <form-promotion-insert
      csrf=<?$this->_echo_js($csrf);?>

      texts="<?$this->_echo_jslit($texts);?>"

      fields="<?$this->_echo_jslit($result);?>"
    />
  </div>
</div>
<script type="module" src="/assets/js/restrict/promotions/insert.js"></script>