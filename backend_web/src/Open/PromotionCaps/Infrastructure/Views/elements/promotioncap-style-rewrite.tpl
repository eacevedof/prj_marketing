<?php
use App\Shared\Infrastructure\Helpers\Views\PromotionCap\BusinessDataHelper as BH;
?>
<style>
/*
promotioncap-style-rewrite.tpl
*/
.main-flex {
<?php
echo $bdhelp->get_style_body();
BH::echo_style("background-color", $promotion["bgcolor"]);
BH::echo_style("background-image", $promotion["bgimage_xs"]);
?>
}
</style>