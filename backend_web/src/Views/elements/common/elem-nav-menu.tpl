<?php
/**
 * @var \App\Views\AppView $this
 * @var array $authuser
 * @var array $topmenu
 */
$authuser = $authuser ?? [];
if ($authuser):
?>
<!--Horizontal-main auth-->
<div class="sticky">
  <div class="horizontal-main hor-menu clearfix side-header">
    <div class="horizontal-mainwrapper container clearfix">
      <!--Nav-->
      <nav class="horizontalMenu clearfix">
        <ul class="horizontalMenu-list">
          <li aria-haspopup="true">
            <a href="/restrict">
                <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24" >
                  <path d="M0 0h24v24H0V0z" fill="none"/><path d="M5 5h4v6H5zm10 8h4v6h-4zM5 17h4v2H5zM15 5h4v2h-4z" opacity=".3"/>
                  <path d="M3 13h8V3H3v10zm2-8h4v6H5V5zm8 16h8V11h-8v10zm2-8h4v6h-4v-6zM13 3v6h8V3h-8zm6 4h-4V5h4v2zM3 21h8v-6H3v6zm2-4h4v2H5v-2z"/>
                </svg>
                <?=__("Home")?>
            </a>
          </li>
          <li aria-haspopup="true">
            <a href="#">
              <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24" >
                <path d="M0 0h24v24H0V0z" fill="none"/><path d="M5 5h4v6H5zm10 8h4v6h-4zM5 17h4v2H5zM15 5h4v2h-4z" opacity=".3"/>
                <path d="M3 13h8V3H3v10zm2-8h4v6H5V5zm8 16h8V11h-8v10zm2-8h4v6h-4v-6zM13 3v6h8V3h-8zm6 4h-4V5h4v2zM3 21h8v-6H3v6zm2-4h4v2H5v-2z"/>
              </svg>
              <?=__("Modules")?><i class="fe fe-chevron-down horizontal-icon"></i>
            </a>
            <ul class="sub-menu">
              <?
              foreach ($topmenu as $config):
                $title = $config["title"];
                $url = $config["search"];
              ?>
              <li aria-haspopup="true"><a href="<?=$url?>" class="slide-item"><?=$title?></a></li>
              <?
              endforeach;
              ?>
            </ul>
          </li>
          <li aria-haspopup="true">
            <a href="/restrict/logout">
              <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24" >
                <path d="M0 0h24v24H0V0z" fill="none"/><path d="M5 5h4v6H5zm10 8h4v6h-4zM5 17h4v2H5zM15 5h4v2h-4z" opacity=".3"/>
                <path d="M3 13h8V3H3v10zm2-8h4v6H5V5zm8 16h8V11h-8v10zm2-8h4v6h-4v-6zM13 3v6h8V3h-8zm6 4h-4V5h4v2zM3 21h8v-6H3v6zm2-4h4v2H5v-2z"/>
              </svg>
              <?=__("Logout")?>
            </a>
          </li>

          <li class="float-end">
            <p style="color:#0761E0;" class="pt-3"><?="[{$authuser["id"]}] {$authuser["description"]} | {$authuser["id_profile"]}"?></p>
          </li>
        </ul>
      </nav>
      <!--Nav-->
    </div>
  </div>
</div>
<!--Horizontal-main auth-->
<?php
else:
?>
<!--Horizontal-main no-auth-->
<div class="sticky">
  <div class="horizontal-main hor-menu clearfix side-header">
    <div class="horizontal-mainwrapper container clearfix">
      <!--Nav-->
      <nav class="horizontalMenu clearfix">
        <ul class="horizontalMenu-list">
          <li aria-haspopup="true">
            <a href="/">
              <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24" >
                <path d="M0 0h24v24H0V0z" fill="none"/><path d="M5 5h4v6H5zm10 8h4v6h-4zM5 17h4v2H5zM15 5h4v2h-4z" opacity=".3"/>
                <path d="M3 13h8V3H3v10zm2-8h4v6H5V5zm8 16h8V11h-8v10zm2-8h4v6h-4v-6zM13 3v6h8V3h-8zm6 4h-4V5h4v2zM3 21h8v-6H3v6zm2-4h4v2H5v-2z"/>
              </svg>
              <?=__("Home")?>
            </a>
          </li>
        </ul>
      </nav>
      <!--Nav-->
    </div>
  </div>
</div>
<!--Horizontal-main no-auth-->
<?php
endif;
