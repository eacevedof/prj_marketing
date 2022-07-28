<?php
/**
 * @var App\Shared\Infrastructure\Views\AppView $this
 * @var array $space
 */
$urlpromo = $space["promotionlink"] ?? "";
?>
<section class="section-scrumbs center-x">
<ul>
  <li><a href="/">&#8962; <?=__("Home")?></a></li>
  <?php
  if ($urlpromo):
  ?>
  <li><a href="<?=$urlpromo?>"><?php $this->_echo($space["promotion"]);?></a></li>
  <?php
  endif;
  ?>
</ul>
</section>
