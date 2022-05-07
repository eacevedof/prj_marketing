<?php
/**
 * @var array $data
 */
$hello = $data["user"] ?: $data["email"];
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
              <code><?=$data["execode"]?></code>
            </p>
            <p>
              <?=__("Please, click on")?>&nbsp;<a href="<?=$data["confirm_link"]?>"><?=__("this confirmation link")?></a>
              <?=__("to finish your subscription.") ?>
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