const _rules = []
const _skip = []

const _form = null
const _fields = []

export const PATTERNS = {
  EMAIL: /^(([^<>()[\]\.,;:\s@\"]+(\.[^<>()[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\.,;:\s@\"]+\.)+[^<>()[\]\.,;:\s@\"]{2,})$/i,
  NAME: /^[a-zA-Z\s-]{3,}$/,
  PHONE: /^[\\+]?[(]?[0-9]{3}[)]?[-\\s\\.]?[0-9]{3}[-\\s\\.]?[0-9]{4,6}$/im,
  DATE: /^\d{2}[./-]\d{2}[./-]\d{4}$/,
  ADDRESS: /^[a-zA-Z0-9\s,. '-]{3,}$/,
}

export default  {

  init: (form, fields) =>{
    _form = form
    _fields = fields
  },

  add_rules: (field, rule, fn) => {
    rules.push({
      field,
      rule,
      fn,
    })
  },

  add_skip: field => {
    _skip.push({field})
  },

  get_errors(input) {
    if(!input) return []

    const errors = []
    _rules.forEach(rule => {
      const field = rule.field
      const msg = rule.fn(input, field, input[field].value, input[field].label)
      if (msg) errors.push({
        field,
        rule:rule.rule,
        label:input[field].label,
        message: msg,
      })
    })

    return errors
  }
}
