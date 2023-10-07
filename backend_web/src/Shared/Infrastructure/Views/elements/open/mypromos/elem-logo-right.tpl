<?php
/**
 * @var App\Shared\Infrastructure\Views\AppView $this
 * @var array $space
 */
use App\Shared\Infrastructure\Helpers\RoutesHelper as Routes;

$spaceurl = ($businessSlug = ($space["businessSlug"] ?? $space["businessslug"] ?? ""))
              ? Routes::getUrlByRouteName("business.space", ["businessSlug" => $businessSlug])
              : "/";

$urllogo = $space["businesslogo"] ?? "/themes/mypromos/images/mypromos-logo-orange.svg";
?>
<!--elem-logo-right-->
<nav class="nav-flex center-x">
  <figure>
    <a href="<?=$spaceurl?>" rel="nofollow noopener noreferer">
      <img id="top-mark" src="<?=$urllogo?>" class="nav-icon">
    </a>
  </figure>
</nav>
<!--/elem-logo-right-->
