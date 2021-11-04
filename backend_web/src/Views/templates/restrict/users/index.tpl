<?php
/**
 * @var \App\Views\AppView $this
 */
?>
<h1><?=$h1?></h1>
<div id="div-datatable">
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
  const data = [
    {
      "name":       "Tiger Nixon",
      "position":   "System Architect",
      "office":     "Edinburgh",
      "extn":       "5421",
      "start_date": "2011/04/25",
      "salary":     "$3,120",
    },
    {
      "name":       "Garrett Winters",
      "position":   "Director",
      "office":     "Edinburgh",
      "extn":       "8422",
      "salary":     "$5,300",
      "start_date": "2011/07/25",
    }
  ]
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

