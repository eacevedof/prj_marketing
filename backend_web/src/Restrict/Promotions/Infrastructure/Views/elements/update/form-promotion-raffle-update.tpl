<?php
use App\Shared\Infrastructure\Factories\ComponentFactory as CF;
use App\Shared\Infrastructure\Components\Date\DateComponent;
use App\Shared\Infrastructure\Helpers\RoutesHelper as Routes;

$promotion = $result["promotion"];
$raffle = $result["raffle"] ?? null;
if (is_null($raffle)) return;

$urlpost = Routes::url("promotion.raffle.update", ["uuid"=>$promotion["uuid"]]);
$date = CF::get(DateComponent::class);
$datefrom = $date->get_jsdt($promotion["date_from"]);
$dateto = $date->get_jsdt($promotion["date_to"]);
$dateexecution = $date->get_jsdt($promotion["date_execution"]);
$raffle["date_raffle"] = $date->get_jsdt($raffle["date_raffle"]);

$texts = [
    "tr00" => __("Run raffle"),
    "tr01" => __("Processing..."),
    "tr02" => __("Cancel"),
    "tr03" => __("Error"),
    "tr04" => __("<b>Data updated</b>"),
    "tr05" => __("Current time in"),
    "tr06" => __("Raffle time"),

    "f00" => __("Nº"),
    "f01" => __("Code"),
    "f02" => __("Name"),
    "f03" => __("Email"),
    "f04" => __("Phone"),
    "f05" => __("Action"),
];
?>
<div id="raffle" class="tab-pane">
  <form-promotion-raffle-update
      csrf=<?php $this->_echo_js($csrf);?>
      url="<?php $this->_echo($urlpost);?>"
      texts="<?php $this->_echo_jslit($texts);?>"

      fields="<?php $this->_echo_jslit($raffle);?>"
  />
</div>
<script type="module" src="/assets/js/restrict/promotions/form-promotion-raffle-update.js?r=3"></script>