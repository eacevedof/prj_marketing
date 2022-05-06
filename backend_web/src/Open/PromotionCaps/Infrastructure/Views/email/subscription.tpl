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
              <?=__("You have subscribed to promotion: <b>&ldquo;{0}&rdquo;</b> of <b>{1}</b>", $data["promotion"], $data["business"])?>&nbsp;
              <br/>
              <?=__("Thanks for participating.") ?>
            </p>
            <p>
              <?=__("Please, click on")?>&nbsp;<a href="<?=$data["confirm_link"]?>"><?=__("this confirmation link")?></a>
              <?=__("to finish your subscription.") ?>
            </p>
            <p>
              <?=__("Remember to use this email in all of your future subscriptions in order to accumulate points") ?>
            </p>
            <p style="color: #ccc">
              <small><code><?=__("Promotion code: {0}", $data["promocode"])?></code></small>
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