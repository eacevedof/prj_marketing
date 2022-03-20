<?php
/**
 * @var App\Shared\Infrastructure\Views\AppView $this
 * @var array $result
 */
if (is_null($result["permissions"])) return;
$permissions = $result["permissions"];
?>
<div id="permissions" class="tab-pane">
  <b><?=__("Policies")?>:</b>
  <pre><?=$permissions["json_rw"]?></pre>
  <br/>
  <ul>
    <li><b><?=__("Created by")?>:</b>&ensp;<span><?=$permissions["insert_user"] ?? ""?></span></li>
    <li><b><?=__("Created at")?>:</b>&ensp;<span><?=$permissions["insert_date"] ?? ""?></span></li>
    <li><b><?=__("Modified by")?>:</b>&ensp;<span><?=$permissions["update_user"] ?? ""?></span></li>
    <li><b><?=__("Modified at")?>:</b>&ensp;<span><?=$permissions["update_date"] ?? ""?></span></li>
    <?php
    if ($issystem):
    ?>
    <li><b><?=__("Deleted by")?>:</b>&ensp;<span><?=$permissions["delete_user"] ?? ""?></span></li>
    <li><b><?=__("Deleted at")?>:</b>&ensp;<span><?=$permissions["delete_date"] ?? ""?></span></li>
    <?php
    endif;
    ?>
  </ul>
</div><!--permissions-->