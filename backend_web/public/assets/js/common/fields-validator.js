const REGEX = {
  EMAIL: "",
  NAME: "",
  PHONE: "/^[\\+]?[(]?[0-9]{3}[)]?[-\\s\\.]?[0-9]{3}[-\\s\\.]?[0-9]{4,6}$/im",
  DATE: "",
  ADDRESS: "",
}

const rules = []
const skip = []

export const add_rules = (field, rule, fn) => {
  rules.push({
    field,
    rule,
    fn,
  })
}

export const add_skip = field => {
  skip.push({field})
}
