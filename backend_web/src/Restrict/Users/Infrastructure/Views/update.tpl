<?php
/**
 * @var \App\Shared\Infrastructure\Views\AppView $this
 * @var array $result
 * @var string $uuid
 *
 * @var array $permissions
 * @var array $businessdata
 */
use App\Restrict\Users\Domain\Enums\UserProfileType;
$isbow = $result["id_profile"] === UserProfileType::BUSINESS_OWNER;

$textuser = [
    "tr00" => __("Save"),
    "tr01" => __("Processing..."),
    "tr02" => __("Cancel"),
    "tr03" => __("Error"),
    "tr04" => __("<b>Data updated</b>"),

    "f00" => __("Email"),
    "f01" => __("Password"),
    "f02" => __("Password confirm"),
    "f03" => __("Full name"),
    "f04" => __("Address"),
    "f05" => __("Birthdate"),
    "f06" => __("Phone"),
    "f07" => __("Superior"),
    "f08" => __("Profile"),
    "f09" => __("Language"),
    "f10" => __("Country"),
];

$datauser = [
    "id" => $result["id"] ?? "",
    "uuid" => $uuid,
    "email" => $result["email"] ?? "",
    "password" => "    ",
    "password2" => "    ",
    "fullname" => $result["fullname"] ?? "",
    "address" => $result["address"] ?? "",
    "birthdate" => $result["birthdate"] ?? "",
    "phone" => $result["phone"] ?? "",

    "id_profile" => $result["id_profile"] ?? "",
    "id_parent" => $result["id_parent"] ?? "",
    "id_country" => $result["id_country"] ?? "",
    "id_language" => $result["id_language"] ?? "",

    "profiles" => $profiles,
    "parents" => $parents,
    "countries" => $countries,
    "languages" => $languages,
];

$textpermission = [
    "tr00" => __("Save"),
    "tr01" => __("Processing..."),
    "tr02" => __("Cancel"),
    "tr03" => __("Error"),
    "tr04" => __("<b>Data updated</b>"),

    "f00" => __("Nº"),
    "f01" => __("Code"),
    "f02" => __("User"),
    "f03" => __("Permissions JSON"),
];

$datapermission = [
    "id_user" => $result["id"] ?? "",

    "id" => $permissions["id"] ?? "",
    "uuid" => $permissions["uuid"] ?? "",
    "json_rw" => $permissions["json_rw"] ?? "[]",
];


$textbusinessdata = [
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
    "f50" => __("Space test")

];

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
<div class="modal-form">
  <div class="card-header">
    <h4 class="card-title mb-1"><?=$h1?></h4>
  </div>
  <div class="card-body p-2 pt-0">
    <div class="tabs-menu">
      <ul class="nav nav-tabs profile navtab-custom panel-tabs">
        <li>
          <a href="#main" data-bs-toggle="tab" class="active" aria-expanded="true">
            <span class="visible-xs">
              <i class="las la-user-circle tx-16 me-1"></i>
            </span>
            <span class="hidden-xs">
              <?=__("Profile")?>
            </span>
          </a>
        </li>
        <li>
          <a href="#permissions" data-bs-toggle="tab" aria-expanded="false">
            <span class="visible-xs"><i class="las la-images tx-15 me-1"></i></span>
            <span class="hidden-xs">
              <?=__("Permissions")?>
            </span>
          </a>
        </li>
        <?
        if($isbow):
        ?>
        <li>
          <a href="#businessdata" data-bs-toggle="tab" aria-expanded="false">
            <span class="visible-xs"><i class="las la-images tx-15 me-1"></i></span>
            <span class="hidden-xs">
              <?=__("Business data")?>
            </span>
          </a>
        </li>
        <?
        endif;
        ?>
      </ul>
    </div><!--nav-->

    <div class="tab-content border-start border-bottom border-right border-top-0 p-2 br-dark">
      <div id="main" class="tab-pane active">
        <form-user-update
          csrf=<?$this->_echo_js($csrf);?>

          texts="<?$this->_echo_jslit($textuser);?>"

          fields="<?$this->_echo_jslit($datauser);?>"
        />
      </div>

      <div id="permissions" class="tab-pane">
        <form-user-permissions-update
          csrf=<?$this->_echo_js($csrf);?>

          useruuid="<?=$uuid?>"
          texts="<?$this->_echo_jslit($textpermission);?>"

          fields="<?$this->_echo_jslit($datapermission);?>"
        />
      </div>

      <?
      if ($isbow):
      ?>
      <div id="businessdata" class="tab-pane">
        <form-user-businessdata-update
            csrf=<?$this->_echo_js($csrf);?>

            useruuid="<?=$uuid?>"
            texts="<?$this->_echo_jslit($textbusinessdata);?>"

            fields="<?$this->_echo_jslit($databusinessdata);?>"
        />
      </div>
      <?
      endif;
      ?>
    </div><!--tab-content-->

  </div><!--card-body-->
</div>
<script type="module" src="/assets/js/restrict/users/update.js"></script>
<script type="module" src="/assets/js/restrict/users/permissions/update.js"></script>
<script type="module" src="/assets/js/restrict/users/businessdata/update.js"></script>
<script type="module">
import {show_tab} from "/assets/js/common/modal-launcher.js"
show_tab()
</script>