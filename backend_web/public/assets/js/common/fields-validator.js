const rules = []
const skip = []

const PATTERNS = {
  EMAIL: "",
  NAME: "",
  PHONE: "/^[\\+]?[(]?[0-9]{3}[)]?[-\\s\\.]?[0-9]{3}[-\\s\\.]?[0-9]{4,6}$/im",
  DATE: "",
  ADDRESS: "",
}

export default  {


  add_rules: (field, rule, fn) => {
    rules.push({
      field,
      rule,
      fn,
    })
  },

  add_skip: field => {
    skip.push({field})
  },

  get_errors() {

  }
}
