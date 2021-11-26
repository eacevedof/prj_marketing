import {debounce} from "/assets/js/common/utils.js"

const DEBOUNCE_TIME = 1000
let _inputs = []
let _dttable = null
let _$table = null

const _search = (idx, value) => _dttable.columns(idx).search(value).draw()

const _get_global_search= () => document
  .getElementById(`${_$table.id}_filter`) //div
  .querySelector(`[type="search"]`)

const _get_inputs = _$table => Array.from(_$table.querySelectorAll(`[approle="column-search"]`))

const focus_global = () => _get_global_search().focus()

const _on_input = e => {
  const $input = e.target
  const colidx = $input.getAttribute("appcolidx")
  if (!colidx) return
  const value = $input.value
  _search(colidx, value)
}

const add_input_events = () => _inputs.forEach($input => $input.addEventListener("input", debounce(e => _on_input(e), DEBOUNCE_TIME)))

const reset_all = () => {
  _inputs.forEach( $input => $input.value = "")
  _get_global_search().value = ""
  _dttable.search("").columns().search("").draw()
}

export default ($table, dttable) => {
  _$table = $table
  _dttable = dttable
  _inputs = _get_inputs(_$table)

  return {
    add_input_events,
    focus_global,
    reset_all
  }
}