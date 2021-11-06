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
    https://datatables.net/examples/non_jquery/dt_events.html
    <table id="table-datatable" class="display" style="width:100%">
        <thead>
        <tr>
            <th>Name</th>
            <th>Position</th>
            <th>Office</th>
            <th>Extn.</th>
            <th>Start date</th>
            <th>Salary</th>
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
<script type="module">
var eventFired = function ( type ) {
    var n = document.querySelector('#demo_info');
    n.innerHTML += '<div>'+type+' event - '+new Date().getTime()+'</div>';
    n.scrollTop = n.scrollHeight;
}
document.addEventListener('DOMContentLoaded', function () {
//  let table = new DataTable('#table-datatable');


});


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

  $('#table-datatable tfoot th').each( function () {
    var title = $(this).text();
    console.log("redner inputs in footer",title)
    $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
  } );
  const table = $("#table-datatable").DataTable( {
    language: trs,
    // Setup - add a text input to each footer cell
    initComplete: function () {
      // Apply the search
      this.api().columns().every( function () {
        var that = this;

        $( 'input', this.footer() ).on( 'keyup change clear', function () {
          if ( that.search() !== this.value ) {
            that
              .search( this.value )
              .draw();
          }
        } );
      } );
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
    console.log("draw")
  })

  table
    .on('order', function () {
      eventFired( 'Order' );
    })
    .on('search', function () {
      eventFired( 'Search' );
    })
    .on('page', function () {
      eventFired( 'Page' );
    });
})

</script>

