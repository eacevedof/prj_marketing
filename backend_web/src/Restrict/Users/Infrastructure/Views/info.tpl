<?php
/**
 * @var \App\Shared\Infrastructure\Views\AppView $this
 * @var string $h1
 * @var ?string $uuid
 * @var array $result
 */
//dd($result);
?>
<div>
  <div class="card-header">
    <h4 class="card-title mb-1">
      <?=$h1?>
    </h4>
  </div>
  <div class="card-body p-2 pt-0">
    <div class="tabs-menu ">
      <ul class="nav nav-tabs profile navtab-custom panel-tabs">
        <li>
          <a href="#main" data-bs-toggle="tab" class="active" aria-expanded="true">
            <span class="visible-xs">
              <i class="las la-user-circle tx-16 me-1"></i>
            </span>
            <span class="hidden-xs">
              <?=__("Profile")?>
            </span>
          </a>
        </li>
        <li>
          <a href="#permissions" data-bs-toggle="tab" aria-expanded="false">
            <span class="visible-xs"><i class="las la-images tx-15 me-1"></i></span>
            <span class="hidden-xs">
              <?=__("Permissions")?>
            </span>
          </a>
        </li>
        <li>
          <a href="#preferences" data-bs-toggle="tab" aria-expanded="false">
            <span class="visible-xs"><i class="las la-images tx-15 me-1"></i></span>
            <span class="hidden-xs">
              <?=__("Preferences")?>
            </span>
          </a>
        </li>
<?php
if ($isbow):
?>
        <li>
          <a href="#businessdata" data-bs-toggle="tab" aria-expanded="false">
            <span class="visible-xs"><i class="las la-images tx-15 me-1"></i></span>
            <span class="hidden-xs">
              <?=__("Business data")?>
            </span>
          </a>
        </li>
