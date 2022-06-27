<?php
/**
 * @var App\Shared\Infrastructure\Views\AppView $this
 * @var array $result
 */
if (is_null($result["user"])) return;
$user = $result["user"];

$texts = [
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

$user = [
    "id" => $user["id"] ?? "",
    "uuid" => $uuid,
    "email" => $user["email"] ?? "",
    "password" => "    ",
    "password2" => "    ",
    "fullname" => $user["fullname"] ?? "",
    "address" => $user["address"] ?? "",
    "birthdate" => substr($user["birthdate"] ?? "",0, 10),
    "phone" => $user["phone"] ?? "",

    "id_profile" => $user["id_profile"] ?? "",
    "id_parent" => $user["id_parent"] ?? "",
    "id_country" => $user["id_country"] ?? "",
    "id_language" => $user["id_language"] ?? "",

    "profiles" => $profiles,
    "parents" => $parents,
    "countries" => $countries,
    "languages" => $languages,
];
//dd($user);
?>
<div id="main" class="tab-pane active">
  <form-user-update
      csrf=<?php $this->_echo_js($csrf);?>

      texts="<?php $this->_echo_jslit($texts);?>"

      fields="<?php $this->_echo_jslit($user);?>"
  />
</div>

