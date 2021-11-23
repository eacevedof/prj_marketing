import {debounce} from "./utils.js"
import {load_asset_css} from "/assets/js/common/utils.js"
import {
  add_page_to_url, get_page_from_url, get_url_with_params
} from "./url.js"

load_asset_css("spinner")

let OPTIONS = {}
let is_rendered = false
let dttable = null
let $table = null
let $search = null

const get_page = perpage => {
  let page = get_page_from_url(3)
  if (!page || isNaN(page)) {
    page = 1
    add_page_to_url(page, 3)
  }
  const pagemin = page - 1
  return pagemin * perpage
}

const add_filter_events = () => {
  if (!dttable) return
  const debouncetime = 1000

  const on_event = e => {
    const $input = e.target
    const colidx = $input.getAttribute("appcolidx")
    if (!colidx) return
    const value = $input.value
    //sin draw no busca
    dttable.columns(colidx).search(value).draw()
  }

  const inputs = $table.querySelectorAll(`[approle="column-search"]`)
  inputs.forEach(
    $input => $input.addEventListener("input", debounce(e => on_event(e), debouncetime))
  )
}

const on_drawcallback = () => {
  //console.log("on_drawcallback se ejecuta despues de cada renderizado")
  dttable
    .column(0, {search:"applied", order:"applied"})
    .nodes()
    .each(($cell, i) => $cell.innerHTML = i+1)
  load_rowbuttons_listeners()
}

const reset_filters = () => {
  const inputs = Array.from($table.querySelectorAll(`[approle="column-search"]`))
  inputs.push($search)
  inputs.forEach( $input => $input.value = "")
  dttable.search("").columns().search("").draw()
}

const get_columns = () => {
  const cols = [
    "id:int",
    "uuid:string",
    "fullname:string",
    "email:string",
    "phone:string",
    "id_profile:string",
    "id_nationality:string",
    "id_language:string",
  ]

  //columna con numeros
  const final = [{
    searchable: false,
    orderable: false,
    targets: 0,
    data: null,
  }]

  cols.forEach((colconfig, i )=> {
    const [colname, type] = colconfig.split(":")
    //console.log("colanme",colname, "type:", type)
    const obj = {
      targets: i+1,
      data: colname,
      //searchable: false, no afecta en nada
      visible: colname!=="id" ? true: false,
      //visible: true,
      render: function (data, type, row) {
        return data
      }
    }

    final.push(obj)
  })

  final.push({
    targets: -1,
    data: null,
    render: function(row, type) {
      //type: display
      //row: objeto
      //console.log("row:",row,"type:",type)
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

  return final
}

const load_rowbuttons_listeners = ()=> {
  let rowbuttons = $table.querySelectorAll(`[approle="rowbtn-show"]`)
  //console.log("rowbuttons", rowbuttons)
  Array.from(rowbuttons).forEach($btn => $btn.addEventListener("click", async (e) => {
    //console.log("btn",$btn)
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
      text: "You will not be able to recover this information!",
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
    action: reset_filters,
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
  const $body = $table.querySelector(`[approle="tbody"]`)
  //render_spinner($body)

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
      recordsTotal: response.data.recordsTotal,
      recordsFiltered: response.data.recordsFiltered,
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

    ajax: function(data, fnrender, settings) {
      //console.log("ajax start")
      get_data(data, fnrender)
      //console.log("ajax end")
    },//ajax

    initComplete: function() {
      //console.log("initComplete start")
      add_filter_events()
      is_rendered = true
      //esto es único por idtable
      $search = document
        .getElementById(`${idtable}_filter`)
        .querySelector(`[type="search"]`)
      //load_rowbuttons_listeners() no me vale aqui pq solo se carga para la prim pagina

      //const $buttonsdiv = document.getElementById("table-datatable_wrapper")?.querySelector(".dt-buttons")
      //const $outsidediv = document.getElementById("div-table-datatable")?.querySelector(`[approle="table-buttons"]`)
      //$outsidediv.innerHTML = $buttonsdiv.innerHTML
      //$buttonsdiv.parentNode.removeChild($buttonsdiv)
      $search.focus()
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