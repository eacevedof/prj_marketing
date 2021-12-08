<?php
/**
 * @var \App\Views\AppView $this
 */
dd($users, "users");
dd($countries,"countries");
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

$result = [
  "email"     => "",
  "password"  => "",
  "password2" => "",
  "fullname"  => "",
  "address"   => "",
  "birthdate" => "",
  "phone"     => ""
];
?>
<h1><?=$h1?></h1>
<div id="app">
  <form-user-create
    csrf=<?$this->_echo_js($csrf);?>

    texts="<?$this->_echo_jslit($texts);?>"

    fields="<?$this->_echo_jslit($result);?>"
  />
</div>
<script type="module" src="/assets/js/restrict/users/create.js"></script>