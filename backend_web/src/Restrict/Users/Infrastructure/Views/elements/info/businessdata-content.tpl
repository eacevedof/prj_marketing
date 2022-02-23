<?php
/**
 * @var App\Shared\Infrastructure\Views\AppView $this
 * @var array $result
 */
if (is_null($result["businessdata"])) return;

use App\Shared\Infrastructure\Factories\HelperFactory as HF;
use App\Shared\Infrastructure\Helpers\Views\BusinessDataHelper;
$helper = HF::get(BusinessDataHelper::class);
?>
<div id="businessdata" class="tab-pane">
  <?php
  $businessdata = $result["businessdata"] ?? [];
  ?>
  <ol>
    <li><b><?=__("Business name")?>:</b>&ensp;<span><?=$businessdata["business_name"] ?? ""?></span></li>
    <li><b><?=__("Slug")?>:</b>&ensp;<span><?=$helper->get_link_domain($businessdata, "slug")?></li>
    <li><b><?=__("Url logo 1")?>:</b>&ensp;<?=$helper->get_img_link($businessdata, "user_logo_1")?></li>
    <li><b><?=__("Url logo 2")?>:</b>&ensp;<?=$helper->get_img_link($businessdata, "user_logo_2")?></li>
    <li><b><?=__("Url logo 3")?>:</b>&ensp;<?=$helper->get_img_link($businessdata, "user_logo_3")?></li>
    <li><b><?=__("Url favicon")?>:</b>&ensp;<?=$helper->get_img_link($businessdata, "url_favicon")?></li>
  </ol>
  <br/>
  <ol>
    <li><b><?=__("Head bg color")?>:</b>&ensp;<?=$helper->get_color($businessdata, "head_bgcolor")?></li>
    <li><b><?=__("Head color")?>:</b>&ensp;<?=$helper->get_color($businessdata, "head_color")?></li>
    <li>
      <b><?=__("Url head bg image")?>:</b>&ensp;
      <?=$helper->get_img_link($businessdata, "head_bgimage")?>
    </li>

    <li><b><?=__("Body bg color")?>:</b>&ensp;<?=$helper->get_color($businessdata, "body_bgcolor")?></li></li>
    <li><b><?=__("Body color")?>:</b>&ensp;<?=$helper->get_color($businessdata, "body_color")?></li>
    <li>
      <b><?=__("Url body bg image")?>:</b>&ensp;
      <?=$helper->get_img_link($businessdata, "body_bgimage")?>
    </li>
  </ol>
  <br/>
  <ol>
    <li>
      <b><?=__("Url business")?>:</b>&ensp;
      <?=$helper->get_link($businessdata, "url_business")?>
    </li>
    <li>
      <b><?=__("Url Facebook")?>:</b>&ensp;
      <?=$helper->get_link($businessdata, "url_social_fb")?>
    </li>
    <li>
      <b><?=__("Url Instagram")?>:</b>&ensp;
      <?=$helper->get_link($businessdata, "url_social_ig")?>
    </li>
    <li>
      <b><?=__("Url Twitter")?>:</b>&ensp;
      <?=$helper->get_link($businessdata, "url_social_twitter")?>
    </li>
    <li>
      <b><?=__("Url Tiktok")?>:</b>&ensp;
      <?=$helper->get_link($businessdata, "url_social_tiktok")?>
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
