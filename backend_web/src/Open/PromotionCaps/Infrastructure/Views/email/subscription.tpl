<?php
/**
 * @var array $data
 */
$hello = $data["user"] ?: $data["email"];
?>
<table>
  <tr>
    <td></td><td><?=$data["business"]?></td><td></td>
  </tr>
  <tr>
    <td></td>
    <td>
      <table>
        <tr><td>
            <h3><?=__("Hello!! {0}", $hello) ?></h3>
            <p>
              <?=__("You have subscribed to promotion: {0}", $data["promotion"]) ?>&nbsp;
              <?=__("with code {0}", $data["promocode"]) ?>&nbsp;
              <?=__("Thank you for your participation.") ?>
            </p>
            <p>
              <?=__("Please, click this")?>&nbsp;<a href="<?=$data["confirm_link"]?>"><?=__("confirmation link")?></a>
              <br/><br/>
              <?=__("to finish your subscription") ?>
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