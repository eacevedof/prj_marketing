const Rowswal = window.Swal

let _dttable = null,
  _$table = null

let _texts = {
  error:{
    title: "Some error occured trying to delete"
  },
  success: {
    title: "Data successfully deleted",
  },
  delswal: {
    title: "Are you sure?",
    text: "You will not be able to recover this information! <b>",
    confirm: "Yes, I am sure!",
    cancel: "No, cancel it!",
  }
}

export const rowswal = {
  set_texts: obj => {
    _texts = {
      ..._texts,
      ...obj
    }
  }
}

const _show_error_handled = () => Rowswal.fire({
  icon: "error",
  title: _texts.error.title,
})

const _show_error_catched = error => Rowswal.fire({
  icon: "error",
  title: _texts.error.title.concat(`<br/>${error}`),
})

/*
const _show_success = () => Rowswal.fire({
  icon: "success",
  title: _texts.success.title,
})
*/
const _show_success = uuid => window.snack
                              .set_time(5)
                              .set_color("green")
                              .set_inner(_texts.success.title.concat(` ${uuid}`))
                              .show()

const on_delete = uuid =>
  Rowswal.fire({
    title: _texts.delswal.title,
    html: _texts.delswal.text.concat(uuid).concat("</b>"),
    confirmButtonText: _texts.delswal.confirm,
    cancelButtonText: _texts.delswal.cancel,
    type: "warning",
    showCancelButton: true,
    confirmButtonColor: "#DD6B55",
    closeOnConfirm: false,
    closeOnCancel: false
  })
  .then(result => {
    if (!result.isConfirmed) return
    //const url = `/restrict/users/delete/${uuid}`
    const url = _$table.getAttribute("urlmodule").concat(`/delete/${uuid}`)
    fetch(url,{method:"delete"})
      .then(response => response.json())
      .then(json => {
        if (json.errors.length>0)
          return _show_error_handled()
        _show_success(uuid)
        _dttable.ajax.reload()
      })
      .catch(error => _show_error_catched(error))
  })//end then

export default ($table, dttable) => {
  _$table = $table
  _dttable = dttable

  return {
    on_delete
  }
}