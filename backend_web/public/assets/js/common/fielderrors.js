const TPL_DIV_ERROR = `
    <div approle="field-error" class="%css%">
      <ul>%lis%</ul>
    </div>
  `
let _fieldsid = []
let _$wrapper = null

const CSS = {
  INPUT_ERROR: "form-error",
  ERROR_TOP: "error-top",
}

const _clear = () => {
  if (!_$wrapper) return
  let errors = Array.from(_$wrapper?.querySelectorAll(`[approle="field-error"]`))
  //limpia los li
  errors.forEach($div => $div.remove())
  //limpia las x y los marcos rojos
  errors = Array.from(_$wrapper?.querySelectorAll(`.${CSS.INPUT_ERROR}`))
  errors.forEach($input => $input.classList.remove(CSS.INPUT_ERROR))
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
  //console.log("_append.errors", errors)
  if (!_$wrapper) return
  //si los ids agregados en la config son menos a los que requiere el servidor
  let notinform = _get_not_declared_fields(errors)
  //recupera los mensajes con el fieldid
  let onlymsgs = _get_errors_by_fieldid(errors).filter(obj => obj.messages.length>0)

  const inform = []
  onlymsgs.forEach(objfield => {
    const lis = objfield.messages.map(message => `<li>${message}</li>`).join("")
    const errordivtpl = TPL_DIV_ERROR.replace("%lis%",lis).replace("%css%","")
    const $input = _$wrapper.querySelector(`#${objfield.fieldid}`)
    if ($input) {
      inform.push(objfield.fieldid)
      if ($input?.type==="checkbox") {
        const $label = $input.parentElement
        $label.insertAdjacentHTML("afterend", errordivtpl)
      }
      else {
        $input.insertAdjacentHTML("afterend", errordivtpl)
      }

      $input.classList.add(CSS.INPUT_ERROR)
      return
    }
    notinform.push(objfield.fieldid)
  })

  notinform = [...new Set(notinform)]
  notinform.map(fieldid => {
    const label = errors.filter(objerr => objerr.field === fieldid).map(objerr => objerr.label)[0]
    const messages = errors.filter(objerr => objerr.field === fieldid).map(objerr => objerr.message)
    const lis = messages.map(message => `<li>${message}</li>`).join("")

    const html = TPL_DIV_ERROR
      .replace("%lis%",`<li class="li-label">${!label ? fieldid : label}</li>${lis}`)
      .replace("%css%",` ${CSS.ERROR_TOP}`)
    _$wrapper.insertAdjacentHTML("afterbegin", html)
  })

  //console.log("error.inform",inform)
  if (inform[0]) {
    const firstinput = _$wrapper.querySelector(`#${inform[0]}`)
    //console.log("first input",firstinput)
    firstinput?.focus()
    if(firstinput.tagName!=="SELECT") firstinput?.select()
  }
}

const _append_top = errors => {
  const lis = errors.map(message => `<li>${message}</li>`).join("")
  const html = TPL_DIV_ERROR
      .replace("%lis%",`${lis}`)
      .replace("%css%",` ${CSS.ERROR_TOP}`)
  _$wrapper.insertAdjacentHTML("afterbegin", html)
}

const _set_config = options => {
  _fieldsid = options.fields
  _$wrapper = options.wrapper
}

const error = {
  config: _set_config,
  append: _append,
  clear: _clear,
  append_top: _append_top,
}

export default error