<?php
/**
 * @var App\Shared\Infrastructure\Views\AppView $this
 * @var array $result
 * @var string $h1
 * @var string $error
 */
?>
<?php
if (!$result) {
  echo "<p>".__("You have no accumulated points yet")."</p>";
  return;
}
?>
<table>
  <tr>
    <th><?=__("Nº")?></th>
    <th><?=__("Promotion")?></th>
    <th><?=__("Date")?></th>
    <th><?=__("Points")?></th>
  </tr>
  <?php
  foreach ($result as $i => $row):
  ?>
    <tr>
      <td><?php $this->_echo($i+1)?></td>
      <td><?php $this->_echo($row["description"]);?></td>
      <td><?php $this->_echo($row["date_execution"]);?></td>
      <td><?php $this->_echo($row["points"]);?></td>
    </tr>
  <?php
  endforeach;
  ?>
</table>