<?php
/**
 * @var \App\Shared\Infrastructure\Views\AppView $this
 * @var array $result
 * @var string $uuid
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

      <div id="businessdata" class="tab-pane">
        businessdata
      </div>
    </div><!--tab-content-->

  </div><!--card-body-->
</div>
<script type="module" src="/assets/js/restrict/users/update.js"></script>
<script type="module" src="/assets/js/restrict/users/permissions/update.js"></script>
<script type="module">
import {show_tab} from "/assets/js/common/modal-launcher.js"
show_tab()
</script>