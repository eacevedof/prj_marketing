<?php
/**
 * @var \App\Views\AppView $this
 * @var \App\Helpers\Views\DatatableHelper $dthelp
 * @var array $authuser
 */
echo $this->_element("common/elem-datatable-asset");
?>
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
import {rowswal} from "/assets/js/common/datatable/rowswal.js"
import {dtcolumn} from "/assets/js/common/datatable/dtcolumn.js"

const sessusrid = <?$this->_echo_js($authuser["id"]);?>;
const sesprofile = <?$this->_echo_js($authuser["id_profile"]);?>;

const PROFILES = {
  ROOT:"1",
  SYS_ADMIN:"2",
  BUSINESS_OWNER:"3",
  BUSINESS_MANAGER:"4",
}

const is_infoable = row => {
  const usrprof = row.id_profile
  return (
      !row?.delete_date && (
          sesprofile===PROFILES.ROOT ||
          (sesprofile===PROFILES.SYS_ADMIN && [PROFILES.SYS_ADMIN, PROFILES.BUSINESS_OWNER, PROFILES.BUSINESS_MANAGER].includes(usrprof)) ||
          sesprofile===PROFILES.BUSINESS_OWNER
      )
  )
}

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

dtcolumn.add_rowbtn({
  btnid: "rowbtn-show",
  render: (v,t,row) => {
    if (is_infoable(row)) return `<button type="button" btnid="rowbtn-show" uuid="${row?.uuid ?? ""}" class="btn btn-dark">
      <i class="mdi mdi-account-card-details"></i>
    </button>`
    return ""
  }
})

dtcolumn.add_rowbtn({
  btnid: "rowbtn-edit",
  render: (v,t,row) => {
    if (is_editable(row))
      return `<button type="button" btnid="rowbtn-edit" uuid="${row?.uuid ?? ""}" class="btn btn-info">
        <i class="las la-pen"></i>
      </button>`
    return ""
  }
})

dtcolumn.add_rowbtn({
  btnid: "rowbtn-del",
  render: (v,t,row) => {
    if (is_deletable(row))
      return `<button type="button" btnid="rowbtn-del" uuid="${row?.uuid ?? ""}" class="btn btn-danger">
        <i class="las la-trash"></i>
      </button>`
    return ""
  }
})

dtcolumn.add_rowbtn({
  btnid: "rowbtn-undel",
  render: (v,t,row) => {
    if (sesprofile===PROFILES.ROOT && row.delete_date)
      return `<button type="button" btnid="rowbtn-undel" uuid="${row?.uuid ?? ""}" class="btn btn-success">
        <i class="las la-undo-alt"></i>
      </button>`
    return ""
  }
})

dtcolumn.add_column({
  data: "uuid",
  render: (v,t,row) => {
    let tpl = `<span class="">${v}</span><sub style="color: #ccc">(${row.id})</sub>`
    if (row.delete_date)
      tpl = `<span class="tx-danger">${v}</span><sub style="color:#ccc">(${row.id})</sub>`
    return tpl
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