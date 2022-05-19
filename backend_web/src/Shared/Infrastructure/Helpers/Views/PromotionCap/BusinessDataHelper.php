<?php
namespace App\Shared\Infrastructure\Helpers\Views\PromotionCap;

use App\Shared\Infrastructure\Helpers\IHelper;
use App\Shared\Infrastructure\Helpers\AppHelper;

final class BusinessDataHelper extends AppHelper implements IHelper
{
    private array $businessdata;

    public function __construct(array $businessdata)
    {
        $this->businessdata = $businessdata;
    }

    public function get_style(): string
    {
        $style = [];
        if ($value = trim($this->businessdata["head_bgimage"]))
            $style[] = "background-image:$value;";

  background-image: url("<?=$businessdata["head_bgimage"]?>");
    }
}
