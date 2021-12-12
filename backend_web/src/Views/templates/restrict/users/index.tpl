<?php
/**
 * @var \App\Views\AppView $this
 * @var \App\Helpers\Views\DatatableHelper $dthelp
 * @var array $auth
 */
?>
<h1><?=$h1?></h1>
<h4><?$this->_echo($auth["description"]);?> (<?$this->_echo($auth["uuid"]);?>)</h4>
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

const iduser = <?$this->_echo_js($auth["id"]);?>;
const sesprofile = <?$this->_echo_js($auth["id_profile"]);?>;

const PROFILES = {
  ROOT:"1",
  SYS_ADMIN:"2",
  BUSINESS_OWNER:"3",
  BUSINESS_MANAGER:"4",
}

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
    const usrprof = row.id_profile
    const usrid = row.id
    
    if(sesprofile===PROFILES.ROOT || iduser===usrid) 
      return `<button type="button" btnid="rowbtn-edit" uuid="${row?.uuid ?? ""}"><?$this->_echo(__("Edit"));?></button>`
    
    if(sesprofile===PROFILES.SYS_ADMIN && [PROFILES.BUSINESS_OWNER, PROFILES.BUSINESS_MANAGER].includes(usrprof)) {
      return `<button type="button" btnid="rowbtn-edit" uuid="${row?.uuid ?? ""}"><?$this->_echo(__("Edit"));?></button>`
    }

    if(sesprofile===PROFILES.BUSINESS_OWNER) {
      return `<button type="button" btnid="rowbtn-edit" uuid="${row?.uuid ?? ""}"><?$this->_echo(__("Edit"));?></button>`
    }
    return ""
  }
})

column.add_rowbtn({
  btnid: "rowbtn-del",
  render: (v,t,row) => {
    if(sesprofile==="2" && row.id_profile==="1") return ""
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