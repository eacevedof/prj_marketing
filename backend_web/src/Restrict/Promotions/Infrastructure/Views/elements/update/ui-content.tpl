<?php
/**
 * @var App\Shared\Infrastructure\Views\AppView $this
 * @var array $result
 */
if (is_null($result["promotionui"])) return;
$promotionui = $result["promotionui"];

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

$promotionui = [
    "id" => $promotionui["id"] ?? "",
    "uuid" => $promotionui["uuid"] ?? "",
    "id_owner" => $promotionui["id_owner"] ?? "",
    "code_erp" => $promotionui["code_erp"] ?? "",
    "description" => $promotionui["description"] ?? "",
    "id_promotion" => $promotionui["id_promotion"] ?? "",
    "input_email" => $promotionui["input_email"] ?? "",
    "pos_email" => $promotionui["pos_email"] ?? "",
    "input_name1" => $promotionui["input_name1"] ?? "",
    "pos_name1" => $promotionui["pos_name1"] ?? "",
    "input_name2" => $promotionui["input_name2"] ?? "",
    "pos_name2" => $promotionui["pos_name2"] ?? "",
    "input_language" => $promotionui["input_language"] ?? "",
    "pos_language" => $promotionui["pos_language"] ?? "",
    "input_country" => $promotionui["input_country"] ?? "",
    "pos_country" => $promotionui["pos_country"] ?? "",
    "input_phone1" => $promotionui["input_phone1"] ?? "",
    "pos_phone1" => $promotionui["pos_phone1"] ?? "",
    "input_birthdate" => $promotionui["input_birthdate"] ?? "",
    "pos_birthdate" => $promotionui["pos_birthdate"] ?? "",
    "input_gender" => $promotionui["input_gender"] ?? "",
    "pos_gender" => $promotionui["pos_gender"] ?? "",
    "input_address" => $promotionui["input_address"] ?? "",
    "pos_address" => $promotionui["pos_address"] ?? "",

    "notoryes" => $notoryes,
];
?>
<div id="main" class="tab-pane active">
  <form-promotion-ui-update
      csrf=<?$this->_echo_js($csrf);?>

      texts="<?$this->_echo_jslit($texts);?>"

      fields="<?$this->_echo_jslit($promotionui);?>"
  />
</div>

