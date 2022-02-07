<?php
/**
 * @var App\Shared\Infrastructure\Views\AppView $this
 * @var string $h1
 * @var ?string $uuid
 * @var array $result
 */
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
          <a href="#tab-1" data-bs-toggle="tab" class="active" aria-expanded="true">
            <span class="visible-xs">
              <i class="las la-promotion-circle tx-16 me-1"></i>
            </span>
            <span class="hidden-xs">
              <?=__("tr_tab_1")?>
            </span>
          </a>
        </li>
        <li>
          <a href="#tab-2" data-bs-toggle="tab" aria-expanded="false">
            <span class="visible-xs"><i class="las la-images tx-15 me-1"></i></span>
            <span class="hidden-xs">
              <?=__("tr_tab_2")?>
            </span>
          </a>
        </li>
        <li>
          <a href="#tab-3" data-bs-toggle="tab" aria-expanded="false">
            <span class="visible-xs"><i class="las la-images tx-15 me-1"></i></span>
            <span class="hidden-xs">
              <?=__("tr_tab_3")?>
            </span>
          </a>
        </li>
      </ul>
    </div>

    <div class="tab-content border-start border-bottom border-right border-top-0 p-4 br-dark">
      <div class="tab-pane active" id="profile">
        <?php
        $promotion = $result["promotion"] ?? [];
        ?>
        <ol>
          <li><b><?=__("Nº")?>:</b>&ensp;<span><?=$promotion["id"] ?? ""?></span></li>
<li><b><?=__("Cod. Promo")?>:</b>&ensp;<span><?=$promotion["uuid"] ?? ""?></span></li>
<li><b><?=__("Owner")?>:</b>&ensp;<span><?=$promotion["id_owner"] ?? ""?></span></li>
<li><b><?=__("External code")?>:</b>&ensp;<span><?=$promotion["code_erp"] ?? ""?></span></li>
<li><b><?=__("Description")?>:</b>&ensp;<span><?=$promotion["description"] ?? ""?></span></li>
<li><b><?=__("Slug")?>:</b>&ensp;<span><?=$promotion["slug"] ?? ""?></span></li>
<li><b><?=__("Type")?>:</b>&ensp;<span><?=$promotion["id_type"] ?? ""?></span></li>
<li><b><?=__("Date from")?>:</b>&ensp;<span><?=$promotion["date_from"] ?? ""?></span></li>
<li><b><?=__("Date to")?>:</b>&ensp;<span><?=$promotion["date_to"] ?? ""?></span></li>
<li><b><?=__("Enabled")?>:</b>&ensp;<span><?=$promotion["is_active"] ?? ""?></span></li>
<li><b><?=__("Invested")?>:</b>&ensp;<span><?=$promotion["invested"] ?? ""?></span></li>
<li><b><?=__("Inv returned")?>:</b>&ensp;<span><?=$promotion["returned"] ?? ""?></span></li>
<li><b><?=__("Max confirmed")?>:</b>&ensp;<span><?=$promotion["max_confirmed"] ?? ""?></span></li>
<li><b><?=__("Notes")?>:</b>&ensp;<span><?=$promotion["notes"] ?? ""?></span></li>
        </ol>
        <ol>
          <li><b><?=__("Content")?>:</b>&ensp;<span><?=$promotion["content"] ?? ""?></span></li>
        </ol>
        <ol>
          <li><b><?=__("Bg color")?>:</b>&ensp;<span><?=$promotion["bgcolor"] ?? ""?></span></li>
          <li><b><?=__("Bg image xs")?>:</b>&ensp;<span><?=$promotion["bgimage_xs"] ?? ""?></span></li>
          <li><b><?=__("Bg image sm")?>:</b>&ensp;<span><?=$promotion["bgimage_sm"] ?? ""?></span></li>
          <li><b><?=__("Bg image md")?>:</b>&ensp;<span><?=$promotion["bgimage_md"] ?? ""?></span></li>
          <li><b><?=__("Bg image lg")?>:</b>&ensp;<span><?=$promotion["bgimage_lg"] ?? ""?></span></li>
          <li><b><?=__("Bg image xl")?>:</b>&ensp;<span><?=$promotion["bgimage_xl"] ?? ""?></span></li>
          <li><b><?=__("Bg image xxl")?>:</b>&ensp;<span><?=$promotion["bgimage_xxl"] ?? ""?></span></li>
        </ol>
        <br/>
        <ul>
          <li><b><?=__("Created by")?>:</b>&ensp;<span><?=$promotion["insert_user"] ?? ""?></span></li>
          <li><b><?=__("Created at")?>:</b>&ensp;<span><?=$promotion["insert_date"] ?? ""?></span></li>
          <li><b><?=__("Modified by")?>:</b>&ensp;<span><?=$promotion["update_user"] ?? ""?></span></li>
          <li><b><?=__("Modified at")?>:</b>&ensp;<span><?=$promotion["update_date"] ?? ""?></span></li>

          <li><b><?=__("Deleted by")?>:</b>&ensp;<span><?=$promotion["delete_user"] ?? ""?></span></li>
          <li><b><?=__("Deleted at")?>:</b>&ensp;<span><?=$promotion["delete_date"] ?? ""?></span></li>
        </ul>
      </div><!-- promotion -->

      <div class="tab-pane" id="tab-1">
        <ol>
          <?php
          $tab1 = $result["tab-1"] ?? [];
          foreach ($tab1 as $field => $value):
          ?>
          <li><span><?$this->_echo($value);?></span></li>
          <?php
          endforeach;
          ?>
        </ol>
      </div><!--tab-1-->

      <div class="tab-pane" id="tab-2">
        <ol>
          <?php
          $tab2 = $result["tab-2"] ?? [];
          foreach ($tab2 as $arvalue):
            ?>
            <li><b><?$this->_echo($arvalue["pref_key"]);?>:</b>&nbsp;&nbsp;<span><?$this->_echo($arvalue["pref_value"]);?></span></li>
          <?php
          endforeach;
          ?>
        </ol>
      </div><!--tab-2-->

    </div><!--tab-content-->
  </div><!--card-body-->
</div>