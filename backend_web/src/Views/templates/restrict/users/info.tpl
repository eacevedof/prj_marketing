<?php
/**
 * @var \App\Views\AppView $this
 */
?>
<h1><?=$h1?></h1>
<h2><?=$uuid?></h2>
<ul>
<?php
prd($userinfo);
foreach ($userinfo["user"] as $field => $value):
?>
    <li><b><?$this->_echo($field);?></b> <span><?$this->_echo($value);?></span></li>
<?php
endforeach;
?>
</ul>

<hr/>
<?php
foreach ($userinfo["permissions"] as $field => $value):
?>
    <li><b><?$this->_echo($field);?></b> <span><?$this->_echo($value);?></span></li>
<?php
endforeach;
?>