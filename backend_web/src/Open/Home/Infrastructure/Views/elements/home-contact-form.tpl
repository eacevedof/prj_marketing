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
  <form id="form-contact" class="form-flex">
    <div>
      <label for="name">Nombre *</label>
      <input type="text" id="name" placeholder="Nicolas Cage" required="required">
    </div>
    <div>
      <label for="email">Email *</label>
      <input id="email" type="email" placeholder="nicocage@domain.com" required="required">
    </div>
    <div>
      <label for="subject">Asunto *</label>
      <input type="text" id="subject" placeholder="I would like to join" class="form-control">
    </div>
    <div>
      <label for="message">Mensaje *</label>
      <textarea id="message" placeholder="My phone is: 777 888 999. My business exports apples" required="required" rows="5" class="form-control"></textarea>
    </div>
    <div class="form-buttons">
      <button id="btn-contact" class="btn btn-dark">Enviar</button>
    </div>
    <button type="button" id="button-exit" class="button-exit"><img src="/themes/mypromos/images/icon-close-modal.svg"></button>
  </form>
</dialog>
<script type="module" src="/assets/js/open/home/contact/send.js"></script>