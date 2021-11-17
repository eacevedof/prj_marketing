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
<script>
alert("xxx")
document.getElementById("txt-user").focus()
document.addEventListener("DOMNodeInserted",function (){
  alert("holaxx")
})
var arr = MyDiv.getElementsByTagName('script')
for (var n = 0; n < arr.length; n++)
  eval(arr[n].innerHTML)//run script inside div
</script>

