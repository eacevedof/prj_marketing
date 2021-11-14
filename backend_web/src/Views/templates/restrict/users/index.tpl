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
    ->add_action("delete")
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
import {debounce} from "/assets/js/common/utils.js"
import {
  add_page_to_url, get_page_from_url
} from "/assets/js/common/url.js"

let is_rendered = false
let $dttable = null
const tableid = "table-datatable"
const tablesel = `#${tableid}`
const $table = document.getElementById(tableid)

const get_page = perpage => {
  let page = get_page_from_url(3)
  if (!page || isNaN(page)) {
    page = 1
    add_page_to_url(page, 3)
  }
  const pagemin = page - 1
  return pagemin * perpage
}

const add_filter_events = $dttable => {
  if (!$dttable) return
  const debouncetime = 1000

  const on_event = e => {
    const $input = e.target
    const colidx = $input.getAttribute("appcolidx")
    if (!colidx) return
    const value = $input.value
    //sin draw no busca
    $dttable.columns(colidx).search(value).draw()
  }

  const inputs = $table.querySelectorAll(`[approle="column-search"]`)
  inputs.forEach($input => $input.addEventListener("input", debounce(e => on_event(e), debouncetime)))
}

const get_language = () => (
    <?$dt->show_lanaguage();?>
)

const reset_filters = () => {
  const inputs = Array.from(document.querySelectorAll(`[approle="column-search"]`))
  const $input = $table.querySelector(`[type="search"]`)
  inputs.push($input)
  inputs.forEach( $input => $input.value = "")
  $dttable.search("").columns().search("").draw()
}

const get_columns = () => {
  const cols = [
    "uuid:string",
    "fullname:string",
    "email:string",
    "phone:string",
    "id_profile:string",
    "id_nationality:string",
    "id_language:string",
  ]

  const final = []
  cols.forEach((colconfig, i )=> {
    const [colname, type] = colconfig.split(":")
    console.log("colanme",colname, "type:", type)
    const obj = {
      targets: i,
      data: colname,
      //searchable: false, no afecta en nada
      visible: true,
      render: function (data, type, row) {
        return data
      }
    }

    final.push(obj)
  })

  final.push({
      targets: -1,
      data: null,
      render: function(data, type, row) {
        const links = [
          `<span>show</span>`,
          `<span>edit</span>`,
          `<span>del</span>`,
        ];

        return links.join("&nbsp;");
      },
    })
  console.log(final)
  return final
}

const get_buttons = () => [
  {
    text: "button xxx",
    action: function (e, dt, node, config) {
      window.location.href = "#";
    },
    className: "button small button-action add",
    attr: {
      "data-tooltip": <?= json_encode(__("Add")) ?>
    }
  },
  {
    text: "Clear search",
    action: function (e, dt, node, config) {
        reset_filters()
    },
    attr: {
      "data-tooltip": <?= json_encode(__("Add")) ?>
    }
  },
  {
    text: "refresh",
    action: function (e, dt, node, config) {
      $dttable.draw()
    },
    attr: {
      "data-tooltip": <?= json_encode(__("Add")) ?>
    }
  },
  {
    text: "show filters",
    action: function (e, dt, node, config) {
      const $row = $table.querySelector(`tr[row="search"]`)
      if ($row) {
        //$row.classList.contains("hidden") ? $row.classList.remove("hidden"): $row.classList.add("hidden")
        $row.classList.toggle("hidden")
      }
    },
    attr: {
      "data-tooltip": <?= json_encode(__("Add")) ?>
    }
  },
]

const on_document_ready = () => {

  const ITEMS_PER_PAGE = <?$dt->show_perpage();?>

  $dttable = $(tablesel).DataTable({
    dom: "Blftipr",
    //searchDelay: 1500,
    responsive: true,
    processing: true,
    //lengthMenu: [[15, 30, 60, 90], [15, 30, 60, 90]],
    buttons: {
      buttons: get_buttons()
    },
    serverSide: true,
    orderCellsTop: true,
    fixedHeader: true,
    pageLength: ITEMS_PER_PAGE,
    //language: get_language(),
    displayStart: get_page(ITEMS_PER_PAGE),
    columnDefs: get_columns(),
    scrollX: false,

    ajax: function(data, fnRender, settings) {
      $.get("/restrict/users/search", data, function(res) {
        console.log("get request start")
        fnRender({
          recordsTotal: res.data.recordsTotal,
          recordsFiltered: res.data.recordsFiltered,
          data: res.data.result,
        })
        console.log("get request end")
      })//get
      console.log("on ajax end")
    },//ajax

    // Setup - add a text input to each footer cell
    initComplete: function() {
      console.log("initComplete start")
      add_filter_events($dttable)
      is_rendered = true
      console.log("initComplete end")
    },
  });

  $dttable.on("page.dt", function() {
    const pagemin = $dttable.page.info()?.page ?? 0
    add_page_to_url(pagemin+1, 3)
  })
  .on("order.dt", function() {
    if (is_rendered) add_page_to_url(1, 3)
  })

}// on_document_ready

window.addEventListener("load", on_document_ready)
</script>

