<?php
/**
 * @var App\Shared\Infrastructure\Views\AppView $this
 * @var array $result
 */
//if (is_null($result["promotionui"] ?? null)) return;
$promotion = $result["promotionui"];

$texts = [
    "tr00" => __("Save"),
    "tr01" => __("Processing..."),
    "tr02" => __("Cancel"),
    "tr03" => __("Error"),
    "tr04" => __("<b>Data updated</b>"),

    "f00" => __("tr_id"),
    "f01" => __("tr_uuid"),
    "f02" => __("tr_id_owner"),
    "f03" => __("tr_code_erp"),
    "f04" => __("tr_description"),
    "f05" => __("tr_id_promotion"),
    "f06" => __("tr_input_email"),
    "f07" => __("tr_pos_email"),
    "f08" => __("tr_input_name1"),
    "f09" => __("tr_pos_name1"),
    "f10" => __("tr_input_name2"),
    "f11" => __("tr_pos_name2"),
    "f12" => __("tr_input_language"),
    "f13" => __("tr_pos_language"),
    "f14" => __("tr_input_country"),
    "f15" => __("tr_pos_country"),
    "f16" => __("tr_input_phone1"),
    "f17" => __("tr_pos_phone1"),
    "f18" => __("tr_input_birthdate"),
    "f19" => __("tr_pos_birthdate"),
    "f20" => __("tr_input_gender"),
    "f21" => __("tr_pos_gender"),
    "f22" => __("tr_input_address"),
    "f23" => __("tr_pos_address"),
];


$result = [
    "id" => $result["id"],
    "uuid" => $result["uuid"],
    "id_owner" => $result["id_owner"],
    "code_erp" => $result["code_erp"],
    "description" => $result["description"],
    "id_promotion" => $result["id_promotion"],
    "input_email" => $result["input_email"],
    "pos_email" => $result["pos_email"],
    "input_name1" => $result["input_name1"],
    "pos_name1" => $result["pos_name1"],
    "input_name2" => $result["input_name2"],
    "pos_name2" => $result["pos_name2"],
    "input_language" => $result["input_language"],
    "pos_language" => $result["pos_language"],
    "input_country" => $result["input_country"],
    "pos_country" => $result["pos_country"],
    "input_phone1" => $result["input_phone1"],
    "pos_phone1" => $result["pos_phone1"],
    "input_birthdate" => $result["input_birthdate"],
    "pos_birthdate" => $result["pos_birthdate"],
    "input_gender" => $result["input_gender"],
    "pos_gender" => $result["pos_gender"],
    "input_address" => $result["input_address"],
    "pos_address" => $result["pos_address"],

    "notoryes" => $notoryes,
];
?>
<div id="main" class="tab-pane active">
  <form-promotion-ui-update
      csrf=<?$this->_echo_js($csrf);?>

      texts="<?$this->_echo_jslit($texts);?>"

      fields="<?$this->_echo_jslit($result);?>"
  />
</div>

