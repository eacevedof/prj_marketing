<?php
/**
 * @var \App\Views\AppView $this
 */
?>
<h1><?=$h1?></h1>
<div id="app">
<form-create csrf="<?=$csrf?>" />
</div>
<script type="module" src="/assets/js/restrict/users/create.js"></script>

<script type="module">
/*
import vue from "/assets/js/restrict/users/create.js"

vue({
  texts: {
    tr00: <?$this->_echo_js(__("send"));?>,
    tr01: <?$this->_echo_js(__("Sending..."));?>,
    tr02: <?$this->_echo_js(__("Send"));?>,
    tr03: <?$this->_echo_js(__("Incomplete process"));?>,
    tr04: <?$this->_echo_js(__("It was not possible to process your request. Please try again<br/>"));?>,
    tr05: <?$this->_echo_js(__("Something went wrong!"));?>,
    tr06: <?$this->_echo_js(__("Some unexpected error occurred"));?>,
  },
  fields:{
    email: "",
    password: " ",
    password2: " ",
    fullname: "",
    address: "",
    birthdate: "",
    phone: "",
  }
})
*/
</script>