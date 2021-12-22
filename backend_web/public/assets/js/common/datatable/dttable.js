import req from "/assets/js/common/req.js"
import {load_asset_css} from "/assets/js/common/utils.js"
import {
  add_page_to_url, get_page_from_url, get_url_with_params
} from "/assets/js/common/url.js"
import {SNACK} from "/assets/js/common/snackbar.js"

import dtcolumn from "./column.js"
import dtsearch from "./search.js"
import dtbutton from "./button.js"

load_asset_css("common/spinner")

let OPTIONS = {},
  is_rendered = false,
  idtable = "",
  $table = null,
  dttable = null


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
      //top buttons
      buttons: dtbutton($table, dttable).get_topbuttons()
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

const _show_error = error => window.snack
    .set_time(5)
    .set_color(SNACK.ERROR)
    .set_inner("Error: ".concat(error.toString()))
    .show()

const _hide_spinner = () => {
  const $div = document.getElementById(`${idtable}_processing`)
  if($div) $div.style.display = "none"
}

const on_ajax = async (data, fnrender) => {
  const urlsearch = $table.getAttribute("urlmodule").concat("/search")
  const URL_SEARCH = get_url_with_params(urlsearch, data)

  const response = await req.get(URL_SEARCH)
  if(response?.errors) {
    _hide_spinner()
    return _show_error(response.errors[0])
  }

  fnrender({
    recordsTotal: response.total,
    recordsFiltered: response.total,
    data: response.result,
  })
}

const dt_render = (options) => {
  OPTIONS = {...options}
  idtable = OPTIONS.ID_TABLE
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