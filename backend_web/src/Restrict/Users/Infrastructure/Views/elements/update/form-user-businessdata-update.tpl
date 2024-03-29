<?php
/**
 * @var App\Shared\Infrastructure\Views\AppView $this
 * @var array $result
 */
use App\Shared\Infrastructure\Helpers\RoutesHelper as Routes;
if (is_null($result["businessdata"])) return;

$idUser = $result["user"]["id"];
$businessdata = $result["businessdata"];

$texts = [
    "tr00" => __("Save"),
    "tr01" => __("Processing..."),
    "tr02" => __("Cancel"),
    "tr03" => __("Error"),
    "tr04" => __("<b>Data updated</b>"),
    "tr05" => __("No editable"),

    "f00" => __("Nº"),
    "f01" => __("uuid"),
    "f02" => __("User"),

    "f03" => __("Business name"),
    "f04" => __("Slug"),
    "f05" => __("Url logo sm"),
    "f06" => __("Url logo md"),
    "f07" => __("Url logo lg"),
    "f08" => __("Url favicon"),
    "f09" => __("Head bg color"),
    "f10" => __("Head color"),
    "f11" => __("Head bg image"),
    "f12" => __("Body bg color"),
    "f13" => __("Body color"),
    "f14" => __("Url body bg image"),
    "f15" => __("Url site"),
    "f16" => __("Url Facebook"),
    "f17" => __("Url Instagram"),
    "f18" => __("Url Twitter"),
    "f19" => __("Url TikTok"),
    "f50" => __("Space test"),
    "f51" => __("Timezone"),
];

$businessdata = [
    "id_user" => $idUser,

    "id" => $businessdata["id"] ?? "",
    "uuid" => $businessdata["uuid"] ?? "",

    "id_tz" => $businessdata["id_tz"] ?? "",
    "business_name" => $businessdata["business_name"] ?? "",
    "slug" => $businessdata["slug"] ?? "",
    "user_logo_1" => $businessdata["user_logo_1"] ?? "",
    "user_logo_2" => $businessdata["user_logo_2"] ?? "",
    "user_logo_3" => $businessdata["user_logo_3"] ?? "",
    "url_favicon" => $businessdata["url_favicon"] ?? "",
    "head_bgcolor" => $businessdata["head_bgcolor"] ?? "#ffffff",
    "head_color" => $businessdata["head_color"] ?? "#ffffff",
    "head_bgimage" => $businessdata["head_bgimage"] ?? "",
    "body_bgcolor" => $businessdata["body_bgcolor"] ?? "#ffffff",
    "body_color" => $businessdata["body_color"] ?? "#ffffff",
    "body_bgimage" => $businessdata["body_bgimage"] ?? "",
    "url_business" => $businessdata["url_business"] ?? "",
    "url_social_fb" => $businessdata["url_social_fb"] ?? "",
    "url_social_ig" => $businessdata["url_social_ig"] ?? "",
    "url_social_twitter" => $businessdata["url_social_twitter"] ?? "",
    "url_social_tiktok" => $businessdata["url_social_tiktok"] ?? "",

    "spaceurl" => Routes::getUrlByRouteName("business.space", ["businessSlug" =>$businessdata["slug"] ?? ""]),
    "timezones" => $timezones,
];
?>
<div id="businessdata" class="tab-pane">
    <form-user-businessdata-update
        csrf=<?php $this->_echoJs($csrf);?>

        useruuid="<?=$uuid?>"
        texts="<?php $this->_echoJsLit($texts);?>"

        fields="<?php $this->_echoJsLit($businessdata);?>"
    />
</div>
<script type="module" src="/assets/js/restrict/users/businessdata/form-user-businessdata-update.js"></script>
