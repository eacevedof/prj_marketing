<?php
/**
 * @var \App\Views\AppView $this
 */

?>
<div class="row row-sm">
  <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12" module="users">
    <div class="card">
      <div class="card-body iconfont text-start">
        <div class="d-flex justify-content-between pb-1">
          <h4 class="card-title mg-b-0">1. <?=__("Users")?></h4>
          <i class="las la-user-circle tx-16 me-1"></i>
        </div>
        <div class="card-footer p-1">
          <a href="/restrict/users" class="btn btn-primary btn-block"><?=__("Search")?></a>
          <a id="btn-users-add" href="/restrict/create" class="btn btn-success btn-block"><?=__("Add")?></a>
        </div>
      </div>
    </div>
  </div>
</div>
<script type="module">
import spinner from "/assets/js/common/spinner.js"
import {reqtxt} from "/assets/js/common/req.js"

const btn = document.getElementById("btn-users-add")
btn.addEventListener("click", ()=>_in_modal("/restrict/users/create"))

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
