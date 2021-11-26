import search from "./search.js"

let _$table = null,
    _dttable = null,
    rowbuttons = [],
    topbuttons = []

const _toggle_filters = () => {
  const $row = _$table.querySelector(`tr[row="search"]`)
  if ($row) $row.classList.toggle("hidden")
}

const rowbuttons_listeners = ()=> {
  let rowbuttons = _$table.querySelectorAll(`[approle="rowbtn-show"]`)
  Array.from(rowbuttons).forEach($btn => $btn.addEventListener("click", async (e) => {
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

  rowbuttons = _$table.querySelectorAll(`[approle="rowbtn-edit"]`)
  Array.from(rowbuttons).forEach($btn => $btn.addEventListener("click", async (e) => {
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

  rowbuttons = _$table.querySelectorAll(`[approle="rowbtn-del"]`)
  Array.from(rowbuttons).forEach($btn => $btn.addEventListener("click", (e) => {
    const uuid = e.target.getAttribute("uuid")
    const url = `/restrict/users/delete/${uuid}`

    Swal.fire({
      title: "Are you sure?",
      text: "You will not be able to recover this information! ".concat(uuid),
      type: "warning",
      showCancelButton: true,
      confirmButtonColor: "#DD6B55",
      confirmButtonText: "Yes, I am sure!",
      cancelButtonText: "No, cancel it!",
      closeOnConfirm: false,
      closeOnCancel: false
    })
    .then(result => {
      if (!result.isConfirmed) return
      fetch(url,{method:"delete"})
        .then(response => response.json())
        .then(json => {
          if (json.errors.length>0)
            return Swal.fire({
              icon: "error",
              title: "Some error occured trying to delete",
            })

          Swal.fire({
            icon: "success",
            title: "Data successfully deleted",
          })
          _dttable.ajax.reload()

        })
        .catch(error => {
          Swal.fire({
            icon: "error",
            title: "Some error occured trying to delete",
          })
        })
    })//end then

  }))//end foreach

}//rowbuttons listeners

const get_buttons = () => {

  let defbtns = [
    {
      approle: "add-item",
      text: "Add",
      className: "",
      visible: true,
      action: () => console.log("add clicked"),
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
                    const btn = topbuttons.filter(top => top.approle === def.approle)[0] ?? null
                    if (!btn) return def

                    return {
                      ...def,
                      ...btn
                    }
                  })
                  .filter(btn => btn.visible)
  return defbtns
}

const set_topbuttons = buttons => topbuttons = buttons

const set_rowbuttons = buttons => rowbuttons = buttons

export default ($table, dttable) => {
  _$table = $table
  _dttable = dttable

  return {
    rowbuttons_listeners,
    get_buttons,
    set_topbuttons,
    set_rowbuttons,
  }
}