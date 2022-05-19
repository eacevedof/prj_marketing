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

}
