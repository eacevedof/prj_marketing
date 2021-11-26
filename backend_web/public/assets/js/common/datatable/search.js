import {debounce} from "/assets/js/common/utils.js"

const debouncetime = 1000
let inputs = []
let jqdttable = null
let $eltable = null

const _search = (idx, value) => jqdttable.columns(idx).search(value).draw()

const _get_global_search= () => document
  .getElementById(`${$eltable.id}_filter`) //div
  .querySelector(`[type="search"]`)

const _get_inputs = $eltable => Array.from($eltable.querySelectorAll(`[approle="column-search"]`))

const focus_global = () => _get_global_search().focus()

const on_input = e => {
  const $input = e.target
  const colidx = $input.getAttribute("appcolidx")
  if (!colidx) return
  const value = $input.value
  _search(colidx, value)
}

const add_input_events = () => inputs.forEach($input => $input.addEventListener("input", debounce(e => on_input(e), debouncetime)))

const reset_all = () => {
  inputs.forEach( $input => $input.value = "")
  _get_global_search().value = ""
  jqdttable.search("").columns().search("").draw()
}

export default ($table, dttable) => {
  console.log("TABLE",$table)
  $eltable = $table
  jqdttable = dttable
  inputs = _get_inputs($eltable)
  console.log("inputs",inputs)

  return {
    add_input_events,
    focus_global,
    reset_all
  }
}