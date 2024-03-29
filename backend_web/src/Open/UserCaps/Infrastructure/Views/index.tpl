<?php
/**
 * @var App\Shared\Infrastructure\Views\AppView $this
 * @var string $total
 * @var array $result
 */
?>
<?php
if (!$total) {
  echo "<h3>".__("You have no accumulated points yet")."</h3>";
  return;
}
?>
<h3><?=__("Total accumulated points: {0}", $total)?></h3>
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
      <td>
        <?php $this->_echo($row["description"]);?>
        <sub>(<?php $this->_echo($row["subscriptionuuid"]);?>)</sub>
      </td>
      <td><?php $this->_echo($row["date_execution"]);?></td>
      <td><?php $this->_echo($row["points"]);?></td>
    </tr>
  <?php
  endforeach;
  ?>
  </tbody>
</table>