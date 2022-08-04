<?php
/**
 * @var App\Shared\Infrastructure\Views\AppView $this
 * @var array $space
 */

use App\Shared\Infrastructure\Helpers\RoutesHelper as Routes;

$urls = [
    "home" => Routes::url("home"),
    "terms" => Routes::url("terms.general"),
    "cookies" => Routes::url("cookies.policy"),
    "privacy" => Routes::url("privacy.policy"),
];
if ($_SERVER["REQUEST_URI"] === "/") {
  $space["urltwitter"] = $space["urltwitter"] ?? "https://twitter.com/mypromoses";
  $space["urlig"] = $space["urlig"] ?? "https://www.instagram.com/mypromoses";
}
?>
<footer class="footer-flex">
  <div class="item item-logo">
    <a href="<?=$urls["home"]?>">
      <?=__("Powered by")?> <img src="/themes/mypromos/images/mypromos-logo-white.svg"></a>
    </li>
  </div>
  <div class="item"></div>
  <div class="item item-links">
    <ul class="ul-links-flex">
      <li><a href="<?=$urls["terms"]?>"><?=__("Terms & Conditions") ?></a></li>
      <li><a href="<?=$urls["cookies"]?>"><?=__("Cookies Policy") ?></a></li>
      <li><a href="<?=$urls["privacy"]?>"><?=__("Privacy Policy") ?></a></li>
      <li><span>© <?=date("Y")?></span></li>
    </ul>
  </div>
  <div class="item"></div>
  <div class="item item-social">
    <ul class="ul-social-flex">
      <?php
      if ($url = $space["urlfb"] ?? ""):
      ?>
      <li><a href="<?=$url?>" target="_blank" rel="nofollow noopener noreferer"><img src="/themes/mypromos/images/icon-social-fb.svg"></a></li>
      <?php
      endif;
      if ($url = $space["urltwitter"] ?? ""):
      ?>
      <li><a href="<?=$url?>" target="_blank" rel="nofollow noopener noreferer"><img src="/themes/mypromos/images/icon-social-twitter.svg"></a></li>
      <?php
      endif;
      if ($url = $space["urlig"] ?? ""):
      ?>
      <li><a href="<?=$url?>" target="_blank" rel="nofollow noopener noreferer"><img src="/themes/mypromos/images/icon-social-ig.svg"></a></li>
      <?php
      endif;
      if ($url = $space["urltiktok"] ?? ""):
      ?>
      <li><a href="<?=$url?>" target="_blank" rel="nofollow noopener noreferer"><img src="/themes/mypromos/images/icon-social-tiktok.svg"></a></li>
      <?php
      endif;
      ?>
    </ul>
  </div>
</footer>