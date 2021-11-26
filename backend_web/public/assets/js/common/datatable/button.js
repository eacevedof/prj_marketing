import search from "./search.js"

let _$table = null,
    _dttable = null

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

const get_buttons = (OPTIONS) => [
  {
    text: OPTIONS.BUTTONS.INSERT.LABEL,
    action:  OPTIONS.BUTTONS.INSERT.ACTION,
    className: "button small button-action add",
    attr: {
      "data-tooltip": OPTIONS.BUTTONS.INSERT.TOOLTIP
    }
  },
  {
    text: OPTIONS.BUTTONS.REFRESH.LABEL,
    action: () => _dttable.draw(),
    attr: {
      "data-tooltip": OPTIONS.BUTTONS.REFRESH.TOOLTIP
    }
  },
  {
    text: OPTIONS.BUTTONS.FILTER_SHOW.LABEL,
    action: _toggle_filters,
    attr: {
      "data-tooltip": OPTIONS.BUTTONS.FILTER_SHOW.TOOLTIP
    }
  },
  {
    text: OPTIONS.BUTTONS.FILTER_RESET.LABEL,
    action: () => search(_$table, _dttable).reset_all(),
    attr: {
      "data-tooltip": OPTIONS.BUTTONS.FILTER_RESET.TOOLTIP
    }
  },
]

export default ($table, dttable) => {
  _$table = $table
  _dttable = dttable

  return {
    rowbuttons_listeners,
    get_buttons
  }
}