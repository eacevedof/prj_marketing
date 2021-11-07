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

export const onDocumentready = callbackFunction =>{
  if(document.readyState != 'loading')
    callbackFunction(event)
  else
    document.addEventListener("DOMContentLoaded", callbackFunction)
}

export const debounce = (func, timeout = 300) => {
  let timer;
  return (...args) => {
    clearTimeout(timer);
    timer = setTimeout(() => { func.apply(this, args); }, timeout);
  };
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
    "/".join(parts2),
    "?",
    parts1[1] ?? ""
  ]
  url  = "".join(p)
  window.history.pushState({}, "", url)
}