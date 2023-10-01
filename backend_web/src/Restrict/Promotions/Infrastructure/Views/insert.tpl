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
$utcfrom = date("Y-m-d H:i:s");

$tzto = $authUser["tz"];
$dtfrom = ($tzfrom !== $tzto) ? CF::getInstanceOf(UtcComponent::class)->getSourceDtIntoTargetTz($utcfrom, $tzfrom, $tzto) : $utcfrom;

$dt = CF::getInstanceOf(DateComponent::class);
$dtfrom = $dt->getDateInJsFormat($dtfrom);
$dtto = $dt->getLastSecondInSomeDate($dtfrom);

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
    "f09" => __("Terms and conditions"),
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
    "id_tz" => $authUser["id_tz"],
    "code_erp" => "",
    "description" => "",
    "date_from" => $dtfrom,
    "date_to" => $dtto,
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
      csrf=<?php $this->_echoJs($csrf);?>

      texts="<?php $this->_echoJsLit($texts);?>"

      fields="<?php $this->_echoJsLit($result);?>"
    />
  </div>
</div>
<script type="module" src="/assets/js/restrict/promotions/form-promotion-insert.js"></script>