<?php
/**
 * @var \App\Views\AppView $this
 * @var array $item
 */

$texts = [
  "tr001" => __("send")
]
?>
<h1><?=$h1?></h1>
<div id="app">
  <form-edit
    csrf="<?=$csrf?>"

    texts=<?$this->_echo_js($texts);?>

    fields=`{
      uuid: <?$this->_echo_js($uuid);?>,
      email: <?$this->_echo_js($item["email"] ?? "");?>,
      password: "    ",
      password2: "    ",
      fullname: <?$this->_echo_js($item["fullname"] ?? "");?>,
      address: <?$this->_echo_js($item["address"] ?? "");?>,
      birthdate: <?$this->_echo_js($item["birthdate"] ?? "");?>,
      phone: <?$this->_echo_js($item["phone"] ?? "");?>,
    }`
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