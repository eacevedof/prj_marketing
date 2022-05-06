<?php
/**
 * @var App\Shared\Infrastructure\Views\AppView $this
 */

$texts = [
  "tr00" => __("Save"),
  "tr01" => __("Processing..."),
  "tr02" => __("Error"),
  "tr03" => __("Some unexpected error occurred"),

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

$result = [
  "email"     => "",
  "password"  => "",
  "password2" => "",
  "fullname"  => "",
  "address"   => "",
  "birthdate" => "",
  "phone"     => "",

  "id_profile" => "",
  "id_parent" => "",
  "id_country" => "",
  "id_language" => "",

  "profiles" => $profiles,
  "parents" => $parents,
  "countries" => $countries,
  "languages" => $languages,
];
?>
<div class="modal-form">
  <div class="card-header">
    <h4 class="card-title mb-1"><?=$h1?></h4>
  </div>
  <div class="card-body pt-0">
    <form-user-insert
      csrf=<?$this->_echo_js($csrf);?>

      texts="<?$this->_echo_jslit($texts);?>"

      fields="<?$this->_echo_jslit($result);?>"
    />
  </div>
</div>
<script type="module" src="/assets/js/restrict/users/insert.js"></script>