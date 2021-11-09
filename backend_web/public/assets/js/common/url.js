

export const getUrlParameter = (name) => {
  name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
  const regex = new RegExp(`[\\?&]${name}=([^&#]*)`);
  const results = regex.exec(location.search);
  return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
}

export const getUrlPosition = pos => {
  let parts = location.pathname.split("?")
  parts = parts[0].split("/")
  return parts[pos] ?? ""
}

export const addPageInUrlByPosition = (pos) => {
  let page = getUrlPosition(pos)
  let url = location.pathname
  if (isNaN(page) || parseInt(page)<1) {
    page = 1
    url = url.concat(`/${page}`)
    window.history.pushState({}, "", url)
  }
}

export const addPageToUrl = (page, pos) => {
  let url = location.pathname
  let parts1 = url.split("?")
  let parts2 = parts1[0].split("/")
  parts2[pos] = page
  const p = [
    parts2.join("/"),
    "?",
    parts1[1] ?? ""
  ]
  url  = p.join("")
  window.history.pushState({}, "", url)
}