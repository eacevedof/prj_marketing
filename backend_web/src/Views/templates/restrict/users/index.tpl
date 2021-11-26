<?php
/**
 * @var \App\Views\AppView $this
 * @var \App\Helpers\Views\DatatableHelper $dt
 */
use App\Factories\HelperFactory as HF;
$dt = HF::get("Views/Datatable");
$dt->add_column("id")
    ->is_visible()
    ->add_column("uuid")
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
<h1><?=$h1?></h1>
<div id="div-table-datatable">
    <table id="table-datatable" class="display" style="width:95%">
        <thead>
            <tr>
                <?= $dt->get_ths() ?>
            </tr>
            <tr row="search" class="hidden">
                <?= $dt->get_search_tds() ?>
            </tr>
        </thead>
        <tbody approle="tbody"></tbody>
        <tfoot>
            <tr>
                <?= $dt->get_tf() ?>
            </tr>
        </tfoot>
    </table>
</div>
<script type="module">
import dt_render from "/assets/js/common/datatable/dttable.js?<?=$now?>"
import button from "/assets/js/common/datatable/button.js"

button().set_topbuttons([
  {
    approle: "add-item",
    label: <?$this->_echo_js(__("Add"));?>,
    visible: true,
    css: "debug",
    onclick: () => alert("add")
  }
])

window.addEventListener("load", () => dt_render({
  ID_TABLE: "table-datatable",
  ITEMS_PER_PAGE: <?$dt->show_perpage();?>,
  URL_SEARCH: "/restrict/users/search",
  URL_ADD: "/restrict/users/create",
  BUTTONS: {
    INSERT: {
      LABEL: <?$this->_echo_js(__("Add"));?>,
      TOOLTIP: <?$this->_echo_js(__("Add"));?>,
      ACTION: function (){
        const url = "/restrict/users/create"
        fetch(url)
          .then(response => response.text())
          .then(html => {
            window.modalraw.disable_bgclick().set_body(html).show()
          })
          .catch(error => {
            console.log("users.create.tpl",error)
          })
          .finally(()=>{

          })
      }
    },
    REFRESH: {
      LABEL: <?$this->_echo_js(__("Refresh"));?>,
      TOOLTIP: <?$this->_echo_js(__("Refresh"));?>,
    },
    FILTER_SHOW: {
      LABEL: <?$this->_echo_js(__("Show filters"));?>,
      TOOLTIP: <?$this->_echo_js(__("Show filters"));?>,
    },
    FILTER_RESET: {
      LABEL: <?$this->_echo_js(__("Reset filters"));?>,
      TOOLTIP: <?$this->_echo_js(__("Reset filters"));?>,
    },
  }
}))
</script>