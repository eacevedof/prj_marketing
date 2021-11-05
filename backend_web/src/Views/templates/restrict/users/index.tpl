<?php
/**
 * @var \App\Views\AppView $this
 */
?>
<h1><?=$h1?></h1>
<div id="div-datatable">
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
<script>
  const data = []
  $("#table-datatable").DataTable( {
    data: data,
    columns: [
      { data: "name" },
      { data: "position" },
      { data: "office" },
      { data: "extn" },
      { data: "start_date" },
      { data: "salary" },
    ]
  } );
</script>

