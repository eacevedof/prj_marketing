
export const get_parameter = key => {
  key = key.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
  const regex = new RegExp(`[\\?&]${key}=([^&#]*)`);
  const results = regex.exec(location.search);
  return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
}

export const get_url_position = pos => {
  let parts = location.pathname.split("?")
  parts = parts[0].split("/")
  return parts[pos] ?? ""
}

export const add_page_to_url = (page, position) => {
  let url = location.pathname
  const parts1 = url.split("?")
  const parts2 = parts1[0].split("/")

  if (parts2[position]) {
    parts2[position] = page
  }
  else {
    parts2.push(page)
  }

  url = parts2.join("/")
  if (parts1[1])  url = url.concat("?").concat(parts1[1])
  window.history.pushState({}, "", url)
}

function get_querystring(obj, prefix) {
  return Object.keys(obj ?? {}).map(objKey => {
    if (obj.hasOwnProperty(objKey)) {
      const key = prefix ? `${prefix}[${objKey}]` : objKey;
      const value = obj[objKey];

      return typeof value === "object" ?
        get_querystring(value, key) :
        `${encodeURIComponent(key)}=${encodeURIComponent(value)}`;
    }

    return null;
  }).join("&");
}

export const get_url_with_params = (url, params) => {
  const qs = get_querystring(params)
  console.log("QS",qs)
  const parts = [
    url,
    "?",
    qs
  ]
  return parts.join("")
}

export const get_page_from_url = position => {
  const page = get_url_position(position)
  if(!page) return null
  if (isNaN(page)) return null
  return parseInt(page)
}