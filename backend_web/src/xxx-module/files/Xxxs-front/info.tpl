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
              <i class="las la-xxx-circle tx-16 me-1"></i>
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
        $xxx = $result["xxx"] ?? [];
        ?>
        <ol>
          %FIELD_KEY_AND_VALUES%
        </ol>
        <br/>
        <ul>
          <li><b><?=__("Created by")?>:</b>&ensp;<span><?=$xxx["insert_user"] ?? ""?></span></li>
          <li><b><?=__("Created at")?>:</b>&ensp;<span><?=$xxx["insert_date"] ?? ""?></span></li>
          <li><b><?=__("Modified by")?>:</b>&ensp;<span><?=$xxx["update_user"] ?? ""?></span></li>
          <li><b><?=__("Modified at")?>:</b>&ensp;<span><?=$xxx["update_date"] ?? ""?></span></li>

          <li><b><?=__("Deleted by")?>:</b>&ensp;<span><?=$xxx["delete_user"] ?? ""?></span></li>
          <li><b><?=__("Deleted at")?>:</b>&ensp;<span><?=$xxx["delete_date"] ?? ""?></span></li>
        </ul>
      </div><!-- xxx -->

      <div class="tab-pane" id="tab-1">
        <ol>
          <?php
          $tab1 = $result["tab-1"] ?? [];
          foreach ($tab1 as $field => $value):
          ?>
          <li><span><?php $this->_echo($value);?></span></li>
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
            <li><b><?php $this->_echo($arvalue["pref_key"]);?>:</b>&nbsp;&nbsp;<span><?php $this->_echo($arvalue["pref_value"]);?></span></li>
          <?php
          endforeach;
          ?>
        </ol>
      </div><!--tab-2-->

    </div><!--tab-content-->
  </div><!--card-body-->
</div>
<?php
$this->_element("restrict/elem-modal-launcher-showtab");
?>