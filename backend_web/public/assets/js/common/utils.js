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
  //const $head = document.getElementsByTagName("head")[0]
  const $body = document.body
  const $script = document.createElement("script")
  $script.src = pathjs
  $script.type = type
  $script.setAttribute("approle","jsmodal")
  //$head.appendChild($script)
  $body.appendChild($script)
}

export const async_import = async (src) => {
  const { default: defaultFunc } = await import(src)
  defaultFunc()
}

export const get_as_element = html => {
  const d=document
  let i
      ,a=d.createElement("div")
      ,b=d.createDocumentFragment();

  a.innerHTML= html;
  while (i=a.firstChild) b.appendChild(i);
  return b;
}

export const run_js = $jswrapper => {
  const scripts = $jswrapper.querySelectorAll("script")
  if (!scripts) return

  const doc = document;
  const atrribs = ["type","src","nonce","noModule"]

  scripts.forEach($script => {
    const $docscript = doc.createElement( "script" );
    $docscript.text =  $script.textContent
    atrribs.forEach(attr => {
      const val = $script.getAttribute(attr)
      if(val) $docscript.setAttribute(attr, val)
    })
    doc.head.appendChild($docscript).parentNode.removeChild($docscript)
  })
}

export const load_asset_css = paths => {
  if (!paths) return

  if (typeof paths === "string" || paths instanceof String) {
    const $link = document.createElement("link")
    $link.href = `/assets/css/${paths}.css`
    $link.rel = "stylesheet"
    return document.head.appendChild($link)//.parentNode.removeChild($link)
  }

  paths.forEach(path => {
    const $link = document.createElement( "link" )
    $link.href = `/assets/css/${path}.css`
    $link.rel = "stylesheet"
    document.head.appendChild($link)//.parentNode.removeChild($link)
  })
}