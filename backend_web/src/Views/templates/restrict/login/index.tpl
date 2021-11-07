<?php
/**
 * @var \App\Views\AppView $this
 */

?>
<h1><?= __("Login") ?></h1>
<div id="app">
    <form @submit.prevent="onSubmit">
        <input type="hidden" id="csrf" v-model="csrf" value="<?=$csrf?>" />
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
<script>
const trs = {

}
</script>
<?= $this->_asset_js_module("restrict/login") ?>
