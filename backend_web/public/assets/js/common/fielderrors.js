const tpl = `
    <div approle="field-error" class="%css%">
      <ul>%lis%</ul>
    </div>
  `
let _fieldsid = []
let _$wrapper = null

const _clear = () => {
  if (!_$wrapper) return
  const errors = Array.from(_$wrapper?.querySelectorAll(`[approle="field-error"]`))
  errors.forEach($div => $div.parentNode.removeChild($div))
}

const _get_not_declared_fields = errors => errors
                                            .filter(objerr => !_fieldsid.includes(objerr.field))
                                            .map(objerr => objerr.field)

const _get_errors_by_fieldid = errors => _fieldsid.map(fieldid => (
  {
    fieldid,
    messages: errors.filter(objerr => objerr.field === fieldid).map(objerr => objerr.message)
  }
))

const _append = errors => {
  console.log("_append.errors", errors)
  if (!_$wrapper) return
  //si los ids agregados en la config son menos a los que requiere el servidor
  const idsnotinform = _get_not_declared_fields(errors)
  //recupera los mensajes con el fieldid
  let fielderrors = _get_errors_by_fieldid(errors).filter(obj => obj.messages.length>0)

  const existinform = []
  const notinform = []

  fielderrors.forEach(objfield => {
    const lis = objfield.messages.map(message => `<li>${message}</li>`).join("")
    const html = tpl.replace("%lis%",lis).replace("%css%","")
    const $input = _$wrapper.querySelector(`#${objfield.fieldid}`)
    if ($input) {
      existinform.push(objfield.fieldid)
      $input.insertAdjacentHTML("afterend", html)
      $input.classList.add("form-error")
      return
    }
    notinform.push(objfield.fieldid)
  })

  const nonerrors = []
  idsnotinform.forEach(id => {
    nonerrors.push({
      id,
      label: errors.filter(objerr => objerr.field === id).map(objerr => objerr.label).join(""),
      messages : errors.filter(objerr => objerr.field === id).map(objerr => objerr.message)
    })
  })

  nonerrors.forEach(objfield => {
    const lis = objfield.messages.map(message => `<li>${message}</li>`).join("")
    const html = tpl
      .replace("%lis%",`<li class="li-label">${objfield.label}</li>${lis}`)
      .replace("%css%"," error-top")
    _$wrapper.insertAdjacentHTML("afterbegin", html)
  })

  //console.log("nonerrors:", nonerrors)
  if (existinform[0])
    _$wrapper.querySelector(`#${existinform[0].id}`).focus()

}

const _set_config = options => {
  _fieldsid = options.fields
  _$wrapper = options.wrapper
}

const error = {
  config: _set_config,
  append: _append,
  clear: _clear
}

export default error