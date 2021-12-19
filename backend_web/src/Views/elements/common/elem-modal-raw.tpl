<?php
/**
* @var \App\Views\AppView $this
*/
?>
<div id="modal-global" class="mod-overlay mod-hide">
  <div class="mo-container" role="modal-dialog">
    <button class="mcr-button-x" role="btn-close">
      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16">
        <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
      </svg>
    </button>
    <div class="mcr-content mc-wd-2 mc-hg-3" role="body"></div>
  </div>
</div>
<script type="module">
import ModalRaw from "/assets/js/common/modal-raw.js"
window.modalraw = new ModalRaw({
  id_modal: "modal-global"
})
</script>
