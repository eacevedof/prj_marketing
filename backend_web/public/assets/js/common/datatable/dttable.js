import {load_asset_css} from "/assets/js/common/utils.js"
import {
  add_page_to_url, get_page_from_url, get_url_with_params
} from "/assets/js/common/url.js"

import dtcolumn from "./column.js"
import dtsearch from "./search.js"
import dtbutton from "./button.js"

load_asset_css("common/spinner")

let OPTIONS = {},
  is_rendered = false,
  $table = null,
  dttable = null,

  column = null,
  search = null,
  button = null


const get_page = perpage => {
  let page = get_page_from_url(3)
  if (!page || isNaN(page)) {
    page = 1
    add_page_to_url(page, 3)
  }
  const pagemin = page - 1
  return pagemin * perpage
}

const on_everydraw = () => {
  dttable
    .column(0, {search:"applied", order:"applied"})
    .nodes()
    .each(($cell, i) => $cell.innerHTML = i+1)

  dtbutton($table, dttable).rowbuttons_listeners()
}

const get_init_conf = () => (
  {
    // l:length changing input control,
    // f: filtering, t: table, i:information sumary, p:pagination, r:processing (la r es necesria para la personalizacion de language.prcessiong)
    dom: "<'table-buttons'B>lfipr<bottam>p",
    searchDelay: 1500,
    buttons: {
      buttons: dtbutton($table, dttable).get_buttons(OPTIONS)
    },
    columnDefs: dtcolumn($table).get_columns(),
    responsive: true,
    serverSide: true,
    processing: true,
    lengthMenu: [25, 50, 75, 100],
    orderCellsTop: true,
    fixedHeader: true,
    order: [[ 1, "desc" ]],
    //language: get_language(),
    language: {
      processing:"<div class=\"spinner\" approle=\"spinner\"><div class=\"spinner-loader\"></div></div>"
    }
  }
)

const on_ajax = (data, fnrender) => {
  const urlsearch = $table.getAttribute("urlmodule").concat("/search")
  const URL_SEARCH = get_url_with_params(urlsearch, data)
  fetch(URL_SEARCH, {
    method: "GET",
    headers: new Headers({
      "Accept": "application/json",
      "Content-Type":"application/json",
      "Cache-Control":"max-age=640000"
    })
  })
  .then(response => response.json())
  .then(response => {
    fnrender({
      recordsTotal: response.data.total,
      recordsFiltered: response.data.total,
      data: response.data.result,
    })
  })
  .catch(error => {
    console.error("grid.error",error)
  })
  .finally(()=>{
    //remove_spinner()
  })
}

const dt_render = (options) => {
  OPTIONS = {...options}
  let idtable = OPTIONS.ID_TABLE
  const tablesel = `#${idtable}`

  $table = document.getElementById(idtable)
  if(!$table) return console.error(`table with id ${idtable} not found`)
  $table.setAttribute("urlmodule",OPTIONS.URL_MODULE)

  const dtconfig = {
    ...get_init_conf(),
    ...OPTIONS,
    ...{
      pageLength: OPTIONS.ITEMS_PER_PAGE,
      displayStart: get_page(OPTIONS.ITEMS_PER_PAGE),
    },

    ajax: on_ajax,

    initComplete: function() {
      //esto es único por idtable
      //alert("table")
      dtsearch($table, dttable).add_input_events()
      dtsearch($table, dttable).focus_global()
      is_rendered = true
      console.log("initComplete end table-ready")
    },

    drawCallback: on_everydraw
  }


  //button = dtbutton($table, dttable)
  //column = dtcolumn($table)
  //search = dtsearch($table, dttable)

  dttable = $(tablesel).DataTable(dtconfig)
  dttable
    .on("page.dt", function() {
      const pagemin = dttable.page.info()?.page ?? 0
      add_page_to_url(pagemin+1, 3)
    })
    .on("order.dt", function() {
      if (is_rendered) add_page_to_url(1, 3)
    })

}//dt_render

export default dt_render