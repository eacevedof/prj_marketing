<?php
/**
 * @var \App\Views\AppView $this
 */
$texts = [
  "tr00" => __("send"),
  "tr01" => __("Sending..."),
  "tr02" => __("Cancel"),
  "tr03" => __("Error"),
  "tr04" => __("<b>Data created</b>"),


  %HTML_FIELDS%
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
    <form-xxx-create
      csrf=<?$this->_echo_js($csrf);?>

      texts="<?$this->_echo_jslit($texts);?>"

      fields="<?$this->_echo_jslit($result);?>"
    />
  </div>
</div>
<script type="module" src="/assets/js/restrict/xxxs/create.js"></script>