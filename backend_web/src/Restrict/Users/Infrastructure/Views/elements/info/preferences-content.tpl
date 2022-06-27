<?php
/**
 * @var App\Shared\Infrastructure\Views\AppView $this
 * @var array $result
 */
if (is_null($result["preferences"])) return;
?>
<div id="preferences" class="tab-pane">
  <ol>
    <?php
    $preferences = $result["preferences"] ?? [];
    foreach ($preferences as $arvalue):
      ?>
      <li><b><?php $this->_echo($arvalue["pref_key"]);?>:</b>&nbsp;&nbsp;<span><?php $this->_echo($arvalue["pref_value"]);?></span></li>
    <?php
    endforeach;
    ?>
  </ol>
</div><!--preferences-->
