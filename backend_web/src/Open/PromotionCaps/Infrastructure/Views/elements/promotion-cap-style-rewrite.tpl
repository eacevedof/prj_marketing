<?php
use App\Shared\Infrastructure\Helpers\Views\PromotionCap\BusinessDataHelper as BH;
?>
<style>
/*
promotion-cap-style-rewrite.tpl
*/

.main-flex {
<?=$bdhelp->get_style_body()?>
<?php
BH::echo_style("background-color", $promotion["bgcolor"]);
BH::echo_style("background-image", $promotion["bgimage_lg"]);
?>
}
</style>