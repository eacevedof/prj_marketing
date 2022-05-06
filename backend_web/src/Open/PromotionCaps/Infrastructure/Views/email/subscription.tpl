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
              <?=__("You have subscribed to promotion: <b>&ldquo;{0}&ldquo;</b>", $data["promotion"]) ?>&nbsp;
              <?=__("with code <code>{0}</code>", $data["promocode"]) ?>
              <br/>
              <?=__("Thank you for your participation.") ?>
            </p>
            <p>
              <?=__("Please, click on")?>&nbsp;<a href="<?=$data["confirm_link"]?>"><?=__("this confirmation link")?></a>
              <?=__("to finish your subscription.") ?>
            </p>
            <p>
              <?=__("Remember to use the same email in all your future subscriptions in order to accumulate points for future surprises") ?>
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