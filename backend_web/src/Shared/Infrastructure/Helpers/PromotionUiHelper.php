<?php

namespace App\Shared\Infrastructure\Helpers;

use App\Open\PromotionCaps\Domain\Enums\PromotionCapUserType;

final class PromotionUiHelper extends AppHelper implements IHelper
{
    private array $promotionUi;

    public function __construct(array $promotionUi)
    {
        $this->promotionUi = $promotionUi;
    }

    public static function fromPrimitives(array $promotionUi): self
    {
        return new self($promotionUi);
    }

    public function getInputs(): array
    {
        $mapped = [];
        foreach ($this->promotionUi as $field => $value) {
            $parts = explode("_", $field);
            $prefix = $parts[0];
            if ($prefix !== "input") {
                continue;
            }
            if (!$value) {
                continue;
            }
            $input = $parts[1];
            if(strstr($field, "_is_")) {
                $input = "{$parts[1]}_{$parts[2]}";
            }
            $mapped[$input] = $this->promotionUi["pos_$input"];
        }
        asort($mapped);
        $mapped = array_keys($mapped);

        $fks = [
            PromotionCapUserType::INPUT_GENDER,
            PromotionCapUserType::INPUT_LANGUAGE,
            PromotionCapUserType::INPUT_COUNTRY
        ];

        /*
        $mapped = array_map(function (string $field) use ($fks) {
            return in_array($field, $fks) ? "id_$field" : $field;
        }, $mapped);
        */
        return $mapped;
    }
}
