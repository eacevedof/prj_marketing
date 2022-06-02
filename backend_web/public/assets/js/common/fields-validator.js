let _rules = []
let _skip = []

let _input = {
  wrapper: null,
  fields: null
}

export const PATTERNS = {
  EMAIL: /^(([^<>()[\]\.,;:\s@\"]+(\.[^<>()[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\.,;:\s@\"]+\.)+[^<>()[\]\.,;:\s@\"]{2,})$/i,
  NAME: /^([A-Z,a-zÑñáéíóú]+ )+[A-Z,a-zÑñáéíóú]+$|^[A-Z,a-záéíóú]+$/is,
  PHONE: /^(\d{3} )+\d+$|^\d{3,}$/is, //777 4444 3355
  DATE: /^\d{4}[./-]\d{2}[./-]\d{2}$/,
  ADDRESS: /^[a-zA-ZÑñáéíóú]+[a-zA-ZÑñáéíóú0-9\s,\.'\-]{3,}[a-zA-Z0-9\.]$/,
  ZERO_ONE: /^[0,1]{1}$/,
  GENDER: /^[1,2,3]{1}$/,
  INTEGER: /^[\d]{1,}$/
}

export default  {

  init: obj =>{
    _input = obj
  },

  reset: () => {
    _rules = []
    _skip = []
  },

  add_rules: (field, rule, fn) => {
    _rules.push({
      field,
      rule,
      fn,
    })
  },

  add_skip: field => {
    _skip.push({field})
  },

  get_errors() {
    const input = _input.fields.map(field => {
      const f = `${field}`
      const obj = {}
      obj[f] = _input.wrapper.querySelector(`#${field}`)?.value
      return obj
    })

    const errors = []
    _rules.forEach(rule => {
      const fieldid = rule.field
      const value = _input.wrapper.querySelector(`#${fieldid}`)?.value
      const label = _input.wrapper.querySelector(`label[for=${fieldid}]`)?.innerText

      const msg = rule.fn(value, fieldid, input, label)
      if (msg)
        errors.push({
          fieldid,
          rule: rule.rule,
          label,
          message: msg,
        })
    })
    return errors
  }
}
