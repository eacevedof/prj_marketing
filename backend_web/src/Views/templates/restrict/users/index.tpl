<?php
/**
 * @var \App\Views\AppView $this
 * @var \App\Helpers\Views\DatatableHelper $dthelp
 * @var array $auth
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

const idprofile = <?$this->_echo_js($auth["id_profile"]);?>

button.add_topbtn({
  approle: "add-item",
  text: `<span style="color:blue"><?$this->_echo(__("Add"));?></span>`,
})

column.add_rowbtn({
  btnid: "rowbtn-show",
  text: <?$this->_echo_js(__("Show"));?>
})

column.add_rowbtn({
  btnid: "rowbtn-edit",
  render: (v,t,row) => {
    if(idprofile==="2" && row.id_profile==="1") return ""
    return `<button type="button" btnid="rowbtn-edit" uuid="${row?.uuid ?? ""}"><?$this->_echo(__("Edit"));?></button>`
  }
})

column.add_rowbtn({
  btnid: "rowbtn-del",
  render: (v,t,row) => {
    if(idprofile==="2" && row.id_profile==="1") return ""
    return `<button type="button" btnid="rowbtn-del" uuid="${row?.uuid ?? ""}"><?$this->_echo(__("Remove"));?></button>`
  }
})

rowswal.set_texts({
  success: {
    title: <?$this->_echo_js(__("Delete success:"));?>
  },
  error: {
    title: <?$this->_echo_js(__("Some error occurred trying to delete"));?>
  }
})

dt_render({
  URL_MODULE: "/restrict/users",
  ID_TABLE: "table-datatable",
  ITEMS_PER_PAGE: <?$dthelp->show_perpage();?>,
})
</script>