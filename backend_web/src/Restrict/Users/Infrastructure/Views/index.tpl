<?php
/**
 * @var App\Shared\Infrastructure\Views\AppView $this
 * @var App\Shared\Infrastructure\Helpers\Views\DatatableHelper $datatableHelper
 * @var array $authUser
 * @var string $h1
 * @var bool $authRead
 * @var bool $authWrite
 */
use App\Shared\Infrastructure\Helpers\RoutesHelper as Routes;
if (!isset($authRead)) $authRead=false;
if (!isset($authWrite)) $authWrite=false;
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
                <?= $datatableHelper->getHtmlThs() ?>
              </tr>
              <tr row="search" class="hidden">
                <?= $datatableHelper->getSearchableTds() ?>
              </tr>
            </thead>
            <tbody approle="tbody"></tbody>
            <tfoot>
              <tr>
                <?= $datatableHelper->getHtmlTdsForTableFoot() ?>
              </tr>
            </tfoot>
          </table>
        </div><!--table-responsive-->
      </div><!--cardbody-->
    </div><!--card-->
  </div><!--col-->
</div><!--row-->
<script type="module">
import dt_render from "/assets/js/common/datatable/dttable.js"
import {rowswal} from "/assets/js/common/datatable/rowswal.js"
import {dtcolumn} from "/assets/js/common/datatable/dtcolumn.js"
import auth from "/assets/js/restrict/auth.js"

auth.id_user = <?php $this->_echoJs($authUser["id"]) ?>;
auth.id_profile = <?php $this->_echoJs($authUser["id_profile"]) ?>;
auth.readable = <?= (int)$authRead ?>;
auth.writable = <?= (int)$authWrite ?>;

const is_infoable = row => {
  if (auth.is_root()) return true
  if (row.delete_date) return false
  if (row.id === auth.id_user) return true
  if ((auth.is_sysadmin() && !auth.is_root(row.id_profile)) || auth.is_business_owner())
    return true

  console.log("is_infoable():","is-bm",auth.is_business_manager(), "auth-can-write",auth.can_write(), "auth-can-read", auth.can_read())
  return (auth.is_business_manager() && (auth.can_write() || auth.can_read()))
}

const is_editable = row => {
  if (row.delete_date) return false
  if (auth.is_root() || row.id === auth.id_user) return true

  if ((auth.is_sysadmin() && auth.is_business(row.id_profile)) || auth.is_business_owner())
    return true
  return (auth.is_business_manager() && auth.can_write())
}

const is_deletable = row => {
  if (row.delete_date) return false
  if (auth.is_root() && row.id !== auth.id_user) return true
  console.log("is_deletable():","is-bow",auth.is_business_owner(), "row-profile",row.id_profile, "is-bm-prof",auth.is_business_manager(row.id_profile))
  if (auth.is_business_owner() && auth.is_business_manager(row.id_profile)) return true
  return (auth.is_business_manager() && auth.can_write() && row.id !== auth.id_user)
}

const is_restorable = row => {
  if (!row.delete_date) return false
  return (auth.is_root() && row.id !== auth.id_user)
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

//***********
//columns
//***********
dtcolumn.add_column({
  data: "uuid",
  render: (v,t,row) => {
    let tpl = `<span class="">${v}</span><sub style="color: #ccc">(${row.id})</sub>`
    if (row.delete_date)
      tpl = `<span class="tx-danger">${v}</span><sub style="color:#ccc">(${row.id})</sub>`
    return tpl
  }
})

dtcolumn.add_column({
  data: "email",
  render: (v,t,row) => {
    let cls = ""
    if (row.id_profile === auth.PROFILES.BUSINESS_OWNER) cls = `style="color:#30B131"`
    if (row.id_profile === auth.PROFILES.BUSINESS_MANAGER) cls = `style="color:#07a5c1"`
    if (row.id_profile === auth.PROFILES.SYS_ADMIN) cls = `style="color:#d6690a"`
    if (row.id_profile === auth.PROFILES.ROOT) cls = `style="color:#bf074e"`
    let tpl = `<span ${cls}>${v}</span>`
    return tpl
  }
})

rowswal.set_texts({
  delswal: {
    error: <?php $this->_echoJs(__("<b>Error on delete</b>"));?>,
    success: <?php $this->_echoJs(__("Data successfully deleted"));?>
  },
  undelswal: {
    error: <?php $this->_echoJs(__("<b>Error on restore</b>"));?>,
    success: <?php $this->_echoJs(__("Data successfully restored"));?>
  },
})

dt_render({
  URL_MODULE: <?php $this->_echoJs(Routes::getUrlByRouteName("module.users", ["page"=>"","_nods"])); ?>,
  ID_TABLE: "table-datatable",
  ITEMS_PER_PAGE: <?php $datatableHelper->showPerPageInfo();?>,
})
</script>
<?php
$this->_element("restrict/elem-modal-launcher");
?>