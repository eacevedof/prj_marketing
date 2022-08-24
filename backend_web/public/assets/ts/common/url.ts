
export const get_parameter = (key: string): string => {
  key = key.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
  const regex: RegExp = new RegExp(`[\\?&]${key}=([^&#]*)`);
  const results: string[] | null = regex.exec(window.location.search);
  return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
}

export const get_url_position = (pos: number): string => {
  let parts: string[] = window.location.pathname.split("?")
  parts = parts[0].split("/")
  return parts[pos] ?? ""
}

export const add_page_to_url = (page:string, position:number): void => {
  let url: string = window.location.pathname
  const urlparts: string[] = url.split("/")

  if (urlparts[position]) {
    urlparts[position] = page
  }
  else {
    urlparts.push(page)
  }

  url = urlparts.join("/")
  const search: string = window.location.search
  if (search) url = url.concat(search)
  window.history.pushState({}, "", url)
}

function get_querystring(obj: object|null, prefix:string|null): string {
  return Object.keys(obj ?? {}).map((objKey: string): string|null => {
    if (obj?.hasOwnProperty(objKey)) {
      const key: string = prefix ? `${prefix}[${objKey}]` : objKey;
      // @ts-ignore
      const value: string = obj[objKey]

      return typeof value === "object" ?
            get_querystring(value, key) :
            `${key}=${encodeURIComponent(value)}`
    }
    return null
  }).filter(obj => obj).join("&");
}

export const get_url_with_params = (url: string, params:object): string => {
  const qs: string = get_querystring(params, null)
  //console.log("QS",qs)
  const parts: string[] = [url, "?", qs]
  return parts.join("")
}

export const get_page_from_url = (position: number):number|null => {
  const page: string = get_url_position(position)
  if(!page) return null
  if (isNaN(Number(page))) return null
  return parseInt(page)
}