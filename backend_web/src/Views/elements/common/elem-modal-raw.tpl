<?php
/**
* @var \App\Views\AppView $this
*/
?>
<div id="modal-global" class="modal-wrapper">
    <div class="modal-dialog modal-dialog-grid" role="modal-dialog">
        <header class="area-header">
            <h2 role="title"></h2>
            <button type="button" role="btn-close">x</button>
        </header>
        <div class="area-body" role="body"></div>
    </div>
</div>
<script type="module">
import ModalRaw from "/assets/js/common/modal-raw.js"
window.modalraw = new ModalRaw("modal-global")
</script>