<?php
endif;
?>
      </ul>
    </div>

    <div class="tab-content border-start border-bottom border-right border-top-0 p-4 br-dark">
      <div id="main" class="tab-pane active">
        <?php
        $profile = $result["user"] ?? [];
        ?>
        <ol>
          <li><b><?=__("Nº")?>:</b>&ensp;<span><?=$profile["id"] ?? ""?></span></li>
          <li><b><?=__("Code")?>:</b>&ensp;<span><?=$profile["uuid"] ?? ""?></span></li>
          <li><b><?=__("Full name")?>:</b>&ensp;<span><?=$profile["fullname"] ?? ""?></span></li>
          <li><b><?=__("Email")?>:</b>&ensp;<span><?=$profile["email"] ?? ""?></span></li>
          <li><b><?=__("Phone")?>:</b>&ensp;<span><?=$profile["phone"] ?? ""?></span></li>
          <li><b><?=__("Country")?>:</b>&ensp;<span><?=$profile["e_country"] ?? ""?></span></li>
          <li><b><?=__("Language")?>:</b>&ensp;<span><?=$profile["e_language"] ?? ""?></span></li>
          <li><b><?=__("Address")?>:</b>&ensp;<span><?=$profile["address"] ?? ""?></span></li>
          <li><b><?=__("Birthdate")?>:</b>&ensp;<span><?=str_replace(" 00:00:00","",$profile["birthdate"] ?? "")?></span></li>
          <li><b><?=__("Profile")?>:</b>&ensp;<span><?=$profile["e_profile"] ?? ""?></span></li>
          <li><b><?=__("Superior")?>:</b>&ensp;<span><?=$profile["e_parent"] ?? ""?></span></li>
        </ol>
        <br/>
        <ul>
          <li><b><?=__("Created by")?>:</b>&ensp;<span><?=$profile["insert_user"] ?? ""?></span></li>
          <li><b><?=__("Created at")?>:</b>&ensp;<span><?=$profile["insert_date"] ?? ""?></span></li>
          <li><b><?=__("Modified by")?>:</b>&ensp;<span><?=$profile["update_user"] ?? ""?></span></li>
          <li><b><?=__("Modified at")?>:</b>&ensp;<span><?=$profile["update_date"] ?? ""?></span></li>

          <li><b><?=__("Deleted by")?>:</b>&ensp;<span><?=$profile["delete_user"] ?? ""?></span></li>
          <li><b><?=__("Deleted at")?>:</b>&ensp;<span><?=$profile["delete_date"] ?? ""?></span></li>
        </ul>
      </div><!-- profile -->

      <div id="permissions" class="tab-pane">
        <ol>
          <?php
          $permissions = $result["permissions"] ?? [];
          foreach ($permissions as $field => $value):
          ?>
          <li><span><?$this->_echo($value);?></span></li>
          <?php
          endforeach;
          ?>
        </ol>
      </div><!--permissions-->

      <div id="preferences" class="tab-pane">
        <ol>
          <?php
          $preferences = $result["preferences"] ?? [];
          foreach ($preferences as $arvalue):
            ?>
            <li><b><?$this->_echo($arvalue["pref_key"]);?>:</b>&nbsp;&nbsp;<span><?$this->_echo($arvalue["pref_value"]);?></span></li>
          <?php
          endforeach;
          ?>
        </ol>
      </div><!--preferences-->

      <?php
      if ($isbow):
      ?>
      <div id="businessdata" class="tab-pane">
        <?php
        $businessdata = $result["businessdata"] ?? [];
        ?>
        <ol>
          <li><b><?=__("Business name")?>:</b>&ensp;<span><?=$businessdata["business_name"] ?? ""?></span></li>
          <li><b><?=__("Slug")?>:</b>&ensp;<span><?=$businessdata["slug"] ?? ""?></span></li>
          <li><b><?=__("Url logo 1")?>:</b>&ensp;<span><?=$businessdata["user_logo_1"] ?? ""?></span></li>
          <li><b><?=__("Url logo 2")?>:</b>&ensp;<span><?=$businessdata["user_logo_2"] ?? ""?></span></li>
          <li><b><?=__("Url logo 3")?>:</b>&ensp;<span><?=$businessdata["user_logo_3"] ?? ""?></span></li>
          <li><b><?=__("Url favicon")?>:</b>&ensp;<span><?=$businessdata["url_favicon"] ?? ""?></span></li>
        </ol>
        <br/>
        <ol>
          <li><b><?=__("Head bg color")?>:</b>&ensp;<span style="background-color:<?=$businessdata["head_bgcolor"] ?? ""?>;">&nbsp;&nbsp;</span></li>
          <li><b><?=__("Head color")?>:</b>&ensp;<span style="background-color:<?=$businessdata["head_bgcolor"] ?? ""?>;">&nbsp;&nbsp;</span></li>
          <li>
            <b><?=__("Url head bg image")?>:</b>&ensp;
            <a href="<?=$businessdata["head_bgimage"] ?? "#"?>" target="_blank">
              <img src="<?=$businessdata["head_bgimage"] ?? "#"?>"/>
            </a>
          </li>

          <li><b><?=__("Body bg color")?>:</b>&ensp;<span style="background-color:<?=$businessdata["body_bgcolor"] ?? ""?>;">&nbsp;&nbsp;</span></li>
          <li><b><?=__("Body color")?>:</b>&ensp;<span style="background-color:<?=$businessdata["body_color"] ?? ""?>;">&nbsp;&nbsp;</span></li>
          <li>
            <b><?=__("Url body bg image")?>:</b>&ensp;
            <a href="<?=$businessdata["body_bgimage"] ?? "#"?>" target="_blank">
              <img src="<?=$businessdata["body_bgimage"] ?? "#"?>"/>
            </a>
          </li>
        </ol>
        <br/>
        <ol>
          <li>
            <b><?=__("Url business")?>:</b>&ensp;
            <a href="<?=$businessdata["url_business"] ?? "#"?>" target="_blank">
              <?=$businessdata["url_business"] ?? "#"?>
            </a>
          </li>
          <li>
            <b><?=__("Url Facebook")?>:</b>&ensp;
            <a href="<?=$businessdata["url_social_fb"] ?? "#"?>" target="_blank">
              <?=$businessdata["url_social_fb"] ?? "#"?>
            </a>
          </li>
          <li>
            <b><?=__("Url Instagram")?>:</b>&ensp;
            <a href="<?=$businessdata["url_social_ig"] ?? "#"?>" target="_blank">
              <?=$businessdata["url_social_ig"] ?? "#"?>
            </a>
          </li>
          <li>
            <b><?=__("Url Twitter")?>:</b>&ensp;
            <a href="<?=$businessdata["url_social_twitter"] ?? "#"?>" target="_blank">
              <?=$businessdata["url_social_twitter"] ?? "#"?>
            </a>
          </li>
          <li>
            <b><?=__("Url Tiktok")?>:</b>&ensp;
            <a href="<?=$businessdata["url_social_tiktok"] ?? "#"?>" target="_blank">
              <?=$businessdata["url_social_tiktok"] ?? "#"?>
            </a>
          </li>
        </ol>
        <br/>
        <ul>
          <li><b><?=__("Created by")?>:</b>&ensp;<span><?=$businessdata["insert_user"] ?? ""?></span></li>
          <li><b><?=__("Created at")?>:</b>&ensp;<span><?=$businessdata["insert_date"] ?? ""?></span></li>
          <li><b><?=__("Modified by")?>:</b>&ensp;<span><?=$businessdata["update_user"] ?? ""?></span></li>
          <li><b><?=__("Modified at")?>:</b>&ensp;<span><?=$businessdata["update_date"] ?? ""?></span></li>

          <li><b><?=__("Deleted by")?>:</b>&ensp;<span><?=$businessdata["delete_user"] ?? ""?></span></li>
          <li><b><?=__("Deleted at")?>:</b>&ensp;<span><?=$businessdata["delete_date"] ?? ""?></span></li>
        </ul>
      </div><!-- businessdata -->
      <?php
      endif;
      ?>
    </div><!--tab-content-->
  </div><!--card-body-->
</div>