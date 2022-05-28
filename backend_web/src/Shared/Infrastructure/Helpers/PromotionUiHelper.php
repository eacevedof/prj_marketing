<?php
namespace App\Shared\Infrastructure\Helpers;

final class PromotionUiHelper extends AppHelper implements IHelper
{
    private array $promotionui;

    public function __construct(array $promotionui)
    {
        $this->promotionui = $promotionui;
    }

    public static function get_instance(array $promotionui): self
    {
        return new self($promotionui);
    }

    public function get_inputs(): array
    {
        $mapped = [];
        foreach ($this->promotionui as $field => $value) {
            $parts = explode("_", $field);
            $prefix = $parts[0];
            if ($prefix!=="input") continue;
            if (!$value) continue;
            $input = $parts[1];
            if(strstr($field,"_is_")) $input = "{$parts[1]}_{$parts[2]}";
            $mapped[$input] = $this->promotionui["pos_$input"];
        }
        asort($mapped);
        $mapped = array_keys($mapped);
        $fks = ["language","country","gender"];
        $mapped = array_map(function (string $field) use ($fks) {
            return in_array($field, $fks) ? "id_$field" : $field;
        }, $mapped);
        return $mapped;
    }
}
