let _rules = []
let _skip = []

let _input = {
  wrapper: null,
  fields: null
}

export const PATTERNS = {
  EMAIL: /^(([^<>()[\]\.,;:\s@\"]+(\.[^<>()[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\.,;:\s@\"]+\.)+[^<>()[\]\.,;:\s@\"]{2,})$/i,
  NAME: /^([A-Z,a-z,ñ,á,é,í,ó,ú]+ )+[A-Z,a-z,ñ,á,é,í,ó,ú]+$|^[A-Z,a-z,á,é,í,ó,ú]+$/is,
  PHONE: /^([\d]+ )+[\d]+$|^[\d]+$/is, //777 4444 3355
  DATE: /^\d{2}[./-]\d{2}[./-]\d{4}$/,
  ADDRESS: /^[a-zA-Z0-9\s,. '-]{3,}$/,
  ZERO_ONE: /^[0,1]{1}$/,
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
      const field = rule.field
      const value = _input.wrapper.querySelector(`#${field}`)?.value
      const label = _input.wrapper.querySelector(`label[for=${field}]`)?.innerText

      const msg = rule.fn(input, field, value, label)
      if (msg)
        errors.push({
          field,
          rule: rule.rule,
          label,
          message: msg,
        })
    })
    return errors
  }
}
