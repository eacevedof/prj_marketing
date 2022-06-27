<?php
/**
 * @var App\Shared\Infrastructure\Views\AppView $this
 * @var array $result
 */
if (is_null($result["ui"])) return;

$texts = [
    "tr00" => __("Save"),
    "tr01" => __("Processing..."),
    "tr02" => __("Cancel"),
    "tr03" => __("Error"),
    "tr04" => __("<b>Data updated</b>"),

    "f00" => __("Nº"),
    "f01" => __("Code"),
    "f02" => __("Owner"),
    "f03" => __("External code"),
    "f04" => __("Description"),
    "f05" => __("Promotion"),
    "f06" => __("Email"),
    "f07" => __("Email position"),
    "f08" => __("First name"),
    "f09" => __("First name position"),
    "f10" => __("Last name"),
    "f11" => __("Last name position"),
    "f12" => __("Language"),
    "f13" => __("Language position"),
    "f14" => __("Country"),
    "f15" => __("Country position"),
    "f16" => __("Mobile number"),
    "f17" => __("Mobile number position"),
    "f18" => __("Birthdate"),
    "f19" => __("Birthdate position"),
    "f20" => __("Gender"),
    "f21" => __("Gender position"),
    "f22" => __("Address"),
    "f23" => __("Address position"),
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
];
?>
<div id="ui" class="tab-pane">
  <ol>
    <?php
    $ui = $result["ui"] ?? [];
    foreach ($ui as $arvalue):
      ?>
      <li><b><?php $this->_echo($arvalue["pref_key"]);?>:</b>&nbsp;&nbsp;<span><?php $this->_echo($arvalue["pref_value"]);?></span></li>
    <?php
    endforeach;
    ?>
  </ol>
</div><!--ui-->
