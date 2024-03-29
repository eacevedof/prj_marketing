<?php
/**
 * @var App\Shared\Infrastructure\Views\AppView $this
 * @var App\Shared\Infrastructure\Helpers\Views\DatatableHelper $datatableHelper
 * @var array $authUser
 * @var string $h1
 * @var ?string $idOwner
 * @var bool $authRead
 * @var bool $authWrite
 */
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

auth.id_user = <?php $this->_echoJs($authUser["id"]) ?>;
auth.id_profile = <?php $this->_echoJs($authUser["id_profile"]) ?>;
auth.id_owner = <?php $this->_echoJs((string) $idOwner) ?>;
auth.readable = <?= (int)$authRead ?>;
auth.writable = <?= (int)$authWrite ?>;

const is_infoable = row => {
  if (auth.is_root()) return true
  if (row.delete_date) return false
  if (auth.is_sysadmin()) return true
  if (auth.is_business_owner() && auth.have_sameowner(row.id_owner)) return true
  return (auth.is_business_manager() && auth.have_sameowner(row.id_owner) && (auth.can_write() || auth.can_read()))
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
  data: "e_promotion",
  render: (v,t,row) => {
    if (parseInt(row.is_test)) return `<span class="tx-gray-500">${v}</span>`
    if (parseInt(row.subs_status)===3) return `<span class="tx-success">${v}</span>`
    return `<span>${v}</span>`
  }
})

dt_render({
  URL_MODULE: "/restrict/billings",
  ID_TABLE: "table-datatable",
  ITEMS_PER_PAGE: <?php $datatableHelper->showPerPageInfo();?>,
})
</script>
<?php
$this->_element("restrict/elem-modal-launcher");
?>