<?php
/**
 * @var \App\Views\AppView $this
 */
?>
<h1><?=$h1?></h1>
<form>
    <label>user</label>
    <input type="text" id="txt-user">
    <button type="button" onclick="alert('envio')">boton de prueba</button>
</form>
<script type="module">
alert("xxx");
document.getElementById("txt-user").focus()
</script>

