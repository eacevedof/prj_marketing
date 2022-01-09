<?php
/**
 * @var \App\Views\AppView $this
 */
?>
<div id="dashboard" class="row row-sm">
  <?
  $i=0;
  foreach ($modules as $module => $config):
    $i++;
    $title = $config["title"];
    $icon = $config["icon"];
    $search = $config["actions"]["search"]["url"] ?? "";
    $create = $config["actions"]["create"]["url"] ?? "";
  ?>
  <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12" module="<?=$module?>">
    <div class="card">
      <div class="card-body iconfont text-start">
        <div class="d-flex justify-content-between pb-1">
          <h4 class="card-title mg-b-0"><?="$i. $title"?></h4>
          <i class="las <?=$icon?> tx-16 me-1"></i>
        </div>
        <div class="card-footer p-1">
          <?if ($search):?>
          <a href="<?=$search?>" class="btn btn-primary btn-block"><?=__("Search")?></a>
          <?endif;?>
          <?if ($create):?>
          <button href="<?=$create?>" class="btn btn-success btn-block" approle="add-item"><?=__("Add")?></button>
          <?endif;?>
        </div>
      </div>
    </div>
  </div>
  <?
  endforeach;
  ?>
</div>
<script type="module">
import spinner from "/assets/js/common/spinner.js"
import {reqtxt} from "/assets/js/common/req.js"

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
</script>
