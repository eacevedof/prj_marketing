import {debounce} from "/assets/js/common/utils.js"

const debouncetime = 1000
let inputs = []
let jqdttable = null

const search = (idx, value) => jqdttable.columns(idx).search(value).draw()

const on_input = e => {
  const $input = e.target
  const colidx = $input.getAttribute("appcolidx")
  if (!colidx) return
  const value = $input.value
  search(colidx, value)
}

const add_input_events = () => inputs.forEach($input => $input.addEventListener("input", debounce(e => on_input(e), debouncetime)))

const get_global_input= ($table) => document
  .getElementById(`${$table.id}_filter`) //div
  .querySelector(`[type="search"]`)

const focus_global = $table => get_global_input($table).focus()

const reset_all = ($table, dttable) => {
  inputs.forEach( $input => $input.value = "")
  get_global_input($table).value = ""
  dttable.search("").columns().search("").draw()
}

export default ($table, dttable) => {
  if (!jqdttable && dttable) jqdttable = dttable

  if (inputs.length===0 && $table)
    inputs = Array.from($table.querySelectorAll(`[approle="column-search"]`))

  return {
    add_input_events,
    focus_global,
    reset_all,
  }
}