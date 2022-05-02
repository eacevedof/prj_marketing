export const selector = shadow => strsel => shadow.querySelector(`#${strsel}`)

//arexclude evita que se envie esa key
export const  get_formdata = shadow => fields => (arexclude=[]) =>{
  const fieldids = Array.isArray(fields) ? fields : Object.keys(fields)
  const data = fieldids.map(field => {
      const ob = {}
      if (arexclude.includes(field)) return {}
      ob[field] = selector(shadow)(field)?.value ?? ""
      return ob
    })
    .reduce((old, cur) => ({
      ...old,
      ...cur
    }), {})

  return data
}