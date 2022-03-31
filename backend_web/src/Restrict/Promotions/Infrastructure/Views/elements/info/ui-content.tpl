<?php
/**
 * @var App\Shared\Infrastructure\Views\AppView $this
 * @var array $result
 */
if (is_null($result["ui"])) return;
?>
<div id="ui" class="tab-pane">
  <ol>
    <?php
    $ui = $result["ui"] ?? [];
    foreach ($ui as $arvalue):
      ?>
      <li><b><?$this->_echo($arvalue["pref_key"]);?>:</b>&nbsp;&nbsp;<span><?$this->_echo($arvalue["pref_value"]);?></span></li>
    <?php
    endforeach;
    ?>
  </ol>
</div><!--ui-->
