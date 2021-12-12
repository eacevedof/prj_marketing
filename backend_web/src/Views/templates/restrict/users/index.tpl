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

button.add_topbtn({
  approle: "add-item",
  text: `<span style="color:blue"><?$this->_echo(__("Add"));?></span>`,
})

column.add_column({
  data: "phone",
  render: (v,t,row) => `<span style="color:dodgerblue">${v}</span>`
})

column.add_rowbtn({
  btnid: "rowbtn-show",
  render: (v,t,row) => `<span style="color:darkblue">Show ${row.uuid}</span>`
})

column.add_extrowbtn((v,t,row) => `<span style="color:aquamarine; background: yellow">Extra ${row.id}</span>`)

rowswal.set_texts({
  success: {
    title: <?$this->_echo_js(__("Delete success:"));?>
  },
  error: {
    title: <?$this->_echo_js(__("Some error occured trying to delete"));?>
  }
})

dt_render({
  URL_MODULE: "/restrict/users",
  ID_TABLE: "table-datatable",
  ITEMS_PER_PAGE: <?$dthelp->show_perpage();?>,
})
</script>