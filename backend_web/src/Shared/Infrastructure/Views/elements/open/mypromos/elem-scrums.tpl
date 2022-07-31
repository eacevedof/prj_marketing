<?php
/**
 * @var App\Shared\Infrastructure\Views\AppView $this
 * @var array $space
 */
use App\Shared\Infrastructure\Helpers\RoutesHelper as Routes;

$urls = [
    "home" => Routes::url("home"),
    "promocreate" => $space["promotionlink"] ?? "",
];
?>
<section class="section-scrumbs center-x">
<ul>
  <li><a href="/">&#8962; <?=__("Home")?></a></li>
  <?php
  if ($url = $urls["promocreate"]):
  ?>
  <li><a href="<?=$url?>"><?php $this->_echo($space["promotion"]);?></a></li>
  <?php
  endif;
  ?>
</ul>
</section>
