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
  echo "<h3>".__("You have no accumulated points yet")."</h3>";
  //return;
}
?>
<table class="table">
  <thead>
  <tr>
    <th><?=__("N.")?></th>
    <th><?=__("Promotion")?></th>
    <th><?=__("Date")?></th>
    <th><?=__("Points")?></th>
  </tr>
  </thead>
  <tbody>
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
  </tbody>
</table>