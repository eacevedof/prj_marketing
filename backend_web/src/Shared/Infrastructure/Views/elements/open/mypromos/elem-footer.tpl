<?php
/**
 * @var App\Shared\Infrastructure\Views\AppView $this
 * @var array $space
 */
?>
<footer class="footer-flex">
  <div class="item item-logo">
    <a href="/"><img src="/themes/mypromos/images/provider-xxx-logo-white.svg">providerxxx.es</a></li>
  </div>
  <div class="item"></div>
  <div class="item item-links">
    <ul class="ul-links-flex">
      <li><a href="/terms-and-conditions"><?=__("Terms & Conditions") ?></a></li>
      <li><a href="/cookies-policy"><?=__("Cookies Policy") ?></a></li>
      <li><a href="/privacy-policy"><?=__("Privacy Policy") ?></a></li>
    </ul>
  </div>
  <div class="item"></div>
  <div class="item item-social">
    <ul class="ul-social-flex">
      <?php
      if ($url = $space["urlfb"]):
      ?>
      <li><a href="<?=$url?>" target="_blank" rel="nofollow noopener noreferer"><img src="/themes/mypromos/images/icon-social-fb.svg"></a></li>
      <?php
      endif;
      if ($url = $space["urltwitter"]):
      ?>
      <li><a href="<?=$url?>" target="_blank" rel="nofollow noopener noreferer"><img src="/themes/mypromos/images/icon-social-twitter.svg"></a></li>
      <?php
      endif;
      if ($url = $space["urlig"]):
      ?>
      <li><a href="<?=$url?>" target="_blank" rel="nofollow noopener noreferer"><img src="/themes/mypromos/images/icon-social-ig.svg"></a></li>
      <?php
      endif;
      if ($url = $space["urltiktok"]):
      ?>
      <li><a href="<?=$url?>" target="_blank" rel="nofollow noopener noreferer"><img src="/themes/mypromos/images/icon-social-tiktok.svg"></a></li>
      <?php
      endif;
      ?>
    </ul>
  </div>
</footer>