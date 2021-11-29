const tpl = `
    <div approle="field-error" class="%css%">
      <ul>%lis%</ul>
    </div>
  `
let fieldsid = []
let $wrapper = null

export const clear_errors = () => {
  if (!$wrapper) return
  const errors = Array.from($wrapper?.querySelectorAll(`[approle="field-error"]`))
  errors.forEach($div => $div.parentNode.removeChild($div))
}

export const field_errors = (errors) => {
  if (!$wrapper) return

  const nonfieldsid = errors
                        .filter(objerr => !fieldsid.includes(objerr.field))
                        .map(objerr => objerr.field)

  let fielderrors = []
  fieldsid.forEach(id => {
    fielderrors.push({
      id,
      messages : errors.filter(objerr => objerr.field === id).map(objerr => objerr.message)
    })
  })

  fielderrors = fielderrors.filter(obj => obj.messages.length>0)
  fielderrors.forEach(objfield => {
    const lis = objfield.messages.map(message => `<li>${message}</li>`).join("")
    const html = tpl.replace("%lis%",lis).replace("%css%","")
    const $input = $wrapper.querySelector(`#${objfield.id}`)
    if ($input) {
      $input.insertAdjacentHTML("afterend", html)
      $input.classList.add("form-error")
    }
  })

  const nonerrors = []
  nonfieldsid.forEach(id => {
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
    $wrapper.querySelector("form")?.insertAdjacentHTML("afterbegin", html)
  })

  if (fielderrors[0]) $wrapper.querySelector(`#${fielderrors[0].id}`).focus()
}

const set_config = options => {
  fieldsid = options.fields
  $wrapper = options.wrapper
}

export default set_config