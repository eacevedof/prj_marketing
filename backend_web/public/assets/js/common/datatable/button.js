import search from "./search.js"
import swal from "./swal.js"

let _$table = null,
    _dttable = null,
    _topbtns = [],
    _rowbtns = []

const _toggle_filters = () => {
  const $row = _$table.querySelector(`tr[row="search"]`)
  if ($row) $row.classList.toggle("hidden")
}

const rowbuttons_listeners = ()=> {
  let _rowbtns = _$table.querySelectorAll(`[approle="rowbtn-show"]`)
  Array.from(_rowbtns).forEach($btn => $btn.addEventListener("click", async (e) => {
    const uuid = e.target.getAttribute("uuid")
    const url = `/restrict/users/info/${uuid}`
    try {
      const r = await fetch(url)
      const html = await r.text()
      window.modalraw.disable_bgclick(false).set_body(html).show()
    }
    catch (error) {
      console.log("info listener")
    }
  }))//end foreach

  _rowbtns = _$table.querySelectorAll(`[approle="rowbtn-edit"]`)
  Array.from(_rowbtns).forEach($btn => $btn.addEventListener("click", async (e) => {
    const uuid = e.target.getAttribute("uuid")
    const url = `/restrict/users/edit/${uuid}`
    try {
      const r = await fetch(url)
      const html = await r.text()
      window.modalraw.disable_bgclick(true).set_body(html).show()
    }
    catch (error) {
      console.error(error)
    }
  }))//end foreach

  _rowbtns = _$table.querySelectorAll(`[approle="rowbtn-del"]`)
  Array.from(_rowbtns).forEach($btn => $btn.addEventListener("click", (e) => {
    const uuid = e.target.getAttribute("uuid")
    swal(_$table, _dttable).on_delete(uuid)
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

  defbtns = defbtns
                  .map(def => {
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

const set_topbuttons = buttons => _topbtns = buttons
const add_topbutton = button => _topbtns.push(button)

const set_rowbuttons = buttons => _rowbtns = buttons
const add_rowbutton = button => _rowbtns.push(button)

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
    get_buttons,
    set_topbuttons,
    add_topbutton,
    set_rowbuttons,
    add_rowbutton,
    //in_modal, permite pasar una url custom a pintar en un modal
  }
}