<?php
echo " \n";
echo "variables en vista <br/>";
print_r($a);

$this->element("common/hola", ["xxx"=>$a]);

$this->element("common/hola", ["xxx" => "agua"]);