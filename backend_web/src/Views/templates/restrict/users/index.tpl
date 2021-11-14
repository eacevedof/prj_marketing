<?php
/**
 * @var \App\Views\AppView $this
 * @var \App\Helpers\Views\DatatableHelper $dt
 */
use App\Factories\HelperFactory as HF;
$dt = HF::get("Views/Datatable");
$dt->add_column("uuid")
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
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.0.1/css/bootstrap.min.css"/>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs5/jszip-2.5.0/dt-1.11.3/b-2.0.1/b-colvis-2.0.1/b-html5-2.0.1/b-print-2.0.1/cr-1.5.5/date-1.1.1/fh-3.2.0/r-2.2.9/rg-1.1.4/sb-1.3.0/sp-1.4.0/sl-1.3.3/datatables.min.css"/>

<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.0.1/js/bootstrap.bundle.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/v/bs5/jszip-2.5.0/dt-1.11.3/b-2.0.1/b-colvis-2.0.1/b-html5-2.0.1/b-print-2.0.1/cr-1.5.5/date-1.1.1/fh-3.2.0/r-2.2.9/rg-1.1.4/sb-1.3.0/sp-1.4.0/sl-1.3.3/datatables.min.js"></script>
<h1><?=$h1?></h1>
<div id="div-datatable">
    <table id="table-datatable" class="display" style="width:95%">
        <thead>
            <tr>
                <?= $dt->get_ths() ?>
            </tr>
            <tr row="search" class="hidden">
                <?= $dt->get_search_tds() ?>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <?= $dt->get_tf() ?>
            </tr>
        </tfoot>
    </table>
</div>
<script type="module">
import dt_render from "/assets/js/common/dttable.js?<?=$now?>"

window.addEventListener("load", () => dt_render({
  ID_TABLE: "table-datatable",
  ITEMS_PER_PAGE: <?$dt->show_perpage();?>,
  URL_SEARCH: "/restrict/users/search",
  URL_ADD: "/restrict/users/insert",
  BUTTONS: {
    INSERT: {
      LABEL: <?$this->echo_js(__("Add"));?>,
      TOOLTIP: <?$this->echo_js(__("Add"));?>,
    },
    REFRESH: {
      LABEL: <?$this->echo_js(__("Refresh"));?>,
      TOOLTIP: <?$this->echo_js(__("Refresh"));?>,
    },
    FILTER_SHOW: {
      LABEL: <?$this->echo_js(__("Show filters"));?>,
      TOOLTIP: <?$this->echo_js(__("Show filters"));?>,
    },
    FILTER_RESET: {
      LABEL: <?$this->echo_js(__("Reset filters"));?>,
      TOOLTIP: <?$this->echo_js(__("Reset filters"));?>,
    },
  }
}))
</script>

