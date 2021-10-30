<?php
/**
 * @var \App\Views\AppView $this
 */
echo " tpl: xxx \n";
echo "variables en vista <br/>";
print_r($a);

$this->_element("common/hola", ["xxx"=>$a]);

$this->_element("common/hola", ["xxx" => "agua"]);