<?php
/**
 * @var array $data
 */
$hello = $data["username"] ?: $data["email"];
?>
<table>
  <tr>
    <td></td>
    <td><h1><?=$data["business"]?></h1></td>
    <td></td>
  </tr>
  <tr>
    <td></td>
    <td>
      <table>
        <tr><td>
            <h3><?=__("Hello {0}!!", $hello) ?></h3>
            <p>
              <?=("We have updated your points. Check it out ")?>
              <a href="<?=$data["points_link"]?>"><?=__("here")?></a>
            </p>
        </td></tr>
      </table>
    </td>
    <td></td>
  </tr>
  <tr>
    <td></td><td></td><td></td>
  </tr>
</table>