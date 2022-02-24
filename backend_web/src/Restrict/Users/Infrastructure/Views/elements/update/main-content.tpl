<?php
/**
 * @var App\Shared\Infrastructure\Views\AppView $this
 * @var array $result
 */
if (is_null($result["usert"])) return;
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
?>
<div id="main" class="tab-pane active">
  <form-user-update
      csrf=<?$this->_echo_js($csrf);?>

      texts="<?$this->_echo_jslit($textuser);?>"

      fields="<?$this->_echo_jslit($datauser);?>"
  />
</div>

