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
    ->add_column("id_nationality")
    ->add_column("id_language")
?>
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.0.1/css/bootstrap.min.css"/>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs5/jszip-2.5.0/dt-1.11.3/b-2.0.1/b-colvis-2.0.1/b-html5-2.0.1/b-print-2.0.1/cr-1.5.5/date-1.1.1/fh-3.2.0/r-2.2.9/rg-1.1.4/sb-1.3.0/sp-1.4.0/sl-1.3.3/datatables.min.css"/>

<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.0.1/js/bootstrap.bundle.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/v/bs5/jszip-2.5.0/dt-1.11.3/b-2.0.1/b-colvis-2.0.1/b-html5-2.0.1/b-print-2.0.1/cr-1.5.5/date-1.1.1/fh-3.2.0/r-2.2.9/rg-1.1.4/sb-1.3.0/sp-1.4.0/sl-1.3.3/datatables.min.js"></script>
<h1><?=$h1?></h1>
<div id="div-datatable">
    <table id="table-datatable" class="display" style="width:100%">
        <thead>
            <tr>
                <?= $dt->get_ths() ?>
            </tr>
            <tr row="search">
                <td approle="column-name" appcol="uuid" appcolidx="0">uuid</td>
                <td approle="column-name" appcol="fullname" appcolidx="1">fullname</td>
                <td approle="column-name" appcol="email" appcolidx="2">email</td>
                <td approle="column-name" appcol="phone" appcolidx="3">phone</td>
                <td approle="column-name" appcol="id_profile" appcolidx="4">id_profile</td>
                <td approle="column-name" appcol="id_nationality" appcolidx="5">id_nationality</td>
                <td approle="column-name" appcol="id_language" appcolidx="5">id_language</td>
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
let $table = null
const tableid = "table-datatable"
const tablesel = `#${tableid}`

const get_page = perpage => {
  let page = get_page_from_url(3)
  if (!page || isNaN(page)) {
    page = 1
    add_page_to_url(page, 3)
  }
  const pagemin = page - 1
  return pagemin * perpage
}

const add_filter_fileds = () => {
  const columns = document.querySelectorAll(`[approle="column-name"]`)
    columns.forEach($column => {
      const title = $column.textContent
      const colidx = $column.getAttribute("appcolidx")
      if (colidx)
        $column.innerHTML = `<input type="text" placeholder="Search ${title}" approle="column-search" appcolidx="${colidx}" />`
  })
}

const add_filter_events = $table => {
  if (!$table) return
  const debouncetime = 1000

  const on_event = e => {
    const $input = e.target
    const colidx = $input.getAttribute("appcolidx")
    if (!colidx) return
    const value = $input.value
    //sin draw no busca
    $table.columns(colidx).search(value).draw()
  }

  const inputs = document.querySelectorAll(`[approle="column-search"][type="text"]`)
  inputs.forEach($input => $input.addEventListener("input", debounce(e => on_event(e), debouncetime)))
}

const get_translations = () => (
  {
    processing: "Procesando...",
    search: "Busqueda&nbsp;:",
    lengthMenu: "Afficher _MENU_ &eacute;l&eacute;ments",
    info: "Affichage de l&eacute;lement _START_ &agrave; _END_ sur _TOTAL_ &eacute;l&eacute;ments",
    infoEmpty: "Affichage de l&eacute;lement 0 &agrave; 0 sur 0 &eacute;l&eacute;ments",
    infoFiltered: "(filtr&eacute; de _MAX_ &eacute;l&eacute;ments au total)",
    infoPostFix: "",
    loadingRecords: "Cargando...",
    zeroRecords: "Aucun &eacute;l&eacute;ment &agrave; afficher",
    emptyTable: "Aucune donnée disponible dans le tableau",
    paginate: {
      first: "Primer",
      previous: "Anterior",
      next: "Siguiente",
      last: "Último"
    },
    aria: {
      sortAscending: ": activer pour trier la colonne par ordre croissant",
      sortDescending: ": activer pour trier la colonne par ordre décroissant"
    }
  }
)

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
      render: function (data, type, row) {
        return data
      }
    }

    final.push(obj)
  })
  console.log(final)
  return final
}

const get_buttons = () => []

const on_document_ready = () => {

  const ITEMS_PER_PAGE = <?$dt->show_perpage();?>

  add_filter_fileds()

  $table = $(tablesel).DataTable({
    dom: "Blftipr",
    searchDelay: 1500,
    responsive: true,
    processing: true,
    lengthMenu: [[15, 30, 60, 90], [15, 30, 60, 90]],
    buttons: get_buttons(),
    serverSide: true,
    orderCellsTop: true,
    fixedHeader: true,
    pageLength: ITEMS_PER_PAGE,
    language: get_translations(),
    displayStart: get_page(ITEMS_PER_PAGE),

    columnDefs: get_columns(),

    ajax: function(data, fnRender, settings) {
      console.log("ajax start", settings)
      data.myExtra = "hola extra"
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
      add_filter_events($table)
      is_rendered = true
      console.log("initComplete end")
    },
  });

  $table.on( "page.dt", function() {
    const pagemin = $table.page.info()?.page ?? 0
    add_page_to_url(pagemin+1, 3)
  })
  .on("order.dt", function() {
    if (is_rendered) add_page_to_url(1, 3)
  })

}// on_document_ready

window.addEventListener("load", on_document_ready)
</script>

