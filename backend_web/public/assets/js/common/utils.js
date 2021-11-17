export const onDocumentready = callbackFunction =>{
  if(document.readyState != "loading")
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

export const include_js = (pathjs, type="text/javascript") => {
  const $head = document.getElementsByTagName("head")[0]
  const $script = document.createElement("script")
  $script.src = pathjs
  $script.type = type
  $script.setAttribute("approle","jsmodal")
  $head.appendChild($script)
}

export const async_import = async (src) => {
  const { default: defaultFunc } = await import(src)
  defaultFunc()
}
