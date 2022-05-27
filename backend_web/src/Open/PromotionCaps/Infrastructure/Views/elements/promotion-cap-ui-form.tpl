<?php
/**
 * @var App\Shared\Infrastructure\Views\AppView $this
 * @var array $promotionui
 */

$texts = [
    "tr00" => __("Subscribe"),
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
    "is_mailing" => __("I would like to receive promotions and raffles in my email"),
    "is_terms" => __("I have red and accept legal terms and conditions"),
];

$result = [
    "inputs" => $uihelp->get_inputs(),
    "languages" => $languages,
    "countries" => $countries,
    "genders" => $genders,
];
?>
<form-promotion-cap-insert
    promotionuuid="<?=$promotionuuid?>"
    texts="<?$this->_echo_jslit($texts);?>"
    fields="<?$this->_echo_jslit($result);?>"
/>
<script type="module" src="/assets/js/open/promotioncap/insert.js"></script>