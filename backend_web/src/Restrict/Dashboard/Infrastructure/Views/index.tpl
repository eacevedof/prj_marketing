<?php
/**
 * @var App\Shared\Infrastructure\Views\AppView $this
 */
$this->_element("restrict/elem-bowdisabled");
?>
<div id="dashboard" class="row row-sm">
  <?php
  $i=0;
  foreach ($modules as $module => $config):
    $i++;
    $title = $config["title"];
    $icon = $config["icon"];
    $search = $config["actions"]["search"]["url"] ?? "";
    $create = $config["actions"]["create"]["url"] ?? "";
    $edit = $config["actions"]["edit"]["url"] ?? "";
  ?>
  <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12" module="<?=$module?>">
    <div class="card">
      <div class="card-body iconfont text-start">
        <div class="d-flex justify-content-between pb-1">
          <h4 class="card-title mg-b-0"><?="$i. $title"?></h4>
          <i class="las <?=$icon?> tx-16 me-1"></i>
        </div>
        <div class="card-footer p-1">
          <?php
          if ($search):
          ?>
          <a href="<?=$search?>" class="btn btn-primary btn-block"><?=__("Search")?></a>
          <?php
          endif;
          if ($create):
          ?>
          <button href="<?=$create?>" class="btn btn-success btn-block" approle="add-item"><?=__("Add")?></button>
          <?php
          endif;
          if ($edit):
          ?>
            <button href="<?=$edit?>" class="btn btn-success btn-block" approle="add-item"><?=__("Edit")?></button>
          <?php
          endif;
          ?>
        </div>
      </div>
    </div>
  </div>
  <?php
  endforeach;
  ?>
</div>
<script type="module">
//dashboard.index.tpl
import spinner from "/assets/js/common/spinner.js"
import {reqtxt} from "/assets/js/common/req.js"
import {show_restrict_url} from "/assets/js/common/modal-launcher.js"

const dashboard = document.getElementById("dashboard")
dashboard.addEventListener("click", (e) => {
  const $el = e.target
  let url = ""
  if ($el && $el.tagName==="BUTTON" && (url=$el.getAttribute("href"))) {
    _in_modal(url)
  }
})

const _in_modal = async url => {
  spinner.render()
  const r = await reqtxt.get(url)
  spinner.remove()
  if (r.errors)
      return window.snack.set_color(SNACK.ERROR).set_time(5).set_inner(r.errors[0]).show()

  window.modalraw.opts({
      bgclick: false,
      body: r,
  }).show()
}// add. _in_modal

await show_restrict_url()
</script>
