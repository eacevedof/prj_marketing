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
              <?=__("You have confirmed your subscription to: <b>&ldquo;{0}&rdquo;</b> published by <b>{1}</b>", $data["promotion"], $data["business"])?>&nbsp;
              <br/>
              <?=__("Show this code when you want to make effective this voucher") ?>
            </p>
            <p>
              <b><code><?=$data["execode"]?></code></b>
            </p>
            <p>
              <?=__("Please, click on")?>&nbsp;<a href="<?=$data["points_link"]?>"><?=__("this link")?></a>&nbsp;
              <?=__("to check out your accumulated points at {0}", $data["business"]) ?>
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