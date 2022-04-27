<?php
use App\Shared\Infrastructure\Factories\ComponentFactory as CF;
use App\Shared\Infrastructure\Components\Date\DateComponent;
$date = CF::get(DateComponent::class);
$datefrom = $date->get_jsdt($result["date_from"]);
$dateto = $date->get_jsdt($result["date_to"]);

$texts = [
    "tr00" => __("Save"),
    "tr01" => __("Processing..."),
    "tr02" => __("Cancel"),
    "tr03" => __("Error"),
    "tr04" => __("<b>Data updated</b>"),

    "f00" => __("Nº"),
    "f01" => __("Code"),
    "f02" => __("Owner"),
    "f03" => __("Timezone"),
    "f04" => __("External code"),
    "f05" => __("Description"),
    "f06" => __("Slug"),
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
    "f17" => __("Invested"),
    "f18" => __("Inv returned"),
    "f19" => __("Max. confirmed"),
    "f20" => __("Raffleable"),
    "f21" => __("Cumulative"),
    "f22" => __("Tags"),
    "f23" => __("Notes"),
    "f24" => __("Viewed"),
    "f25" => __("Subscribed"),
    "f26" => __("Confirmed"),
    "f27" => __("Executed"),
    "f28" => __("Published"),
    "f29" => __("Launched"),
];

$result = [
    "id" => $result["id"],
    "uuid" => $result["uuid"],
    "id_owner" => $result["id_owner"],
    "id_tz" => $result["id_tz"],
    "code_erp" => $result["code_erp"],
    "description" => $result["description"],
    "slug" => $result["slug"],
    "date_from" => $datefrom,
    "date_to" => $dateto,
    "content" => $result["content"],
    "bgcolor" => $result["bgcolor"],
    "bgimage_xs" => $result["bgimage_xs"],
    "bgimage_sm" => $result["bgimage_sm"],
    "bgimage_md" => $result["bgimage_md"],
    "bgimage_lg" => $result["bgimage_lg"],
    "bgimage_xl" => $result["bgimage_xl"],
    "bgimage_xxl" => $result["bgimage_xxl"],
    "invested" => $result["invested"],
    "returned" => $result["returned"],
    "max_confirmed" => $result["max_confirmed"],
    "is_raffleable" => $result["is_raffleable"],
    "is_cumulative" => $result["is_cumulative"],
    "is_published" => $result["is_published"],
    "is_launched" => $result["is_launched"],
    "tags" => $result["tags"],
    "notes" => $result["notes"],

    "businessslug" => $businessslug,
    "timezones" => $timezones ?? [],
    "notoryes" => $notoryes,
    "businessowners" => $businessowners,
];
//dd($result);
?>
<div id="main" class="tab-pane active">
  <form-promotion-update
      csrf=<?$this->_echo_js($csrf);?>

      texts="<?$this->_echo_jslit($texts);?>"

      fields="<?$this->_echo_jslit($result);?>"
  />
</div>

