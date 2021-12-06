<?php
/**
 * @var \App\Views\AppView $this
 * @var string $h1
 * @var ?string $uuid
 * @var array $result
 */
?>
<h1><?=$h1?></h1>
<h2><?=$uuid ?? ""?></h2>
<ul>
<?php
foreach (($result["user"] ?? []) as $field => $value):
?>
    <li><b><?$this->_echo($field);?></b> <span><?$this->_echo($value);?></span></li>
<?php
endforeach;
?>
</ul>

<hr/>
<?php
foreach (($result["permissions"] ?? []) as $field => $value):
?>
    <li><b><?$this->_echo($field);?></b> <span><?$this->_echo($value);?></span></li>
<?php
endforeach;
?>