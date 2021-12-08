<?php
/**
 * @var \App\Views\AppView $this
 * @var array $result
 */

$texts = [
  "tr00" => __("send"),
  "tr01" => __("Sending..."),
  "tr02" => __("Error"),
  "tr03" => __("Some unexpected error occurred"),

  "f00" => __("Email"),
  "f01" => __("Password"),
  "f02" => __("Password confirm"),
  "f03" => __("Full name"),
  "f04" => __("Address"),
  "f05" => __("Birthdate"),
  "f06" => __("Phone"),
  "f07" => __("Parent"),
  "f08" => __("Profile"),
  "f09" => __("Language"),
  "f10" => __("Country"),
];

$result = [
  "uuid" => $uuid,
  "email" => $result["email"] ?? "",
  "password" => "    ",
  "password2" => "    ",
  "fullname" => $result["fullname"] ?? "",
  "address" => $result["address"] ?? "",
  "birthdate" => $result["birthdate"] ?? "",
  "phone" => $result["phone"] ?? "",

  "id_parent" => $result["id_parent"] ?? "",
  "id_country" => $result["id_country"] ?? "",
  "id_language" => $result["id_language"] ?? "",
  "id_profile" => $result["id_profile"] ?? "",

  "parents" => $users,
  "countries" => $countries,
  "languages" => $languages,
  "profiles" => $profiles,
];
?>
<h1><?=$h1?></h1>
<div id="app">
  <form-user-edit
    csrf=<?$this->_echo_js($csrf);?>

    texts="<?$this->_echo_jslit($texts);?>"

    fields="<?$this->_echo_jslit($result);?>"
  />
</div>
<script type="module" src="/assets/js/restrict/users/edit.js"></script>