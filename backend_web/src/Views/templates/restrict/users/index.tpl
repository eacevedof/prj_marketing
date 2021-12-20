<?php
/**
 * @var \App\Views\AppView $this
 * @var \App\Helpers\Views\DatatableHelper $dthelp
 * @var array $auth
 */
?>
<link href="/themes/valex/assets/plugins/datatable/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
<link href="/themes/valex/assets/plugins/datatable/css/buttons.bootstrap5.min.css" rel="stylesheet">
<link href="/themes/valex/assets/plugins/datatable/responsive.bootstrap5.css" rel="stylesheet" />
<link href="/themes/valex/assets/plugins/datatable/css/jquery.dataTables.min.css" rel="stylesheet">
<link href="/themes/valex/assets/plugins/datatable/responsive.dataTables.min.css" rel="stylesheet">
<link href="/themes/valex/assets/css/style.css" theme="valex" rel="stylesheet">

<script src="/themes/valex/assets/plugins/datatable/js/jquery.dataTables.min.js"></script>
<script src="/themes/valex/assets/plugins/datatable/datatables.min.js"></script>
<script src="/themes/valex/assets/plugins/datatable/js/dataTables.bootstrap5.js"></script>
<script src="/themes/valex/assets/plugins/datatable/js/dataTables.buttons.min.js"></script>
<script src="/themes/valex/assets/plugins/datatable/js/buttons.bootstrap5.min.js"></script>
<script src="/themes/valex/assets/plugins/datatable/js/jszip.min.js"></script>
<script src="/themes/valex/assets/plugins/datatable/js/buttons.html5.min.js"></script>
<script src="/themes/valex/assets/plugins/datatable/js/buttons.print.min.js"></script>
<script src="/themes/valex/assets/plugins/datatable/js/buttons.colVis.min.js"></script>
<script src="/themes/valex/assets/plugins/datatable/pdfmake/pdfmake.min.js"></script>
<script src="/themes/valex/assets/plugins/datatable/pdfmake/vfs_fonts.js"></script>

<div class="row row-sm">
  <div class="col-xl-12">
    <div class="card">
      <div class="card-header pb-0">
        <div class="d-flex justify-content-between">
          <h4 class="card-title mg-b-0"><?=$h1?></h4>
          <i class="mdi mdi-dots-horizontal text-gray"></i>
        </div>
      </div>
      <div class="card-body">
        <div class="table-responsive" id="div-table-datatable">
            <table id="table-datatable" class="table text-md-nowrap table-striped">
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
      </div>
    </div>
  </div>
</div>
<script type="module">
import dt_render from "/assets/js/common/datatable/dttable.js"
import {button} from "/assets/js/common/datatable/button.js"
import {rowswal} from "/assets/js/common/datatable/rowswal.js"
import {column} from "/assets/js/common/datatable/column.js"

const sessusrid = <?$this->_echo_js($auth["id"]);?>;
const sesprofile = <?$this->_echo_js($auth["id_profile"]);?>;

const PROFILES = {
  ROOT:"1",
  SYS_ADMIN:"2",
  BUSINESS_OWNER:"3",
  BUSINESS_MANAGER:"4",
}


column.add_rowbtn({
  btnid: "rowbtn-show",
  text: <?$this->_echo_js(__("Show"));?>
})

const is_editable = row => {
  const usrprof = row.id_profile
  const usrid = row.id
  return (
    !row?.delete_date && (
      (sesprofile===PROFILES.ROOT || sessusrid===usrid) ||
      (sesprofile===PROFILES.SYS_ADMIN && [PROFILES.BUSINESS_OWNER, PROFILES.BUSINESS_MANAGER].includes(usrprof)) ||
      (sesprofile===PROFILES.BUSINESS_OWNER)
    )
  )
}

const is_deletable = row => {
  const usrprof = row.id_profile
  return (
    !row?.delete_date && (
      (sesprofile===PROFILES.ROOT) ||
      (sesprofile===PROFILES.SYS_ADMIN && [PROFILES.BUSINESS_OWNER, PROFILES.BUSINESS_MANAGER].includes(usrprof)) ||
      (sesprofile===PROFILES.BUSINESS_OWNER && usrprof===PROFILES.BUSINESS_MANAGER)
    )
  )
}

column.add_rowbtn({
  btnid: "rowbtn-edit",
  render: (v,t,row) => {
    if (is_editable(row)) return `<button type="button" btnid="rowbtn-edit" uuid="${row?.uuid ?? ""}"><?$this->_echo(__("Edit"));?></button>`
    return ""
  }
})

column.add_rowbtn({
  btnid: "rowbtn-del",
  render: (v,t,row) => {
    if (is_deletable(row))
      return `<button type="button" btnid="rowbtn-del" uuid="${row?.uuid ?? ""}"><?$this->_echo(__("Remove"));?></button>`
    return ""
  }
})

rowswal.set_texts({
  delswal: {
    error: <?$this->_echo_js(__("<b>Error on delete</b>"));?>,
    success: <?$this->_echo_js(__("Data successfully deleted"));?>
  },
  undelswal: {
    error: <?$this->_echo_js(__("<b>Error on restore</b>"));?>,
    success: <?$this->_echo_js(__("Data successfully restored"));?>
  },
})

dt_render({
  URL_MODULE: "/restrict/users",
  ID_TABLE: "table-datatable",
  ITEMS_PER_PAGE: <?$dthelp->show_perpage();?>,
})
</script>