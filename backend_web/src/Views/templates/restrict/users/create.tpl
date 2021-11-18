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
            <input type="email" id="email" v-model="email" placeholder="<?=__("your email")?>" required="required">
        </div>
        <div>
            <label for="password"><?=__("Password")?> *</label>
            <input id="password" type="password" v-model="password" required>
        </div>
        <div>
            <label for="text"><?=__("Full name")?> *</label>
            <input type="text" v-model="fullname" required>
        </div>
        <div>
            <label for="text"><?=__("Address")?> *</label>
            <input type="text" v-model="address">
        </div>
        <div>
            <label for="text"><?=__("Birthdate")?> *</label>
            <input type="date" v-model="birthdate">
        </div>
        <div>
            <button id="btn-contact" class="btn btn-dark" :disabled="issending" >
                {{btnsend}}
                <img v-if="issending" src="/assets/images/common/loading.png" width="25" height="25"/>
            </button>
        </div>
    </form>
</div>
<script type="module">
import vue from "/assets/js/restrict/users/create.js"
vue()
</script>