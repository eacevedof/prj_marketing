<?php
/**
 * @var \App\Views\AppView $this
 * @var array $item
 */
?>
<h1><?=$h1?></h1>
<div id="app">
  <form-edit csrf="<?=$csrf?>" />
</div>
<script type="module">
import init from "/assets/js/restrict/users/edit.js"
init({
  tr00: <?$this->_echo_js(__("send"));?>,
  tr01: <?$this->_echo_js(__("Sending..."));?>,
  tr02: <?$this->_echo_js(__("Error"));?>,
  tr03: <?$this->_echo_js(__("Some unexpected error occurred"));?>,

  f00: <?$this->_echo_js(__("Email"));?>,
  f01: <?$this->_echo_js(__("Password"));?>,
  f02: <?$this->_echo_js(__("Password confirm"));?>,
  f03: <?$this->_echo_js(__("Full name"));?>,
  f04: <?$this->_echo_js(__("Address"));?>,
  f05: <?$this->_echo_js(__("Birthdate"));?>,
  f06: <?$this->_echo_js(__("Phone"));?>
})
</script>