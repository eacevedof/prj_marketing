<?php
$subscription = $result["subscription"];

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
];

$subscription = [
  "description" => $subscription["description"],
];

?>
<div id="main" class="tab-pane active">
    <form-subscription-update
        csrf=<?$this->_echo_js($csrf);?>

        texts="<?$this->_echo_jslit($texts);?>"

        fields="<?$this->_echo_jslit($subscription);?>"
    />
</div>

