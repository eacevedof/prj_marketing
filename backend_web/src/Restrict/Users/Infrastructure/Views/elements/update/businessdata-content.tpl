<?php
/**
 * @var App\Shared\Infrastructure\Views\AppView $this
 * @var array $result
 */
if (is_null($result["businessdata"])) return;

$databusinessdata = [
    "id_user" => $result["id"] ?? "",

    "id" => $businessdata["id"] ?? "",
    "uuid" => $businessdata["uuid"] ?? "",

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
];
?>
<div id="businessdata" class="tab-pane">
    <form-user-businessdata-update
        csrf=<?$this->_echo_js($csrf);?>

        useruuid="<?=$uuid?>"
        texts="<?$this->_echo_jslit($textbusinessdata);?>"

        fields="<?$this->_echo_jslit($databusinessdata);?>"
    />
</div>

