import {reqtxt} from "/assets/js/common/req.js"
import spinner from "/assets/js/common/spinner.js"
import {SNACK} from "/assets/js/common/snackbar.js"

import search from "./dtsearch.js"
import rowswal from "./rowswal.js"

let _$table = null,
    _dttable = null,
    _urlmodule = "",
    _topbtns = []

const _toggle_filters = () => {
  const $row = _$table.querySelector(`tr[row="search"]`)
  if ($row) $row.classList.toggle("hidden")
}

const _export_data = () => {

}

const _on_show = async function(btn){
  spinner.render()
  const uuid = btn.getAttribute("uuid")
  const URL_INFO = _urlmodule.concat(`/info/${uuid}`)
  const r = await reqtxt.get(URL_INFO)
  spinner.remove()
  if (r.errors)
    return window.snack.set_color(SNACK.ERROR).set_time(5).set_inner(r.errors[0]).show()
  window.modalraw.opts({
    bgclick: false,
    body: r,
  }).show()
}

const _on_edit = async function(btn){
  spinner.render()
  const uuid = btn.getAttribute("uuid")
  const URL_EDIT = _urlmodule.concat(`/edit/${uuid}`)
  const r = await reqtxt.get(URL_EDIT)
  spinner.remove()
  if (r.errors)
    return window.snack.set_color(SNACK.ERROR).set_time(5).set_inner(r.errors[0]).show()
  window.modalraw.opts({
    bgclick: false,
    body: r,
  }).show()
}

const _on_del = function (btn) {
  const uuid = btn.getAttribute("uuid")
  rowswal(_$table, _dttable).on_delete(uuid)
}

const _on_undel = function (btn) {
  const uuid = btn.getAttribute("uuid")
  rowswal(_$table, _dttable).on_undelete(uuid)
}

const on_wrapbtns_click = (e) => {
  const $any = e.target
  let $btn = null
  if ($any.tagName.toLowerCase()==="i")
    $btn = $any.parentNode

  if ($any.tagName.toLowerCase()==="button")
    $btn = $any

  if (!$btn) return

  const btnid = $btn.getAttribute("btnid")
  switch (btnid) {
    case "rowbtn-show": _on_show($btn); break;
    case "rowbtn-edit": _on_edit($btn); break;
    case "rowbtn-del": _on_del($btn); break;
    case "rowbtn-undel": _on_undel($btn); break;
  }
}

const rowbuttons_listeners = $row => {
  const wrapbtns = $row
                  ? $row.querySelectorAll(`[approle="btns"]`)
                  : _$table.querySelectorAll(`[approle="btns"]`)
  wrapbtns.forEach($div => $div.addEventListener("click", on_wrapbtns_click))
}

const get_topbuttons = () => {

  let topbtns = [
    {
      //add-button
      approle: "add-item",
      text: `<i class="mdi mdi-plus-box"></i>`,
      className: "btn btn-success btn-icon me-2",
      visible: true,
      action: () => _in_modal(_$table.getAttribute("urlmodule").concat("/create")),
      attr: {
        approle: "add-item"
      }
    },
    {
      approle: "refresh-grid",
      text: `<i class="mdi mdi-refresh"></i>`,
      className: "btn btn-warning  btn-icon me-2",
      visible: true,
      action: () => _dttable.draw(),
      attr: {
        approle: "refresh-grid"
      }
    },
    {
      approle: "show-filters",
      text: `<i class="mdi mdi-filter-variant"></i>`,
      className: "btn btn-info btn-icon me-2 btn-b",
      visible: true,
      action: _toggle_filters,
      attr: {
        approle: "show-filters"
      }
    },
    {
      approle: "reset-filters",
      text: `<i class="mdi mdi-auto-fix"></i>`,
      className: "btn btn-dark btn-icon me-2",
      visible: true,
      action: () => search(_$table, _dttable).reset_all(),
      attr: {
        approle: "reset-filters"
      }
    },
    {
      approle: "export-data",
      text: `<i class="mdi mdi-arrow-collapse-down"></i>`,
      className: "btn btn-dark btn-icon me-2",
      visible: true,
      action: _export_data,
      attr: {
        approle: "export-data"
      }
    },
  ]

  const is_addbtn = _$table.querySelector(`[approle='actions']`)?.getAttribute("add")==="1";
  if (!is_addbtn) topbtns = topbtns.filter(btn => btn.approle !== "add-item")
  topbtns = topbtns.map(def => {
                    const btn = _topbtns.filter(top => top.approle === def.approle)[0] ?? null
                    if (!btn) return def
                    return {
                      ...def,
                      ...btn
                    }
                  })
                  .filter(btn => btn.visible)
  return topbtns
}

export const dtbutton = {
  add_topbtn: button => _topbtns.push(button),
  set_topbtns: buttons => _topbtns = buttons
}

//para el boton add
const _in_modal = async url => {
  spinner.render()
  const r = await reqtxt.get(url)
  spinner.remove()
  if (r.errors)
    return window.snack.set_color(SNACK.ERROR).set_time(5).set_inner(r.errors[0]).show()
  window.modalraw.opts({
    bgclick: false,
    body: r,
  }).show()
}// add. _in_modal

export default ($table, dttable) => {
  _$table = $table
  _dttable = dttable
  _urlmodule = _$table.getAttribute("urlmodule") ?? ""

  return {
    rowbuttons_listeners,
    get_topbuttons
  }
}