<?php
/**
 * @var App\Shared\Infrastructure\Views\AppView $this
 * @var App\Helpers\Views\DatatableHelper $dthelp
 * @var array $authuser
 * @var string $h1
 * @var ?string $idowner
 * @var bool $authread
 * @var bool $authwrite
 */
if(!isset($authread)) $authread=false;
if(!isset($authwrite)) $authwrite=false;

$this->_element("restrict/elem-bowdisabled");
$this->_element("common/elem-datatable-asset");
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
import auth from "/assets/js/restrict/auth.js"

auth.id_user = <?$this->_echo_js($authuser["id"]) ?>;
auth.id_profile = <?$this->_echo_js($authuser["id_profile"]) ?>;
auth.id_owner = <?$this->_echo_js((string) $idowner) ?>;
auth.readable = <?= (int)$authread ?>;
auth.writable = <?= (int)$authwrite ?>;

const is_infoable = row => {
  if (auth.is_root()) return true
  if (row.delete_date) return false
  if (auth.is_sysadmin()) return true
  if (auth.is_business_owner() && auth.have_sameowner(row.id_owner)) return true
  return (auth.is_business_manager() && auth.have_sameowner(row.id_owner) && (auth.can_write() || auth.can_read()))
}

const is_editable = row => {
  if (row.delete_date) return false
  if (auth.is_system()) return true
  if (auth.is_business_owner() && auth.have_sameowner(row.id_owner)) return true
  return (auth.is_business_manager() && auth.have_sameowner(row.id_owner) && auth.can_write())
}

const is_deletable = row => {
  if (row.delete_date) return false
  if (auth.is_root()) return true
  if (auth.is_sysadmin() && auth.can_write()) return true
  if (auth.is_business_owner() && auth.have_sameowner(row.id_owner)) return true
  return (auth.is_business_manager() && auth.have_sameowner(row.id_owner) && auth.can_write())
}

const is_restorable = row => {
  if (!row.delete_date) return false
  return auth.is_root()
}

dtcolumn.add_rowbtn({
  btnid: "rowbtn-show",
  render: (v,t,row) => {
    if (is_infoable(row)) return `<button type="button" btnid="rowbtn-show" uuid="${row?.uuid ?? ""}" class="btn btn-dark" title="info">
    <i class="mdi mdi-account-card-details"></i>
  </button>`
    return ""
  }
})

dtcolumn.add_rowbtn({
  btnid: "rowbtn-edit",
  render: (v,t,row) => {
    if (is_editable(row))
      return `<button type="button" btnid="rowbtn-edit" uuid="${row?.uuid ?? ""}" class="btn btn-info" title="edit">
      <i class="las la-pen"></i>
    </button>`
    return ""
  }
})

dtcolumn.add_rowbtn({
  btnid: "rowbtn-del",
  render: (v,t,row) => {
    if (is_deletable(row))
      return `<button type="button" btnid="rowbtn-del" uuid="${row?.uuid ?? ""}" class="btn btn-danger" title="remove">
      <i class="las la-trash"></i>
    </button>`
    return ""
  }
})

dtcolumn.add_rowbtn({
  btnid: "rowbtn-undel",
  render: (v,t,row) => {
    if (is_restorable(row))
      return `<button type="button" btnid="rowbtn-undel" uuid="${row?.uuid ?? ""}" class="btn btn-success" title="restore">
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
  URL_MODULE: "/restrict/promotions",
  ID_TABLE: "table-datatable",
  ITEMS_PER_PAGE: <?$dthelp->show_perpage();?>,
})
</script>
<?php
$this->_element("restrict/elem-modal-launcher");
?>