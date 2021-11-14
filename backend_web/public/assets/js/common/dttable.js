//alert("hola")
import {debounce} from "./utils.js"
import {
  add_page_to_url, get_page_from_url
} from "./url.js"

let is_rendered = false
let $dttable = null

const dt_render = (options) => {
  let tableid = options.table_id
  const tablesel = `#${tableid}`
  const $table = document.getElementById(tableid)

  $dttable.on("page.dt", function() {
    const pagemin = $dttable.page.info()?.page ?? 0
    add_page_to_url(pagemin+1, 3)
  })
  .on("order.dt", function() {
    if (is_rendered) add_page_to_url(1, 3)
  })
}

export default dt_render