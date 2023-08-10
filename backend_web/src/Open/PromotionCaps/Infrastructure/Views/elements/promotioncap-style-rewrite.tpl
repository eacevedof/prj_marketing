<?php
use App\Shared\Infrastructure\Helpers\Views\PromotionCap\BusinessDataHelper as BH;
?>
<style>
/*
promotioncap-style-rewrite.tpl
*/
.main-flex {
<?php
echo $bdhelp->getStyleBody();
BH::echoStyle("background-color", $promotion["bgcolor"]);
BH::echoStyle("background-image", $promotion["bgimage_xs"]);
?>
}
</style>