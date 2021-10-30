<?php
/**
 * @var \App\Views\AppView $this
 */

//var_dump($login);
?>
<h1>Login</h1>
<form @submit="checkform" id="form-login">
    <div>
        <label for="name">Nombre *</label>
        <input type="text" id="name" v-model="name" placeholder="...tu nombre" required="required">
    </div>
    <div>
        <label for="email">Email *</label>
        <input id="email" type="email" v-model="email" placeholder="tu-email@dominio.com" class="form-control" required="required">
    </div>
    <div>
        <label for="subject" class="form-label">Asunto *</label>
        <input type="text" id="subject" v-model="subject" placeholder="Asunto" class="form-control">
    </div>
    <div>
        <label for="message" class="form-label">Mensaje *</label>
        <textarea id="message" v-model="message" class="form-control"  placeholder="Mensaje" required="required" rows="5"></textarea>
    </div>
    <div>
        <button id="btn-contact" class="btn btn-dark" :disabled="issending" >
            {{btnsend}}
            <img v-if="issending" src="/assets/images/common/loading.png" width="25" height="25"/>
        </button>
    </div>
</form>
<?= $this->_asset_js("restrict/login") ?>
