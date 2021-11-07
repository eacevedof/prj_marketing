<?php
/**
 * @var \App\Views\AppView $this
 */
?>
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.0.1/css/bootstrap.min.css"/>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs5/jszip-2.5.0/dt-1.11.3/b-2.0.1/b-colvis-2.0.1/b-html5-2.0.1/b-print-2.0.1/cr-1.5.5/date-1.1.1/fh-3.2.0/r-2.2.9/rg-1.1.4/sb-1.3.0/sp-1.4.0/sl-1.3.3/datatables.min.css"/>

<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.0.1/js/bootstrap.bundle.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/v/bs5/jszip-2.5.0/dt-1.11.3/b-2.0.1/b-colvis-2.0.1/b-html5-2.0.1/b-print-2.0.1/cr-1.5.5/date-1.1.1/fh-3.2.0/r-2.2.9/rg-1.1.4/sb-1.3.0/sp-1.4.0/sl-1.3.3/datatables.min.js"></script>
<h1><?=$h1?></h1>
<div id="div-datatable">
    https://datatables.net/examples/non_jquery/dt_events.html
    <table id="table-datatable" class="display" style="width:100%">
    <thead>
    <tr>
        <th>uuid</th>
        <th>fullname</th>
        <th>email</th>
        <th>phone</th>
        <th>id_profile</th>
        <th>id_nationality</th>
        <th>id_language</th>
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
        <th>uuid</th>
        <th>fullname</th>
        <th>email</th>
        <th>phone</th>
        <th>id_profile</th>
        <th>id_nationality</th>
        <th>id_language</th>
    </tr>
    </tfoot>
    </table>
</div>
<style>
  body {
    font: 90%/1.45em "Helvetica Neue", HelveticaNeue, Verdana, Arial, Helvetica, sans-serif;
    margin: 0;
    padding: 0;
    color: #333;
    background-color: #fff;
  }

</style>
<script type="module">
import {
  debounce, getUrlPosition,
  addPageInUrlByPosition, addPageToUrl
} from "/assets/js/common/utils.js"

let table = null
const jqid = "#table-datatable"

$(document).ready(function (){

  const trs = {
    processing:     "Procesando...",
    search:         "Busqueda&nbsp;:",
    lengthMenu:    "Afficher _MENU_ &eacute;l&eacute;ments",
    info:           "Affichage de l'&eacute;lement _START_ &agrave; _END_ sur _TOTAL_ &eacute;l&eacute;ments",
    infoEmpty:      "Affichage de l'&eacute;lement 0 &agrave; 0 sur 0 &eacute;l&eacute;ments",
    infoFiltered:   "(filtr&eacute; de _MAX_ &eacute;l&eacute;ments au total)",
    infoPostFix:    "",
    loadingRecords: "Cargando...",
    zeroRecords:    "Aucun &eacute;l&eacute;ment &agrave; afficher",
    emptyTable:     "Aucune donnée disponible dans le tableau",
    paginate: {
      first:      "Primer",
      previous:   "Anterior",
      next:       "Siguiente",
      last:       "Último"
    },
    aria: {
      sortAscending:  ": activer pour trier la colonne par ordre croissant",
      sortDescending: ": activer pour trier la colonne par ordre décroissant"
    }
  }

  // Setup - add a text input to each footer cell
  $(`[approle="column-name"]`).each( function () {
    const $td = this
    const title = $($td).text();
    const colidx = $td.getAttribute("appcolidx")
    $(this).html(
      `<input type="text" placeholder="Search ${title}" approle="column-search" appcolidx="${colidx}" />`
    );
  });

  table = $("#table-datatable").DataTable({
    processing: true,
    serverSide: true,
    //bSort:false, //desactiva flechas de ordenacion
    orderCellsTop: true,
    fixedHeader: true,
    pageLength: 25,
    language: trs,
    // Setup - add a text input to each footer cell
    initComplete: function () {
      console.log("initComplet")
      addPageInUrlByPosition(2)
      let page = getUrlPosition(2)

      if(isNaN(page) || parseInt(page)<0) {
        page=0
      }
      else {
          page = page-1
      }

      this.api().page(page).draw('page')
      // Apply the search
      $(`[approle='column-search']`).each((i, $input) => {

        if($input) {
          console.log("column-search",$input)
          $($input).on('keyup change clear', debounce(function (e) {
            const idx = $input.getAttribute("appcolidx")
            const value = $input.value
            table.columns(idx).search(value).draw() //sin draw no busca
          }, 1000))
        }
      });
      console.log("init complete")
    },
    ajax: function(data, callback, settings) {
      console.log("calling ajax with data:", data)
      if(table?.page) {
        const page = table.page.info().page+1
        addPageToUrl(page)
      }

      // make a regular ajax request using data.start and data.length
      $.get('/restrict/users/search', data, function(res) {
        console.log("res", res)
        // map your server's response to the DataTables format and pass it to
        // DataTables' callback
        callback({
          recordsTotal: res.data.recordsTotal,
          recordsFiltered: res.data.recordsFiltered,
          data: res.data.result
        });
      });
    },
    dom: 'Bfrtip',//buttons in dom
    buttons: [
      'colvis',
      'excel',
      'print'
    ],
    columns: [
      { data: "uuid" },
      { data: "fullname" },
      { data: "email" },
      { data: "phone" },
      { data: "id_profile" },
      { data: "id_nationality" },
      { data: "id_language" },
    ]
  });

  /*
  table.on("draw", function (){
    console.log("on draw")
  })

  table
    .on('order', function (e) {
      const order = table.order()
      console.log("order colidx:",order[0][0],"orientation",order[0][1])
    })
    .on('search', function (e) {
      //post all
      console.log("search",e)
    })
    .on('page', function (e) {
      //e.preventDefault();
      //page se mueve de 0 a n
      const page = table.page.info()
      console.log("page:", page.page ,"pages:", page.pages )
      //eventFired( 'Page' );
    });

   */

})

</script>

