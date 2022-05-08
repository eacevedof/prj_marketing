<?php
$texts = [
  "tr00" => __("Save"),
  "tr01" => __("Processing..."),
  "tr02" => __("Cancel"),
  "tr03" => __("Error"),
  "tr04" => __("<b>Data updated</b>"),

  "f00" => "",
];
$subscription = $result["subscription"];
$subscription = [
  "subs_status" => $subscription["subs_status"],
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
  <form-subscription-update
      csrf=<?$this->_echo_js($csrf);?>

      texts="<?$this->_echo_jslit($texts);?>"

      fields="<?$this->_echo_jslit($subscription);?>"
  />
</div>

