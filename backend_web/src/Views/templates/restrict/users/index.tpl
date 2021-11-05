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
<script type="module">

  const request = new Request("/restrict/users/1/search", {
    method: 'GET',
    headers: new Headers({
      'Accept': 'application/json',
      'custom-security':'XXXX',
      'Purchase-Code':'XXXXXXX',
      'Content-Type':'application/json',
      'Cache-Control':'max-age=640000'
    })
  });
    fetch(request)
    .then((response) => response.json())
    .then((responseJson) => {
      //console.log("response",responseJson)
    })
    .catch((error) => {
      console.error(error);
    });


  $("#table-datatable").DataTable( {
    "ajax":
      {
        "url":'/restrict/users/1/search',
        "dataSrc": function (data) {
          console.log("data",data.data.result)
          return data.data.result
          const d = [
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
            },
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
            },
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
          console.log("d",d)
        }
      },
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

