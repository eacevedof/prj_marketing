<?php
/**
 * @var \App\Views\AppView $this
 */
?>
<h1><?=$h1?></h1>
<div id="vue-users-create">
    <form @submit.prevent="onSubmit">
        <input type="hidden" id="_csrf" value="<?=$csrf??""?>" />
        <div>
            <label for="email"><?=__("Email")?> *</label>
            <div id="field-email">
                <input type="email" id="email" v-model="email" required="required">
            </div>
        </div>
        <div>
            <label for="password"><?=__("Password")?> *</label>
            <div id="field-password">
                <input type="password" id="password"  v-model="password" required>
            </div>
        </div>
        <div>
            <label for="password2"><?=__("Password confirm")?> *</label>
            <div id="field-password2">
                <input type="password" id="password2" v-model="password2" required>
            </div>
        </div>
        <div>
            <label for="fullname"><?=__("Full name")?> *</label>
            <div id="field-fullname">
                <input type="text" id="fullname" v-model="fullname" required>
            </div>
        </div>
        <div>
            <label for="address"><?=__("Address")?> *</label>
            <div id="field-address">
                <input type="text" id="address" v-model="address">
            </div>
        </div>
        <div>
            <label for="birthdate"><?=__("Birthdate")?> *</label>
            <div id="field-birthdate">
                <input type="date" id="birthdate" v-model="birthdate">
            </div>
        </div>
        <div>
            <button id="btn-submit" :disabled="issending" >
                {{btnsend}}
                <img v-if="issending" src="/assets/images/common/loading.png" width="25" height="25"/>
            </button>
        </div>
    </form>
</div>
<script type="module">
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
    email: "mmm@ooo.com",
    password: "uu",
    password2: "uu",
    fullname: "fff ulll",
    address: "sss oo mmee aadd",
    birthdate: "",
    phone: "444 333 222",
  }
})
</script>