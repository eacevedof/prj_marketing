<?php
/**
 * @var App\Shared\Infrastructure\Views\AppView $this
 * @var array $promotionui
 */

$mapped = [];
foreach ($promotionui as $field => $value) {
    $parts = explode("_", $field);
    $prefix = $parts[0];
    if ($prefix!=="input") continue;
    if (!$value) continue;
    $input = $parts[1];
    $mapped[$input] = $promotionui["pos_$input"];
}
asort($mapped);
print_r($mapped);
?>
