<?php
/**
 * @var App\Shared\Infrastructure\Views\AppView $this
 * @var array $space
 */
$urlbusiness = $space["businessurl"] ?? "/";
$target = $urlbusiness==="/" ? "_self" : "_blank";
$urllogo = $space["businesslogo"] ?? "/themes/mypromos/images/provider-xxx-logo-orange.svg";
?>
<!--elem-logo-right-->
<nav class="nav-flex center-x">
  <figure>
    <a href="<?=$urlbusiness?>" target="<?=$target?>" rel="nofollow noopener noreferer">
      <img id="top-mark" src="<?=$urllogo?>" class="nav-icon">
    </a>
  </figure>
</nav>
<!--/elem-logo-right-->
