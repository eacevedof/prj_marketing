import {reqtxt} from "/assets/js/common/req.js"
import search from "./search.js"
import rowswal from "./rowswal.js"

let _$table = null,
    _dttable = null,
    _topbtns = []

const _toggle_filters = () => {
  const $row = _$table.querySelector(`tr[row="search"]`)
  if ($row) $row.classList.toggle("hidden")
}

const rowbuttons_listeners = ()=> {
  const urlmodule = _$table.getAttribute("urlmodule")
  let _rowbtns = _$table.querySelectorAll(`[btnid="rowbtn-show"]`)
  Array.from(_rowbtns).forEach($btn => $btn.addEventListener("click", async (e) => {
    const uuid = e.target.getAttribute("uuid")
    const URL_INFO = urlmodule.concat(`/info/${uuid}`)
    const r = await reqtxt.get(URL_INFO)
    if (r.errors)
      return window.snack.set_color("red").set_time(5).set_inner(r.errors[0]).show()
    window.modalraw.disable_bgclick(false).set_body(r).show()
  }))//end foreach

  _rowbtns = _$table.querySelectorAll(`[btnid="rowbtn-edit"]`)
  Array.from(_rowbtns).forEach($btn => $btn.addEventListener("click", async (e) => {
    const uuid = e.target.getAttribute("uuid")
    const URL_EDIT = urlmodule.concat(`/edit/${uuid}`)
    const r = await reqtxt.get(URL_EDIT)
    if (r.errors)
      return window.snack.set_color("red").set_time(5).set_inner(r.errors[0]).show()
    window.modalraw.disable_bgclick(false).set_body(r).show()
  }))//end foreach

  _rowbtns = _$table.querySelectorAll(`[btnid="rowbtn-del"]`)
  Array.from(_rowbtns).forEach($btn => $btn.addEventListener("click", (e) => {
    const uuid = e.target.getAttribute("uuid")
    rowswal(_$table, _dttable).on_delete(uuid)
  }))//end foreach

  _rowbtns = _$table.querySelectorAll(`[btnid="rowbtn-undel"]`)
  Array.from(_rowbtns).forEach($btn => $btn.addEventListener("click", (e) => {
    const uuid = e.target.getAttribute("uuid")
    rowswal(_$table, _dttable).on_undelete(uuid)
  }))//end foreach

}//_rowbtns listeners

const get_buttons = () => {

  let defbtns = [
    {
      approle: "add-item",
      text: "Add",
      className: "",
      visible: true,
      action: () => in_modal(_$table.getAttribute("urlmodule").concat("/create")),
      attr: {
        approle: "add-item"
      }
    },
    {
      approle: "refresh-grid",
      text: "Refresh",
      className: "",
      visible: true,
      action: () => _dttable.draw(),
      attr: {
        approle: "refresh-grid"
      }
    },
    {
      approle: "show-filters",
      text: "Show filters",
      className: "",
      visible: true,
      action: _toggle_filters,
      attr: {
        approle: "show-filters"
      }
    },
    {
      approle: "reset-filters",
      text: "Reset filters",
      className: "",
      visible: true,
      action: () => search(_$table, _dttable).reset_all(),
      attr: {
        approle: "reset-filters"
      }
    },
  ]

  const is_addbtn = _$table.querySelector(`[approle='actions']`)?.getAttribute("add")==="1";
  if (!is_addbtn) defbtns = defbtns.filter(btn => btn.approle !== "add-item")
  defbtns = defbtns.map(def => {
                    const btn = _topbtns.filter(top => top.approle === def.approle)[0] ?? null
                    if (!btn) return def

                    return {
                      ...def,
                      ...btn
                    }
                  })
                  .filter(btn => btn.visible)
  return defbtns
}

export const button = {
  add_topbtn: button => _topbtns.push(button),
  set_topbtns: buttons => _topbtns = buttons
}

const in_modal = url => fetch(url)
  .then(response => response.text())
  .then(html => {
    window.modalraw.disable_bgclick().set_body(html).show()
  })
  .catch(error => {
    console.log("in_modal",error)
  })
  .finally(()=>{

  })

export default ($table, dttable) => {
  _$table = $table
  _dttable = dttable

  return {
    rowbuttons_listeners,
    get_buttons
    //in_modal, permite pasar una url custom a pintar en un modal
  }
}