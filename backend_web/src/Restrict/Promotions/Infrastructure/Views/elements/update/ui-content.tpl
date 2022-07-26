<?php
/**
 * @var App\Shared\Infrastructure\Views\AppView $this
 * @var array $result
 */
if (is_null($result["promotionui"])) return;
$promotionui = $result["promotionui"];
//dd($promotionui);
$texts = [
    "tr00" => __("Save"),
    "tr01" => __("Processing..."),
    "tr02" => __("Cancel"),
    "tr03" => __("Error"),
    "tr04" => __("<b>Data updated</b>"),
    "tr05" => __("Field"),
    "tr06" => __("Enabled"),
    "tr07" => __("Position"),

    "f00" => __("Nº"),
    "f01" => __("Code"),
    "f02" => __("Owner"),
    "f03" => __("External code"),
    "f04" => __("Description"),
    "f05" => __("tr_id_promotion"),

    "f06" => __("Email"),
    //"f07" => __("tr_pos_email"),
    "f08" => __("Fist name"),
    //"f09" => __("tr_pos_name1"),
    "f10" => __("Last name"),
    //"f11" => __("tr_pos_name2"),
    "f12" => __("Language"),
    //"f13" => __("tr_pos_language"),
    "f14" => __("Country"),
    //"f15" => __("tr_pos_country"),
    "f16" => __("Mobile"),
    //"f17" => __("tr_pos_phone1"),
    "f18" => __("Birthday"),
    //"f19" => __("tr_pos_birthdate"),
    "f20" => __("Gender"),
    //"f21" => __("tr_pos_gender"),
    "f22" => __("Address"),
    //"f23" => __("tr_pos_address"),
    "f24" => __("I would like to receive promotions and raffles in my email"),
    "f26" => __("I have red and accept legal terms and conditions"),
];

$promotionui = [
    "id" => $promotionui["id"] ?? "",
    "uuid" => $promotionui["uuid"] ?? "",
    "id_owner" => $promotionui["id_owner"] ?? "",
    "id_promotion" => $promotionui["id_promotion"] ?? $result["promotion"]["id"],

    "input_email" => $promotionui["input_email"] ?? "1",
    "pos_email" => $promotionui["pos_email"] ?? 10,

    "input_name1" => $promotionui["input_name1"] ?? "1",
    "pos_name1" => $promotionui["pos_name1"] ?? 20,

    "input_phone1" => $promotionui["input_phone1"] ?? "0",
    "pos_phone1" => $promotionui["pos_phone1"] ?? 30,

    "input_name2" => $promotionui["input_name2"] ?? "0",
    "pos_name2" => $promotionui["pos_name2"] ?? 40,

    "input_language" => $promotionui["input_language"] ?? "0",
    "pos_language" => $promotionui["pos_language"] ?? 50,

    "input_country" => $promotionui["input_country"] ?? "0",
    "pos_country" => $promotionui["pos_country"] ?? 60,

    "input_birthdate" => $promotionui["input_birthdate"] ?? "0",
    "pos_birthdate" => $promotionui["pos_birthdate"] ?? 70,

    "input_gender" => $promotionui["input_gender"] ?? "0",
    "pos_gender" => $promotionui["pos_gender"] ?? 80,

    "input_address" => $promotionui["input_address"] ?? "0",
    "pos_address" => $promotionui["pos_address"] ?? 90,

    "input_is_mailing" => $promotionui["input_is_mailing"] ?? "0",
    "pos_is_mailing" => $promotionui["pos_is_mailing"] ?? 100,

    "input_is_terms" => $promotionui["input_is_terms"] ?? "0",
    "pos_is_terms" => $promotionui["pos_is_terms"] ?? 110,

    "notoryes" => $notoryes,

    "disabled_date" => $result["promotion"]["disabled_date"],
];
//dd($notoryes);
?>
<div id="ui" class="tab-pane mb-2">
  <!--assets/js/restrict/promotions/ui/update.js-->
  <form-promotion-ui-update
      csrf=<?php $this->_echo_js($csrf);?>
      promotionuuid=<?php $this->_echo_js($result["promotion"]["uuid"]);?>
      promoislaunched=<?php $this->_echo_js($result["promotion"]["is_launched"]);?>

      texts="<?php $this->_echo_jslit($texts);?>"

      fields="<?php $this->_echo_jslit($promotionui);?>"
  />
</div>

