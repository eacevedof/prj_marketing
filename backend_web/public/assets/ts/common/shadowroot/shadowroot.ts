type FnShadowSelector = (selector:string) => HTMLElement | null

export const selector = (shadow:ShadowRoot): FnShadowSelector => (strsel: string):HTMLElement|null => shadow.querySelector(`#${strsel}`)

//arexclude evita que se envie esa key
export const get_formdata = (shadow:ShadowRoot) => (fields:string[]| Object) => (arexclude:string[]=[]): string[] => {
  const fieldids: string[] = Array.isArray(fields) ? fields : Object.keys(fields)
  const data = fieldids.map((field:string): Object => {
      if (arexclude.includes(field)) return {}
      const found: HTMLElement | null = selector(shadow)(field)

      const ob:Record<string, string> = {}

      ob.field = (found?.getAttribute("type") === "checkbox") ?
                    (found.getAttribute("checked") ?
                        (found.getAttribute("value") ?? "1") : "0"
                    ) :
                    found?.getAttribute("value") ?? ""
      return ob
    })
    .reduce((old, cur) => ({
      ...old,
      ...cur
    }), {})

  return data
}