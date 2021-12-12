import injson from "/assets/js/common/req.js"
const Rowswal = window.Swal

let _dttable = null,
  _$table = null

let _texts = {
  delswal: {
    title: "Delete operation",
    text: "You will not be able to recover this information! <b>",
    confirm: "Yes, I am sure!",
    cancel: "No, cancel it!",
    error: `<b>Error on remove</b>`,
    success: "Data successfully deleted",
  },
  undelswal: {
    title: "Restore operation",
    text: "",
    confirm: "Yes, I am sure!",
    cancel: "No, cancel it!",
    error: `<b>Error on restore</b>`,
    success: "Data successfully restored",
  }
}

const TYPE = {
  DELETE: "delete",
  UNDELETE: "restore"
}

const _show_error_handled = (type=TYPE.DELETE) => type===TYPE.DELETE ? Rowswal.fire({
  icon: "error",
  title: _texts.delswal.error,
}) : Rowswal.fire({
  icon: "error",
  title: _texts.undelswal.error,
})

const _show_error_catched = (error,type=TYPE.DELETE) => type===TYPE.DELETE ? Rowswal.fire({
    icon: "error",
    title: _texts.delswal.error.concat(`<br/>${error}`),
  })
  :
  Rowswal.fire({
    icon: "error",
    title: _texts.undelswal.error.concat(`<br/>${error}`),
  })

const _show_success = (uuid, type=TYPE.DELETE) => type===TYPE.DELETE ? window.snack
  .set_time(5)
  .set_color("green")
  .set_inner(_texts.delswal.success.concat(` ${uuid}`))
  .show()
  : window.snack
    .set_time(5)
    .set_color("green")
    .set_inner(_texts.undelswal.success.concat(` ${uuid}`))
    .show()

/*
const _show_success = () => Rowswal.fire({
  icon: "success",
  title: _texts.success.title,
})
*/
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
  .then(async result => {
    if (!result.isConfirmed) return

    const URL_DELETE = _$table.getAttribute("urlmodule").concat(`/delete/${uuid}`)
    const response = await injson.patch(
      URL_DELETE, {
        _action: "row.delete",
      })
    if(response?.errors) return _show_error_catched(response.errors[0])
    _show_success(uuid)
    _dttable.ajax.reload()
  })//end then

const on_undelete = uuid =>
  Rowswal.fire({
    title: _texts.undelswal.title,
    html: _texts.undelswal.text.concat(uuid).concat("</b>"),
    confirmButtonText: _texts.undelswal.confirm,
    cancelButtonText: _texts.undelswal.cancel,
    type: "warning",
    showCancelButton: true,
    confirmButtonColor: "#DD6B55",
    closeOnConfirm: false,
    closeOnCancel: false
  })
  .then(result => {
    if (!result.isConfirmed) return
    const url = _$table.getAttribute("urlmodule").concat(`/undelete/${uuid}`)
    fetch(url,{
      method: "PATCH",
      headers: {
        "Accept": "application/json",
        "Content-Type": "application/json",
      }
    })
    .then(response => response.json())
    .then(json => {
      if (json.errors.length>0)
        return _show_error_handled(TYPE.UNDELETE)
      _show_success(uuid, TYPE.UNDELETE)
      _dttable.ajax.reload()
    })
    .catch(error => _show_error_catched(error, TYPE.UNDELETE))
  })//end then

export const rowswal = {
  set_texts: obj => {
    const delswal =  {
      ..._texts.delswal,
      ...obj.delswal ?? {}
    }
    const undelswal = {
      ..._texts.undelswal,
      ...obj.undelswal ?? {}
    }

    _texts = {
      delswal,
      undelswal,
    }
    //console.log("_texts",_texts)
  }
}

export default ($table, dttable) => {
  _$table = $table
  _dttable = dttable

  return {
    on_delete,
    on_undelete
  }
}