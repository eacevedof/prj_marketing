<?php
/**
 * @var App\Shared\Infrastructure\Views\AppView $this
 * @var array $space
 */

use App\Shared\Infrastructure\Components\Request\RoutesComponent as Routes;

$urls = [
    "home" => Routes::url("home"),
    "terms" => Routes::url("terms.general"),
    "cookies" => Routes::url("cookies.policy"),
    "privacy" => Routes::url("privacy.policy"),
];
?>
<footer class="footer-flex">
  <div class="item item-logo">
    <a href="<?=$urls["terms"]?>"><img src="/themes/mypromos/images/provider-xxx-logo-white.svg">providerxxx.es</a></li>
  </div>
  <div class="item"></div>
  <div class="item item-links">
    <ul class="ul-links-flex">
      <li><a href="<?=$urls["terms"]?>"><?=__("Terms & Conditions") ?></a></li>
      <li><a href="<?=$urls["cookies"]?>"><?=__("Cookies Policy") ?></a></li>
      <li><a href="<?=$urls["privacy"]?>"><?=__("Privacy Policy") ?></a></li>
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