<?php
use App\Open\PromotionCaps\Domain\Enums\PromotionCapActionType as Status;

$texts = [
  "tr00" => __("Save"),
  "tr01" => __("Processing..."),
  "tr02" => __("Cancel"),
  "tr03" => __("Error"),
  "tr04" => __("<b>Data updated</b>"),

  "f00" => "",
];
$subscription = $result["subscription"];
$status = $subscription["subs_status"];

$validable = "";
switch ($status) {
  case Status::CONFIRMED: $validable = "<p><b>".__("Already confirmed")."</b></p>"; break;
  case Status::CANCELLED: $validable = "<p><b>".__("Cancelled")."</b></p>"; break;
}

$subscription = [
  "subs_status" => $status,
  "capuseruuid" => $subscription["e_usercode"],
  "exec_code" => "",
];

?>
<div id="main" class="tab-pane active">
  <h4><?=__("Vaucher code validation")?></h4>
  <br>
  <ul>
    <li><?=__("Business")?>: <?$this->_echo($result["subscription"]["e_business"])?></li>
    <li><?=__("Promotion")?>: <?$this->_echo($result["subscription"]["e_promotion"])?></li>
    <li><?=__("For")?>: <?$this->_echo($result["subscription"]["e_username"] . " / " . $result["subscription"]["e_usercode"])?></li>
  </ul>
  <?php
  if ($validable):
    echo $validable;
  else:
  ?>
  <form-subscription-update
      csrf=<?$this->_echo_js($csrf);?>

      texts="<?$this->_echo_jslit($texts);?>"

      fields="<?$this->_echo_jslit($subscription);?>"
  />
  <?php
  endif;
  ?>
</div>

