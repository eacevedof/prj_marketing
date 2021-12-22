<?php
/**
 * @var \App\Views\AppView $this
 * @var string $h1
 * @var ?string $uuid
 * @var array $result
 */
?>
<div class="modal-form">
    <div class="card-header">
      <h4 class="card-title mb-1"><?=$h1?></h4>
      <h5 class="card-title mb-1"><?=$uuid ?? ""?></h5>
    </div>
    <div class="card-body pt-0">
    <ul>
    <?php
    foreach (($result["user"] ?? []) as $field => $value):
    ?>
        <li><b><?$this->_echo($field);?></b> <span><?$this->_echo($value);?></span></li>
    <?php
    endforeach;
    ?>
    </ul>

    <hr/>
    <?php
    foreach (($result["permissions"] ?? []) as $field => $value):
    ?>
        <li><b><?$this->_echo($field);?></b> <span><?$this->_echo($value);?></span></li>
    <?php
    endforeach;
    ?>
    </div>
</div>
