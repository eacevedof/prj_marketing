<?php
/**
 * @var App\Shared\Infrastructure\Views\AppView $this
 * @var array $result
 * @var string $h1
 * @var string $error
 */
?>
<main>
  <?php
  if (isset($error))
    return $this->_element("common/elem-error", ["title"=>$h1, "description"=>$error]);
  ?>
  <h1><?=$h1;?></h1>
  <?php
  if (!$result) {
    echo "<p>".__("You have no accumulated points yet")."</p>";
    return;
  }
  ?>
  <table>
    <td></td>
  </table>
</main>