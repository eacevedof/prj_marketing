const Swal = window.Swal

let _dttable = null
let _$table = null

const _texts = {
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

const _show_error_handled = () => Swal.fire({
  icon: "error",
  title: _texts.error.title,
})

const _show_error_catched = error => Swal.fire({
  icon: "error",
  title: _texts.error.title.concat(`<br/>${error}`),
})

const _show_success = () => Swal.fire({
  icon: "success",
  title: _texts.success.title,
})

const on_delete = uuid =>
  Swal.fire({
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
    const url = _$table.getAttribute("urlmodule").concat("/delete/").concat(uuid)
    fetch(url,{method:"delete"})
      .then(response => response.json())
      .then(json => {
        if (json.errors.length>0)
          return _show_error_handled()
        _show_success()
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