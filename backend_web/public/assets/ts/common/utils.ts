interface IOnEvent {
  (event: Event|undefined) : void
}

export const onDocumentready = (fnOnEvent: IOnEvent) =>{
  if(document.readyState != "loading")
    fnOnEvent(event)
  else
    document.addEventListener("DOMContentLoaded", fnOnEvent)
}

interface IDebounce {
  (args: any[]) : void | any
}

export const debounce = (func: IDebounce, timeout = 300) => {
  let timer: number;
  return (...args: any[]) => {
    clearTimeout(timer)
    // @ts-ignore
    timer = setTimeout(() => func.apply(this, args), timeout)
  }
}

export const include_js = (pathjs: string, type: string="text/javascript") => {
  //const $head = document.getElementsByTagName("head")[0]
  const $body: HTMLElement = document.body
  const $script: HTMLScriptElement = document.createElement("script")
  $script.src = pathjs
  $script.type = type
  $script.setAttribute("approle","jsmodal")
  //$head.appendChild($script)
  $body.appendChild($script)
}

export const async_import = async (src: string) => {
  const { default: defaultFunc } = await import(src)
  defaultFunc()
}

export const get_as_element = (html: string) => {
  const d=document
  let i
      ,a=d.createElement("div")
      ,b=d.createDocumentFragment();

  a.innerHTML= html;
  while (i=a.firstChild) b.appendChild(i);
  return b;
}



export const run_js = ($jswrapper: HTMLElement) => {
  const scripts: HTMLElement[] = Array.from($jswrapper.querySelectorAll("script"))
  if (!scripts) return

  const $document: HTMLDocument = window.document
  const atrribs: string[] = ["type","src","nonce","noModule"]

  scripts.forEach(($script: HTMLElement) => {
    const $docscript: HTMLScriptElement = $document.createElement( "script" );
    $docscript.setAttribute("text", $script.getAttribute("textContent") ?? "")
    atrribs.forEach((attr: string) => {
      const val = $script.getAttribute(attr)
      if(val) $docscript.setAttribute(attr, val)
    })
    $document?.head?.appendChild($docscript)?.parentNode?.removeChild($docscript)
  })
}

const _append_css = (href: string) => {
  const $link: HTMLLinkElement = document.createElement("link")
  $link.type = "text/css"
  $link.rel = "stylesheet"
  $link.href = href
  $link.media = "all"
  window.document?.head?.appendChild($link)?.parentNode?.removeChild($link)
}

export const load_css = ($wrapper:HTMLElement) => {
  const links: HTMLLinkElement[] = Array.from($wrapper.querySelectorAll("link"))
  //console.log("load_css.links",links,"type",typeof links)
  if (!links) return

  const $document: HTMLDocument = window.document;
  const atrribs: string[] = ["type","rel","href","media"]

  links.forEach(($link: HTMLLinkElement ) => {
    //console.log("load_css.link", $link)
    const $doclink: HTMLLinkElement = $document.createElement("link")
    atrribs.forEach( (attr:string) => {
      const val = $link.getAttribute(attr)
      if(val) $doclink.setAttribute(attr, val)
    })
    $document?.head?.appendChild($doclink)?.parentNode?.removeChild($doclink)
  })
}

export const load_asset_css = (paths: string | string[]) => {
  if (!paths) return
  let links: HTMLLinkElement[] = Array.from(document.head.querySelectorAll("link"))
  const hrefs: string[] = links.map(link => link.href)
  //console.log("links",links)
  if (typeof paths === "string" || paths instanceof String) {
    const url = `/assets/css/${paths}.css`
    if (hrefs.filter(href => href.includes(url)).length>0)
      return

    const $link = document.createElement("link")
    $link.href = url
    $link.rel = "stylesheet"
    //si lo quito deja de funcionar
    //con el remove el spinner no se ve
    document.head.appendChild($link)//.parentNode.removeChild($link)
    return
  }

  paths.forEach(path => {
    const url = `/assets/css/${path}.css`
    if (links.filter(href => href.includes(url)).length>0)
      return

    const $link = document.createElement( "link" )
    $link.href = url
    $link.rel = "stylesheet"
    //con el remove el spinner no se ve
    document.head.appendChild($link)//.parentNode.removeChild($link)
  })
}