<?php
/**
 * @var App\Shared\Infrastructure\Views\AppView $this
 * @var array $bowdisabled
 */
if (!$bowdisabled) return;
?>
<div class="row row-sm">
  <div class="col-xl-12">
    <div class="card">
      <div class="card-body">
        <div id="elem-bowdisabled">
          <h2><?=__("Warning")?></h2>
          <p>
              <?= __("Business account <b>{0}</b> is disabled", $bowdisabled["business_name"])?>&nbsp;
              <?=__("for this reason: {0}", $bowdisabled["disabled_reason"]) ?>.
          </p>
          <p>
              <?=__("Please, contact <a href=\"mailto:{0}\">{1}</a> to resolve this issue", "support@yyy.xxx")?>
          </p>
        </div>
      </div>
    </div>
  </div>
</div>
<?php
exit();
?>
