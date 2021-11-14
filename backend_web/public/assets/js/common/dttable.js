//alert("hola")
import {debounce} from "./utils.js"
import {
  add_page_to_url, get_page_from_url
} from "./url.js"

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

const reset_filters = () => {
  const inputs = Array.from($table.querySelectorAll(`[approle="column-search"]`))
  inputs.push($search)
  inputs.forEach( $input => $input.value = "")
  $dttable.search("").columns().search("").draw()
}

const get_columns = () => {
  const cols = [
    "uuid:string",
    "fullname:string",
    "email:string",
    "phone:string",
    "id_profile:string",
    "id_nationality:string",
    "id_language:string",
  ]

  const final = []
  cols.forEach((colconfig, i )=> {
    const [colname, type] = colconfig.split(":")
    console.log("colanme",colname, "type:", type)
    const obj = {
      targets: i,
      data: colname,
      //searchable: false, no afecta en nada
      visible: true,
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

const get_buttons = () => [
  {
    text: "Add",
    action: function (e, dt, node, config) {
        window.location.href = "#";
      },
      className: "button small button-action add",
      attr: {
        "data-tooltip": "add"
      }
  },
  {
    text: "Clear search",
    action: reset_filters,
    attr: {
      "data-tooltip": "clear"
    }
  },
  {
    text: "refresh",
    action: () => $dttable.draw(),
    attr: {
      "data-tooltip": "refresh"
    }
  },
  {
    text: "show filters",
    action: function (e, dt, node, config) {
      const $row = $table.querySelector(`tr[row="search"]`)
      if ($row) {
        //$row.classList.contains("hidden") ? $row.classList.remove("hidden"): $row.classList.add("hidden")
        $row.classList.toggle("hidden")
      }
    },
    attr: {
      "data-tooltip":"show_filters"
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
    processing: true,
    lengthMenu: [25, 50, 75, 100],
    serverSide: true,
    orderCellsTop: true,
    fixedHeader: true,
    //language: get_language(),
    //searchDelay: 1500,
  }
)

const dt_render = (options) => {
  console.log("options:", options)
  let idtable = options.id_table
  const tablesel = `#${idtable}`

  $table = document.getElementById(idtable)
  if(!$table) return console.error(`table with id ${idtable} not found`)

  console.log("dom.$table",$table)
  const dtconfig = {
    ...get_init_conf(),
    ...options,
    ...{
      pageLength: options.ITEMS_PER_PAGE,
      displayStart: get_page(options.ITEMS_PER_PAGE),
    },

    ajax: function(data, fnRender, settings) {
      console.log("ajax start")
      window.$.get(options.GET_URL, data, function(res) {
        console.log("response start")
        fnRender({
          recordsTotal: res.data.recordsTotal,
          recordsFiltered: res.data.recordsFiltered,
          data: res.data.result,
        })
        console.log("response end")
      })//get
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
  }

  console.log("CONFIG", dtconfig)
  $dttable = $(tablesel).DataTable(dtconfig)

  $dttable.on("page.dt", function() {
    const pagemin = $dttable.page.info()?.page ?? 0
    add_page_to_url(pagemin+1, 3)
  })
  .on("order.dt", function() {
    if (is_rendered) add_page_to_url(1, 3)
  })
  console.log("$dttable:",$dttable)
}//dt_render

export default dt_render