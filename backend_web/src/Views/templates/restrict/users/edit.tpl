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
<script type="module">
import init from "/assets/js/restrict/users/edit.js"
init({
  tr00: <?$this->_echo_js(__("send"));?>,
  tr01: <?$this->_echo_js(__("Sending..."));?>,
  tr02: <?$this->_echo_js(__("Error"));?>,
  tr03: <?$this->_echo_js(__("Some unexpected error occurred"));?>,

  f00: <?$this->_echo_js(__("Email"));?>,
  f01: <?$this->_echo_js(__("Password"));?>,
  f02: <?$this->_echo_js(__("Password confirm"));?>,
  f03: <?$this->_echo_js(__("Full name"));?>,
  f04: <?$this->_echo_js(__("Address"));?>,
  f05: <?$this->_echo_js(__("Birthdate"));?>,
  f06: <?$this->_echo_js(__("Phone"));?>
},{
  uuid: <?$this->_echo_js($uuid);?>,
  email: <?$this->_echo_js($item["email"] ?? "");?>,
  password: "    ",
  password2: "    ",
  fullname: <?$this->_echo_js($item["fullname"] ?? "");?>,
  address: <?$this->_echo_js($item["address"] ?? "");?>,
  birthdate: <?$this->_echo_js($item["birthdate"] ?? "");?>,
  phone: <?$this->_echo_js($item["phone"] ?? "");?>,
})
</script>