<?php
/**
 * @var App\Shared\Infrastructure\Views\AppView $this
 * @var array $promotionui
 */
?>
<dialog class="dialog">
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