<?php
/**
 * @var App\Shared\Infrastructure\Views\AppView $this
 * @var array $result
 */
if (is_null($result["promotion"])) return;
$promotion = $result["promotion"];
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
    "f05" => __("Promotion"),

    "f06" => __("Email"),
    //"f07" => __("Email position"),
    "f08" => __("Fist name"),
    //"f09" => __("Pos. Name"),
    "f10" => __("Last name"),
    //"f11" => __("Pos. Last name"),
    "f12" => __("Language"),
    //"f13" => __("Pos. Language"),
    "f14" => __("Country"),
    //"f15" => __("Pos. Country"),
    "f16" => __("Mobile"),
    //"f17" => __("Pos. Phone"),
    "f18" => __("Birthday"),
    //"f19" => __("Pos. Birthdate"),
    "f20" => __("Gender"),
    //"f21" => __("Pos. Gender"),
    "f22" => __("Address"),
    //"f23" => __("Pos. Address"),
    "f24" => __("I would like to receive promotions and raffles in my email"),
    "f26" => __("I have red and accept legal terms and conditions"),
];

$promotion = [
    "id" => $promotion["id"] ?? "",
    "uuid" => $promotion["uuid"] ?? "",
    "id_owner" => $promotion["id_owner"] ?? "",
    "id_promotion" => $promotion["id_promotion"] ?? $result["promotion"]["id"],

    "input_email" => $promotion["input_email"] ?? "1",
    "pos_email" => $promotion["pos_email"] ?? 10,

    "input_name1" => $promotion["input_name1"] ?? "1",
    "pos_name1" => $promotion["pos_name1"] ?? 20,

    "input_phone1" => $promotion["input_phone1"] ?? "0",
    "pos_phone1" => $promotion["pos_phone1"] ?? 30,

    "input_name2" => $promotion["input_name2"] ?? "0",
    "pos_name2" => $promotion["pos_name2"] ?? 40,

    "input_language" => $promotion["input_language"] ?? "0",
    "pos_language" => $promotion["pos_language"] ?? 50,

    "input_country" => $promotion["input_country"] ?? "0",
    "pos_country" => $promotion["pos_country"] ?? 60,

    "input_birthdate" => $promotion["input_birthdate"] ?? "0",
    "pos_birthdate" => $promotion["pos_birthdate"] ?? 70,

    "input_gender" => $promotion["input_gender"] ?? "0",
    "pos_gender" => $promotion["pos_gender"] ?? 80,

    "input_address" => $promotion["input_address"] ?? "0",
    "pos_address" => $promotion["pos_address"] ?? 90,

    "input_is_mailing" => $promotion["input_is_mailing"] ?? "0",
    "pos_is_mailing" => $promotion["pos_is_mailing"] ?? 100,

    "input_is_terms" => $promotion["input_is_terms"] ?? "0",
    "pos_is_terms" => $promotion["pos_is_terms"] ?? 110,

    "notoryes" => $notoryes,

    "disabled_date" => $result["promotion"]["disabled_date"],
];
?>
<div id="ui" class="tab-pane mb-2">
  <form-promotion-ui-update
      csrf=<?php $this->_echo_js($csrf);?>
      promotionuuid=<?php $this->_echo_js($result["promotion"]["uuid"]);?>
      iseditable=<?php $this->_echo_js((int)$result["promotion"]["is_editable"]);?>

      texts="<?php $this->_echo_jslit($texts);?>"

      fields="<?php $this->_echo_jslit($promotion);?>"
  />
</div>
<script type="module" src="/assets/js/restrict/promotions/form-promotion-ui-update.js"></script>
