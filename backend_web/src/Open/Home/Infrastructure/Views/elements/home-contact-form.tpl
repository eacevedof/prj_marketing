<?php
/**
 * @var App\Shared\Infrastructure\Views\AppView $this
 * @var array $promotionui
 */
$texts = [
    "tr01" => __("Processing..."),
    "tr02" => __("Error"),
    "tr03" => __("Some unexpected error occurred"),
    "tr04" => __("Please check form errors"),

    "tr10" => __("Empty value is not allowed"),
    "tr11" => __("Invalid format. Eg: xxx@domain.com"),
    "tr12" => __("Invalid format. Eg: John Santino"),
    "tr13" => __("Invalid format. Eg: I want to apply remarketing on my clients"),
    "tr14" => __("Invalid format. Eg: Some description about my business"),

    "email" => __("Email"),
    "name" => __("Name"),
    "subject" => __("Subject"),
    "message" => __("Message"),

];
?>
<dialog class="dialog">
  <form-home-contact-send
      csrf="<?=$promotionuuid?>"
      texts="<?php $this->_echo_jslit($texts);?>"
  />
</dialog>
<script type="module" src="/assets/js/open/home/contact/send.js"></script>