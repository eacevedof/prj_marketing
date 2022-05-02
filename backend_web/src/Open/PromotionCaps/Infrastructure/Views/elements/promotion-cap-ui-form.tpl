<?php
/**
 * @var App\Shared\Infrastructure\Views\AppView $this
 * @var array $promotionui
 */

$mapped = [];
foreach ($promotionui as $field => $value) {
    $parts = explode("_", $field);
    $prefix = $parts[0];
    if ($prefix!=="input") continue;
    if (!$value) continue;
    $input = $parts[1];
    $mapped[$input] = $promotionui["pos_$input"];
}
asort($mapped);
$mapped = array_keys($mapped);

$texts = [
    "tr00" => __("Save"),
    "tr01" => __("Processing..."),
    "tr02" => __("Error"),
    "tr03" => __("Some unexpected error occurred"),

    "email" => __("Email"),
    "name1" => __("First name"),
    "name2" => __("Last name"),
    "address" => __("Address"),
    "birthdate" => __("Birthdate"),
    "phone1" => __("Phone"),
    "language" => __("Language"),
    "country" => __("Country"),
    "gender" => __("Gender"),
];

$result = [
    "inputs" => $mapped,

    "languages" => $languages,
    "countries" => $countries,
    "genders" => $genders,
];
?>
<div class="card-body pt-0">
    <form-promotion-cap-insert
        promotionuuid="<?=$promotionuuid?>"
        texts="<?$this->_echo_jslit($texts);?>"
        fields="<?$this->_echo_jslit($result);?>"
    />
</div>
<script type="module" src="/assets/js/open/promotionscap/insert.js"></script>