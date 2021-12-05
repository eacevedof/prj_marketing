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
  let notinform = _get_not_declared_fields(errors)
  //recupera los mensajes con el fieldid
  let onlymsgs = _get_errors_by_fieldid(errors).filter(obj => obj.messages.length>0)

  const inform = []
  onlymsgs.forEach(objfield => {
    const lis = objfield.messages.map(message => `<li>${message}</li>`).join("")
    const html = tpl.replace("%lis%",lis).replace("%css%","")
    const $input = _$wrapper.querySelector(`#${objfield.fieldid}`)
    if ($input) {
      inform.push(objfield.fieldid)
      $input.insertAdjacentHTML("afterend", html)
      $input.classList.add("form-error")
      return
    }
    notinform.push(objfield.fieldid)
  })

  notinform = [...new Set(notinform)]
  notinform.map(fieldid => {
    const label = errors.filter(objerr => objerr.field === fieldid).map(objerr => objerr.label)[0]
    const messages = errors.filter(objerr => objerr.field === fieldid).map(objerr => objerr.message)
    const lis = messages.map(message => `<li>${message}</li>`).join("")

    const html = tpl
      .replace("%lis%",`<li class="li-label">${!label?fieldid:label}</li>${lis}`)
      .replace("%css%"," error-top")
    _$wrapper.insertAdjacentHTML("afterbegin", html)
  })

  if (inform[0])
    _$wrapper.querySelector(`#${inform[0].id}`).focus()
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