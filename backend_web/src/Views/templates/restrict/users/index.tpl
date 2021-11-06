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
    <div id="demo_info" class="box"></div>
    <button type="button" id="btn-draw">draw table</button>
    https://datatables.net/examples/non_jquery/dt_events.html
    <table id="table-datatable" class="display" style="width:100%">
        <thead>
        <tr>
            <th approle="column-name">Name</th>
            <th approle="column-name">Position</th>
            <th approle="column-name">Office</th>
            <th approle="column-name">Extn.</th>
            <th approle="column-name">Start date</th>
            <th approle="column-name">Salary</th>
        </tr>
        </thead>
        <tfoot>
        <tr>
            <th>Name</th>
            <th>Position</th>
            <th>Office</th>
            <th>Extn.</th>
            <th>Start date</th>
            <th>Salary</th>
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
  function debounce(func, timeout = 300){
    let timer;
    return (...args) => {
      clearTimeout(timer);
      timer = setTimeout(() => { func.apply(this, args); }, timeout);
    };
  }

let table = null
const jqid = "#table-datatable"

var eventFired = function ( type ) {
    var n = document.querySelector('#demo_info');
    n.innerHTML += '<div>'+type+' event - '+new Date().getTime()+'</div>';
    n.scrollTop = n.scrollHeight;
}

function refresh (table) {
  //table.clear()
  //table.data = [{"name":"Tiger Alfa","position":"xxxx","office":"Edinburgh","extn":"5421","start_date":"2011-04-25","salary":"$3,120"},{"name":"Garrett Winters","position":"Director","office":"Edinburgh","extn":"8422","salary":"$5,300","start_date":"2011-07-25"},{"name":"Tiger Alfa","position":"xxxx","office":"Edinburgh","extn":"5421","start_date":"2011-04-25","salary":"$3,120"},{"name":"Garrett Winters","position":"Director","office":"Edinburgh","extn":"8422","salary":"$5,300","start_date":"2011-07-25"},{"name":"Tiger Alfa","position":"xxxx","office":"Edinburgh","extn":"5421","start_date":"2011-04-25","salary":"$3,120"},{"name":"Garrett Winters","position":"Director","office":"Edinburgh","extn":"8422","salary":"$5,300","start_date":"2011-07-25"}]
  table.ajax.reload()
}

$(document).ready(function (){

    $("#btn-draw").on("click", () => refresh(table) )


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
    var title = $(this).text();
    console.log("header table search:", this, "title:", title)
    $(this).html( '<input type="text" placeholder="Search '+title+'" class="column_search" approle="column-search" />' );
  } );

  table = $("#table-datatable").DataTable( {
    orderCellsTop: true,
    fixedHeader: true,
    pageLength: 10,
    language: trs,
    // Setup - add a text input to each footer cell
    initComplete: function () {
      // Apply the search
      $(`[approle='column-search']`).each((i, $input) => {

        if($input)
            $($input).on( 'keyup change clear', debounce(function (e) {
              console.log("column_search keyup change and clear", e)
            },1000) )
      });
      console.log("init complete")
    },
    ajax: {
      url:'/restrict/users/1/search',
      dataSrc: function (data) {
        console.log("dataSrc.data:", data)
        return data.data.result
      }
    },
    dom: 'Bfrtip',
    buttons: [

      'colvis',
      'excel',
      'print'
    ],
    columns: [
      { data: "name" },
      { data: "position" },
      { data: "office" },
      { data: "extn" },
      { data: "start_date" },
      { data: "salary" },
    ]
  });

  table.on("draw", function (){
    console.log("on draw")
  })

  table
    .on('order', function (e) {
      e.preventDefault();
      //post all
      console.log("order",e)
      eventFired( 'Order' );
    })
    .on('search', function (e) {
      e.preventDefault();
      //post all
      console.log("search",e)
      eventFired( 'Search' );
    })
    .on('page', function (e) {
      e.preventDefault();
      console.log("page",e)
      eventFired( 'Page' );
    });

})

</script>

