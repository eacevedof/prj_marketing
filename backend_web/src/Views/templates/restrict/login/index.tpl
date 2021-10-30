<?php
/**
 * @var \App\Views\AppView $this
 */

//var_dump($login);
?>
<h1>Login</h1>
<div id="app">
    <form @submit.prevent="onSubmit">
        <div>
            <label for="email"><?=__("Email")?> *</label>
            <input type="email" id="email" v-model="email" placeholder="<?=__("your email")?>" required="required">
        </div>
        <div>
            <label for="password"><?=__("Password")?> *</label>
            <input id="password" type="password" v-model="password" placeholder="<?=__("your secret password")?>" required="required">
        </div>

        <div>
            <button id="btn-contact" class="btn btn-dark" :disabled="issending" >
                {{btnsend}}
                <img v-if="issending" src="/assets/images/common/loading.png" width="25" height="25"/>
            </button>
        </div>
    </form>
</div>
<?= $this->_asset_js("restrict/login") ?>
