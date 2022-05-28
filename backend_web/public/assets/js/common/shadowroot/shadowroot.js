export const selector = shadow => strsel => shadow.querySelector(`#${strsel}`)

//arexclude evita que se envie esa key
export const  get_formdata = shadow => fields => (arexclude=[]) =>{
  const fieldids = Array.isArray(fields) ? fields : Object.keys(fields)
  const data = fieldids.map(field => {
      const ob = {}
      if (arexclude.includes(field)) return {}
      const found = selector(shadow)(field)
      console.log("FOUND", found)
      if (found?.type === "checkbox") {
          ob[field] = found?.checked ? (found?.value ??  "1") : "0"
      }
      else
        ob[field] = found?.value ?? ""

      console.log("ob", ob)
      return ob
    })
    .reduce((old, cur) => ({
      ...old,
      ...cur
    }), {})

  return data
}