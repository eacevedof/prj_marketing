const REGEX = {
  EMAIL: "",
  NAME: "",
  PHONE: "",
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
