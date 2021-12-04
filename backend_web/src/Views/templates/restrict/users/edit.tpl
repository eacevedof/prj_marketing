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

$fields = [
  "uuid" => $uuid,
  "email" => $item["email"] ?? "",
  "password" => "    ",
  "password2" => "    ",
  "fullname" => $item["fullname"] ?? "",
  "address" => $item["address"] ?? "",
  "birthdate" => $item["birthdate"] ?? "",
  "phone" => $item["phone"] ?? ""
];
//var_dump($texts, json_encode($texts));die;
?>

<h1><?=$h1?></h1>
<div id="app">
  <form-edit
    csrf=<?$this->_echo_js($csrf);?>

    texts="<?$this->_echo_js($texts, true);?>"

    fields="<?$this->_echo_js($fields, true);?>"
  />
</div>
<script type="module" src="/assets/js/restrict/users/edit.js"></script>