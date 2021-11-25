
import {load_asset_css} from "/assets/js/common/utils.js"
import {
  add_page_to_url, get_page_from_url, get_url_with_params
} from "/assets/js/common/url.js"

import column from "./column.js"
import search from "./search.js"

load_asset_css("spinner")

let OPTIONS = {}
let is_rendered = false
let dttable = null
let $table = null

const get_page = perpage => {
  let page = get_page_from_url(3)
  if (!page || isNaN(page)) {
    page = 1
    add_page_to_url(page, 3)
  }
  const pagemin = page - 1
  return pagemin * perpage
}

const on_drawcallback = () => {
  //console.log("on_drawcallback se ejecuta despues de cada renderizado")
  dttable
    .column(0, {search:"applied", order:"applied"})
    .nodes()
    .each(($cell, i) => $cell.innerHTML = i+1)
  load_rowbuttons_listeners()
}

const get_columns = () => {
  const cols = column($table).get_colums()
  //col con numero
  const allcols = [{
    searchable: false,
    orderable: false,
    targets: 0,
    data: null,
  }]

  cols.forEach((colname, i )=> {
    const obj = {
      targets: i+1,
      data: colname,
      //searchable: false, no afecta en nada
      visible: column($table).is_visible(colname),
      render: function (row) {
        return row
      }
    }

    allcols.push(obj)
  })

  allcols.push({
    targets: -1,
    data: null,
    render: function(row) {
      //type: display
      const uuid = row.uuid ?? ""
      if(!uuid) return ""
      const links = [
        `<button type="button" uuid="${uuid}" approle="rowbtn-show">show</button>`,
        `<button type="button" uuid="${uuid}" approle="rowbtn-edit">edit</button>`,
        `<button type="button" uuid="${uuid}" approle="rowbtn-del">del</button>`,
      ]
      return links.join("&nbsp;");
    },
  })

  return allcols
}

const load_rowbuttons_listeners = ()=> {
  let rowbuttons = $table.querySelectorAll(`[approle="rowbtn-show"]`)
  Array.from(rowbuttons).forEach($btn => $btn.addEventListener("click", async (e) => {
    const uuid = e.target.getAttribute("uuid")
    const url = `/restrict/users/info/${uuid}`
    try {
      const r = await fetch(url)
      const html = await r.text()
      //console.log("html",html)
      window.modalraw.disable_bgclick(false).set_body(html).show()
    }
    catch (error) {
      console.log("info listener")
    }
  }))

  rowbuttons = $table.querySelectorAll(`[approle="rowbtn-edit"]`)
  Array.from(rowbuttons).forEach($btn => $btn.addEventListener("click", async (e) => {
    const uuid = e.target.getAttribute("uuid")
    const url = `/restrict/users/edit/${uuid}`
    try {
      const r = await fetch(url)
      const html = await r.text()
      window.modalraw.disable_bgclick(true).set_body(html).show()
    }
    catch (error) {
      console.log("info listener")
    }
  }))

  rowbuttons = $table.querySelectorAll(`[approle="rowbtn-del"]`)
  Array.from(rowbuttons).forEach($btn => $btn.addEventListener("click", (e) => {
    const uuid = e.target.getAttribute("uuid")
    const url = `/restrict/users/delete/${uuid}`

    Swal.fire({
      title: "Are you sure?",
      text: "You will not be able to recover this information! ".concat(uuid),
      type: "warning",
      showCancelButton: true,
      confirmButtonColor: "#DD6B55",
      confirmButtonText: "Yes, I am sure!",
      cancelButtonText: "No, cancel it!",
      closeOnConfirm: false,
      closeOnCancel: false
    })
    .then(result => {
      if (!result.isConfirmed) return
      fetch(url,{method:"delete"})
        .then(response => response.json())
        .then(json => {
          if (json.errors.length>0)
            return Swal.fire({
              icon: "error",
              title: "Some error occured trying to delete",
            })

          Swal.fire({
            icon: "success",
            title: "Data successfully deleted",
          })
          dttable.ajax.reload()

        })
        .catch(error => {
          Swal.fire({
            icon: "error",
            title: "Some error occured trying to delete",
          })
        })
    })//swal.action

  }))
}

const toggle_filters = () => {
  const $row = $table.querySelector(`tr[row="search"]`)
  if ($row) $row.classList.toggle("hidden")
}

const get_buttons = () => [
  {
    text: OPTIONS.BUTTONS.INSERT.LABEL,
    action:  OPTIONS.BUTTONS.INSERT.ACTION,
    className: "button small button-action add",
    attr: {
      "data-tooltip": OPTIONS.BUTTONS.INSERT.TOOLTIP
    }
  },
  {
    text: OPTIONS.BUTTONS.REFRESH.LABEL,
    action: () => dttable.draw(),
    attr: {
      "data-tooltip": OPTIONS.BUTTONS.REFRESH.TOOLTIP
    }
  },
  {
    text: OPTIONS.BUTTONS.FILTER_SHOW.LABEL,
    action: toggle_filters,
    attr: {
      "data-tooltip": OPTIONS.BUTTONS.FILTER_SHOW.TOOLTIP
    }
  },
  {
    text: OPTIONS.BUTTONS.FILTER_RESET.LABEL,
    action: () => search().reset_all($table, dttable),
    attr: {
      "data-tooltip": OPTIONS.BUTTONS.FILTER_RESET.TOOLTIP
    }
  },
]

const get_init_conf = () => (
  {
    // l:length changing input control,
    // f: filtering, t: table, i:information sumary, p:pagination, r:processing (la r es necesria para la personalizacion de language.prcessiong)
    dom: "<'table-buttons'B>lfipr<bottam>p",
    searchDelay: 1500,
    buttons: {
      buttons: get_buttons()
    },
    columnDefs: get_columns(),
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

const get_data = (data, fnrender) => {
  const url = get_url_with_params(OPTIONS.URL_SEARCH, data)

  fetch(url, {
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
  //console.log("OPTIONS:", OPTIONS)
  let idtable = OPTIONS.ID_TABLE
  const tablesel = `#${idtable}`

  $table = document.getElementById(idtable)
  if(!$table) return console.error(`table with id ${idtable} not found`)

  //console.log("dom.$table",$table)
  const dtconfig = {
    ...get_init_conf(),
    ...OPTIONS,
    ...{
      pageLength: OPTIONS.ITEMS_PER_PAGE,
      displayStart: get_page(OPTIONS.ITEMS_PER_PAGE),
    },

    ajax: function(data, fnrender) {
      get_data(data, fnrender)
    },//ajax

    initComplete: function() {
      //esto es único por idtable
      search($table, dttable).add_input_events()
      search().focus_global($table)
      is_rendered = true
      console.log("initComplete end table-ready")
    },

    drawCallback: on_drawcallback
  }

  //console.log("CONFIG", dtconfig)
  dttable = $(tablesel).DataTable(dtconfig)

  dttable
    .on("page.dt", function() {
      const pagemin = dttable.page.info()?.page ?? 0
      add_page_to_url(pagemin+1, 3)
    })
    .on("order.dt", function() {
      if (is_rendered) add_page_to_url(1, 3)
    })
  //console.log("dttable:",dttable)
}//dt_render

export default dt_render