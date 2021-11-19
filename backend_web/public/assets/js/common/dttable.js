//alert("hola")
import {debounce} from "./utils.js"
import {
  add_page_to_url, get_page_from_url, get_url_with_params
} from "./url.js"

let OPTIONS = {}
let is_rendered = false
let $dttable = null
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
  if (!$dttable) return
  const debouncetime = 1000

  const on_event = e => {
    const $input = e.target
    const colidx = $input.getAttribute("appcolidx")
    if (!colidx) return
    const value = $input.value
    //sin draw no busca
    $dttable.columns(colidx).search(value).draw()
  }

  const inputs = $table.querySelectorAll(`[approle="column-search"]`)
  inputs.forEach(
    $input => $input.addEventListener("input", debounce(e => on_event(e), debouncetime))
  )
}

const add_col_idx = () => {
  $dttable
    .column(0, {search:"applied", order:"applied"})
    .nodes()
    .each(($cell, i) => $cell.innerHTML = i+1)
}

const reset_filters = () => {
  const inputs = Array.from($table.querySelectorAll(`[approle="column-search"]`))
  inputs.push($search)
  inputs.forEach( $input => $input.value = "")
  $dttable.search("").columns().search("").draw()
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

  const final = [{
    searchable: false,
    orderable: false,
    targets: 0,
    data: null,
  }]

  cols.forEach((colconfig, i )=> {
    const [colname, type] = colconfig.split(":")
    console.log("colanme",colname, "type:", type)
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
    render: function(data, type, row) {
      const links = [
        `<span>show</span>`,
        `<span>edit</span>`,
        `<span>del</span>`,
      ];

      return links.join("&nbsp;");
    },
  })
  console.log(final)
  return final
}

const toggle_filters = () => {
  const $row = $table.querySelector(`tr[row="search"]`)
  if ($row) $row.classList.toggle("hidden")
}

const get_buttons = () => [
  {
    text: OPTIONS.BUTTONS.INSERT.LABEL,
    action:  () => window.location.href = OPTIONS.URL_ADD,
    className: "button small button-action add",
    attr: {
      "data-tooltip": OPTIONS.BUTTONS.INSERT.TOOLTIP
    }
  },
  {
    text: OPTIONS.BUTTONS.REFRESH.LABEL,
    action: () => $dttable.draw(),
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
    dom: "Blftipr",
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
    //searchDelay: 1500,
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
    console.log("response:", response)
    fnrender({
      recordsTotal: response.data.recordsTotal,
      recordsFiltered: response.data.recordsFiltered,
      data: response.data.result,
    })
  })
  .catch(error => {
    console.log("get_data.error",error)
  })
  .finally(()=>{

  })
}

const dt_render = (options) => {
  OPTIONS = {...options}
  console.log("OPTIONS:", OPTIONS)
  let idtable = OPTIONS.ID_TABLE
  const tablesel = `#${idtable}`

  $table = document.getElementById(idtable)
  if(!$table) return console.error(`table with id ${idtable} not found`)

  console.log("dom.$table",$table)
  const dtconfig = {
    ...get_init_conf(),
    ...OPTIONS,
    ...{
      pageLength: OPTIONS.ITEMS_PER_PAGE,
      displayStart: get_page(OPTIONS.ITEMS_PER_PAGE),
    },

    ajax: function(data, fnrender, settings) {
      console.log("ajax start")
      get_data(data, fnrender)
      console.log("ajax end")
    },//ajax

    initComplete: function() {
      console.log("initComplete start")
      add_filter_events()
      is_rendered = true
      $search = document
        .getElementById(`${idtable}_filter`)
        .querySelector(`[type="search"]`)
      console.log("initComplete end")
    },
    drawCallback: add_col_idx
  }

  console.log("CONFIG", dtconfig)
  $dttable = $(tablesel).DataTable(dtconfig)

  $dttable
    .on("page.dt", function() {
      const pagemin = $dttable.page.info()?.page ?? 0
      add_page_to_url(pagemin+1, 3)
    })
    .on("order.dt", function() {
      if (is_rendered) add_page_to_url(1, 3)
    })
  console.log("$dttable:",$dttable)
}//dt_render

export default dt_render