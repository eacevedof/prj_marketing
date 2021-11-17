<?php
/**
 * @var \App\Views\AppView $this
 * @var \App\Helpers\Views\DatatableHelper $dt
 */
use App\Factories\HelperFactory as HF;
$dt = HF::get("Views/Datatable");
$dt->add_column("uuid")
    ->add_label("uuid")
    ->add_tooltip(__("uuid"))
    ->add_column("fullname")
    ->add_column("email")
    ->add_column("phone")
    ->add_column("id_profile")
    ->add_search_opts(["" => __("select an option"),"1"=>"uno","2"=>"dos"])
    ->add_column("id_nationality")
    ->add_column("id_language")
    ->add_action("edit")
    ->add_action("show")
    ->add_action("delete");

$now = date("YmdHis");
?>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
<h1><?=$h1?></h1>
<div>
    <button type="button" approle="btn-create">Create</button>
</div>
<div id="div-datatable">
    <table id="table-datatable" class="display" style="width:95%">
        <thead>
            <tr>
                <?= $dt->get_ths() ?>
            </tr>
            <tr row="search" class="hidden">
                <?= $dt->get_search_tds() ?>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <?= $dt->get_tf() ?>
            </tr>
        </tfoot>
    </table>
</div>
<script type="module">
const $btnCreate = document.querySelector(`[approle="btn-create"]`)
$btnCreate.addEventListener("click", function (){
  const url = "/restrict/users/create"
  fetch(url, {
    method: "GET",
    /*
    headers: new Headers({
      "Accept": "application/json",
      "Content-Type":"application/json",
      "Cache-Control":"max-age=640000"
    })

     */
  })
  .then(response => response.text())
  .then(html => {
    console.log("window-modalraw",window.modalraw)
    console.log("HTML",html)
    window.modalraw.set_body(html).show()
  })
  .catch(error => {
    console.log("get_data.error",error)
  })
  .finally(()=>{

  })
})
</script>
<script type="module">
import dt_render from "/assets/js/common/dttable.js?<?=$now?>"

window.addEventListener("load", () => dt_render({
  ID_TABLE: "table-datatable",
  ITEMS_PER_PAGE: <?$dt->show_perpage();?>,
  URL_SEARCH: "/restrict/users/search",
  URL_ADD: "/restrict/users/insert",
  BUTTONS: {
    INSERT: {
      LABEL: <?$this->echo_js(__("Add"));?>,
      TOOLTIP: <?$this->echo_js(__("Add"));?>,
    },
    REFRESH: {
      LABEL: <?$this->echo_js(__("Refresh"));?>,
      TOOLTIP: <?$this->echo_js(__("Refresh"));?>,
    },
    FILTER_SHOW: {
      LABEL: <?$this->echo_js(__("Show filters"));?>,
      TOOLTIP: <?$this->echo_js(__("Show filters"));?>,
    },
    FILTER_RESET: {
      LABEL: <?$this->echo_js(__("Reset filters"));?>,
      TOOLTIP: <?$this->echo_js(__("Reset filters"));?>,
    },
  }
}))
</script>