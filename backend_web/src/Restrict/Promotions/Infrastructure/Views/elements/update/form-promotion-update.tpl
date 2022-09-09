<?php
use App\Shared\Infrastructure\Factories\ComponentFactory as CF;
use App\Shared\Infrastructure\Components\Date\DateComponent;

$promotion = $result["promotion"];

$date = CF::get(DateComponent::class);
$datefrom = $date->get_jsdt($promotion["date_from"]);
$dateto = $date->get_jsdt($promotion["date_to"]);
$dateexecution = $date->get_jsdt($promotion["date_execution"]);

$texts = [
    "tr00" => __("Save"),
    "tr01" => __("Processing..."),
    "tr02" => __("Cancel"),
    "tr03" => __("Error"),
    "tr04" => __("<b>Data updated</b>"),
    "tr05" => __("Current time in"),

    "f00" => __("Nº"),
    "f01" => __("Code"),
    "f02" => __("Owner"),
    "f03" => __("Timezone"),
    "f04" => __("External code"),
    "f05" => __("Description"),
    "f06" => __("Slug"),
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
    "f30" => __("Date limit"),
    "f31" => __("Date raffle"),
];

$promotion = [
    "id" => $promotion["id"],
    "uuid" => $promotion["uuid"],
    "id_owner" => $promotion["id_owner"],
    "id_tz" => $promotion["id_tz"],
    "code_erp" => $promotion["code_erp"],
    "description" => $promotion["description"],
    "slug" => $promotion["slug"],
    "date_from" => $datefrom,
    "date_to" => $dateto,
    "date_execution" => $dateexecution,
    "content" => $promotion["content"],
    "bgcolor" => $promotion["bgcolor"],
    "bgimage_xs" => $promotion["bgimage_xs"],
    "bgimage_sm" => $promotion["bgimage_sm"],
    "bgimage_md" => $promotion["bgimage_md"],
    "bgimage_lg" => $promotion["bgimage_lg"],
    "bgimage_xl" => $promotion["bgimage_xl"],
    "bgimage_xxl" => $promotion["bgimage_xxl"],
    "invested" => $promotion["invested"],
    "returned" => $promotion["returned"],
    "max_confirmed" => $promotion["max_confirmed"],
    "is_raffleable" => $promotion["is_raffleable"],
    "date_raffle" => $dateto,
    "is_cumulative" => $promotion["is_cumulative"],
    "is_published" => $promotion["is_published"],
    "is_launched" => $promotion["is_launched"],
    "tags" => $promotion["tags"],
    "notes" => $promotion["notes"],
    "disabled_date" => $promotion["disabled_date"],
    "disabled_user" => $promotion["disabled_user"],
    "disabled_reason" => $promotion["disabled_reason"],
    "num_viewed" => $promotion["num_viewed"],
    "num_subscribed" => $promotion["num_subscribed"],
    "num_confirmed" => $promotion["num_confirmed"],
    "num_executed" => $promotion["num_executed"],


    "promotionlink" => $promotion["promotionlink"],
    "timezones" => $timezones ?? [],
    "notoryes" => $notoryes,
    "businessowners" => $businessowners,
];
//dd($promotion);
?>
<div id="main" class="tab-pane active">
  <form-promotion-update
      csrf=<?php $this->_echo_js($csrf);?>

      texts="<?php $this->_echo_jslit($texts);?>"

      fields="<?php $this->_echo_jslit($promotion);?>"
  />
</div>
<script type="module" src="/assets/js/restrict/promotions/form-promotion-update.js"></script>
