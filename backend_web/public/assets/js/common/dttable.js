//alert("hola")
import {debounce} from "./utils.js"
import {
  add_page_to_url, get_page_from_url
} from "./url.js"

let is_rendered = false
let $dttable = null
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

const add_filter_events = $dttable => {
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
  inputs.forEach($input => $input.addEventListener("input", debounce(e => on_event(e), debouncetime)))
}

const reset_filters = () => {
  const inputs = Array.from(document.querySelectorAll(`[approle="column-search"]`))
  const $input = $table.querySelector(`[type="search"]`)
  inputs.push($input)
  inputs.forEach( $input => $input.value = "")
  $dttable.search("").columns().search("").draw()
}

const dt_render = (options) => {
  let tableid = options.table_id
  const tablesel = `#${tableid}`
  $table = document.getElementById(tableid)
  $dttable = $(tablesel).DataTable({...options})

  $dttable.on("page.dt", function() {
    const pagemin = $dttable.page.info()?.page ?? 0
    add_page_to_url(pagemin+1, 3)
  })
  .on("order.dt", function() {
    if (is_rendered) add_page_to_url(1, 3)
  })
}

export default dt_render