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

    "tr04" => _("Empty value is not allowed"),
    "tr05" => _("Invalid format. Eg: xxx@domain.com"),
    "tr06" => _("Invalid format. Eg: John Santino"),
    "tr07" => _("Invalid format. Eg: 777 888 999 333"),
    "tr08" => _("Invalid format. Eg: Smith Rincón"),
    "tr09" => _("Please select an option"),
    "tr10" => _("Invalid selection"),
    "tr11" => _("Invalid date"),
    "tr12" => _("Invalid format. Eg: St. Paul Street, 47 - N.Y."),
    "tr13" => _("Invalid value"),
    "tr14" => _("In order to finish your subscription you have to read and accept terms and conditions"),

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