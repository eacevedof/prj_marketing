<?php
namespace App\Shared\Infrastructure\Helpers\Views\PromotionCap;

use App\Shared\Infrastructure\Helpers\IHelper;
use App\Shared\Infrastructure\Helpers\AppHelper;

final class PromotionUiHelper extends AppHelper implements IHelper
{
    private array $promotionui;

    public function __construct(array $promotionui)
    {
        $this->promotionui = $promotionui;
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
            $mapped[$input] = $this->promotionui["pos_$input"];
        }
        asort($mapped);
        $mapped = array_keys($mapped);
        //dd($mapped);
        $mapped = array_merge($mapped, ["terms", "mailing"]);
        //dd($mapped);
        return $mapped;
    }
}
