<?php
/**
 * @var App\Shared\Infrastructure\Views\AppView $this
 * @var array $result
 */
if (is_null($result["permissions"])) return;
?>
<div id="permissions" class="tab-pane">
  <ol>
    <?php
    $permissions = $result["permissions"];
    foreach ($permissions as $field => $value):
      ?>
      <li><span><?$this->_echo($value);?></span></li>
    <?php
    endforeach;
    ?>
  </ol>
</div><!--permissions-->