<?php
/**
 * @var App\Shared\Infrastructure\Views\AppView $this
 * @var array $promotionui
 * @var string $promotionSlug
 */
use App\Shared\Infrastructure\Helpers\RoutesHelper as Routes;

$url = Routes::getUrlByRouteName("terms.by-promotion", ["promotionSlug" => $promotionSlug]);
$businessSlug = $result["businessdata"]["slug"] ?? "";
$texts = [
    "tr00" => __("Subscribe"),
    "tr01" => __("Processing..."),
    "tr02" => __("Error"),
    "tr03" => __("Some unexpected error occurred"),
    "tr04" => __("Please check form errors"),

    "tr10" => __("This field cannot be left blank"),
    "tr11" => __("Invalid format. Eg: xxx@domain.com"),
    "tr12" => __("Invalid format. Eg: John Santino"),
    "tr13" => __("Invalid format. Eg: 777 888 999 333"),
    "tr14" => __("Invalid format. Eg: Smith Rincón"),
    "tr15" => __("Please select an option"),
    "tr16" => __("Invalid selection"),
    "tr17" => __("Invalid date"),
    "tr18" => __("Invalid format. Eg: St. Paul Street, 47 - N.Y."),
    "tr19" => __("Invalid value"),
    "tr20" => __("In order to finish your subscription you have to read and accept terms and conditions."),

    "tr30" => __("Thank you <b>%name%</b> for your subscription. Please check your email <b>%email%</b> and click on the confirmation link.<br/><small>May be you have to check your spam folder</small>"),

    "email" => __("Email"),
    "name1" => __("First name"),
    "name2" => __("Last name"),
    "address" => __("Address"),
    "birthdate" => __("Birthdate"),
    "phone1" => __("Phone"),
    "language" => __("Language"),
    "country" => __("Country"),
    "gender" => __("Gender"),
    "is_mailing" => __("I would like to receive information about similar promotions, raffles and special discounts in my email."),
    "is_terms" => __(
            "I have read and accept the <a href=\"{0}\" target=\"_blank\">promotion and general</a> terms and conditions",$url
    ),
];
$result = [
    "inputs" => $uihelp->getInputs(),
    "languages" => $languages,
    "countries" => $countries,
    "genders" => $genders,
];
//dd($genders);
?>
<form-promotion-cap-insert
    businessslug="<?=$businessSlug?>"
    promotionuuid="<?=$promotionuuid?>"
    texts="<?php $this->_echoJsLit($texts);?>"
    fields="<?php $this->_echoJsLit($result);?>"
/>
<script type="module" src="/assets/js/open/promotioncap/form-promotion-cap-insert.js"></script>