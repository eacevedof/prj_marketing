<?php
/**
 * @var App\Shared\Infrastructure\Views\AppView $this
 * @var array $result
 * @var string $h1
 * @var string $error
 */
use App\Shared\Infrastructure\Components\Date\UtcComponent as UTC;

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
  $points = array_column($result, "p");
  $points = array_sum($points);
  ?>
  <h3><?=__("Hello {0}!", $username)?></h3>
  <h4><?=__("You have a total of {0} points", $points)?></h4>
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
        <td><?$this->_echo($i+1)?></td>
        <td><?$this->_echo($row["description"]);?></td>
        <td><?$this->_echo($row["date_execution"]);?></td>
        <td><?$this->_echo($row["p"]);?></td>
      </tr>
    <?php
    endforeach;
    ?>
  </table>
</main>