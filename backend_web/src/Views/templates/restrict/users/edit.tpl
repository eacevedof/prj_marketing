<?php
/**
 * @var \App\Views\AppView $this
 * @var array $item
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
  "f06" => __("Phone")
];
$itemx = [
  "uuid" => $uuid,
  "email" => $item["email"] ?? "",
  "password" => "    ",
  "password2" => "    ",
  "fullname" => $item["fullname"] ?? "",
  "address" => $item["address"] ?? "",
  "birthdate" => $item["birthdate"] ?? "",
  "phone" => $item["phone"] ?? ""
];
?>

<h1><?=$h1?></h1>
<div id="app">
  <form-edit
    csrf=<?$this->_echo_js($csrf);?>

    texts="<?$this->_echo_jslit($texts);?>"

    fields="<?$this->_echo_jslit($item);?>"
  />
</div>
<script type="module" src="/assets/js/restrict/users/edit.js"></script>