<?php
/**
 * @var \App\Views\AppView $this
 * @var \App\Helpers\Views\DatatableHelper $dthelp
 */
?>
<h1><?=$h1?></h1>
<div id="div-table-datatable">
    <table id="table-datatable" class="display">
        <thead>
            <tr>
                <?= $dthelp->get_ths() ?>
            </tr>
            <tr row="search" class="hidden">
                <?= $dthelp->get_search_tds() ?>
            </tr>
        </thead>
        <tbody approle="tbody"></tbody>
        <tfoot>
            <tr>
                <?= $dthelp->get_tf() ?>
            </tr>
        </tfoot>
    </table>
</div>
<script type="module">
import dt_render from "/assets/js/common/datatable/dttable.js"
import {button} from "/assets/js/common/datatable/button.js"
import {rowswal} from "/assets/js/common/datatable/rowswal.js"
import {column} from "/assets/js/common/datatable/column.js"

column.add_action_btn({
  approle: "rowbtn-show",
  text: "Show xxx",
   : `<button type="button" %attr%>%text%</button>`,
  attr: {
    approle: "rowbtn-show",
    uuid: "%uuid%",
    style: "color:red",
  }
})

rowswal.set_texts({
  success: {
    title: <?$this->_echo_js(__("Delete success:"));?>
  },
  error: {
    title: <?$this->_echo_js(__("Some error occured trying to delete"));?>
  }
})

button.add_topbtn({
  approle: "add-item",
  text: `<span style="color:blue"><?$this->_echo(__("Add"));?></span>`,
})

dt_render({
  URL_MODULE: "/restrict/users",
  ID_TABLE: "table-datatable",
  ITEMS_PER_PAGE: <?$dthelp->show_perpage();?>,
})
</script>