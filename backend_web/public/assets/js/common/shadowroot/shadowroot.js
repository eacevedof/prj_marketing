export const selector = shadow => strsel => shadow.querySelector(`#${strsel}`)

export const  get_formdata = shadow => fields => (arexclude=[]) =>{
  const data = Object.keys(fields)
    .map(field => {
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