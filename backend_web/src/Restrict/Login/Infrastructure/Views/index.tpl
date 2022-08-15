<?php
/**
 * @var App\Shared\Infrastructure\Views\AppView $this
 */
use App\Shared\Infrastructure\Helpers\RoutesHelper as Routes;
$texts = [
    "tr00" => __("Send"),
    "tr01" => __("Processing..."),
    "tr03" => __("Error"),
    "tr04" => __("Access granted"),
    "tr05" => __("...redirecting to dashboard"),
    "tr06" => __("Check empty fields"),

    "f00" => __("Email")." (".__("required").")",
    "f01" => __("Password")." (".__("required").")",
];

$url = Routes::url("login.access")
?>
<div class="card box-shadow-0">
  <div class="card-header">
    <h1 class="card-title mb-1"><?= __("Login") ?></h1>
  </div>
  <div class="card-body pt-0">
    <form-login
        csrf="<?php $this->_echo($csrf);?>"
        url="<?php $this->_echo($url);?>"
        texts="<?php $this->_echo_jslit($texts);?>"
    />
  </div>
</div>
<script type="module"src="/assets/js/restrict/form-login.js"></script>
