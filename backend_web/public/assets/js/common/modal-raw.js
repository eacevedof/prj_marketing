import {async_import, include_js, add_to_dom} from "./utils.js"

export default function ModalRaw(idModal, idOpener=null) {

  const $modal = document.getElementById(idModal)
  if(!$modal) return console.log("no modal found!",idModal)

  const $dialog = $modal.querySelector(":scope > [role='modal-dialog']")
  const $title = $dialog.querySelector(":scope > header > [role='title']")
  const $btnClose = $dialog.querySelector(":scope > header > [role='btn-close']")
  const $body = $dialog.querySelector(":scope > [role='body']")
  const $opener = idOpener ? document.getElementById(idOpener) : null

  const show = () => {
    $modal.classList.remove("modal-hide")
    $modal.classList.add("modal-show")
  }

  const hide = () => $modal.classList.add("modal-hide")

  const run_js = () => {
    const scripts = $body.getElementsByTagName("script")
    //include_js(src,type)
    //import(src).then(module => module.default()).catch(err => console.log(err))
    //async_import(src)
    Array.from(scripts).forEach($script => {
      console.log("script",$script)
      const src = $script.attributes.src.value
      const type = $script.attributes.type.value
      include_js(src, type)
    })
  }

  const remove_js = () => {
    const $mainbody = document.body
    const scripts = $mainbody.querySelectorAll(`script[approle="jsmodal"]`)
    Array.from(scripts).forEach($script => $script.parentElement.removeChild($script))
  }

  this.show = function (fnBefore, fnAfter) {
    if (fnBefore) {
      const abort = fnBefore()
      if (abort) return this
    }

    show()
    //run_js()
    if(fnAfter) fnAfter()
    return this
  }

  this.hide = function (fnBefore, fnAfter) {
    if (fnBefore) {
      const abort = fnBefore()
      if (abort) return this
    }
    $title.innerHTML = ""
    $body.innerHTML = ""
    //remove_js()
    hide()
    if(fnAfter) fnAfter()
    return this
  }

  this.set_body = function (html) {
    if(!html || !$body) return this
    //$body.innerHTML = ""
    //cuando se ejecuta vue la primera vez borra el form
    console.log("setting body html", $body)
    $body.innerHTML = ""
    //return $body.innerHTML = html

    //const domnode = new DOMParser().parseFromString(html, "text/html")
    //const domnode = get_as_element(html)

    const $eltmp = document.createElement("div")
    $eltmp.innerHTML = html
    $body.innerHTML = html
    add_to_dom($eltmp)

    //$eltmp.childNodes.forEach(node => $body.appendChild(node))
    //$body.appendChild($eltmp)
    //$($body).append(html)
    //$body.insertAdjacentHTML("afterbegin", html)
    //console.log("after append",$body)

    return this
  }

  this.set_title = function (html) {
    if(!html || !$title) return this
    $title.innerHTML = html
    return this
  }

  this.destroy = () => {
    if($modal) $modal.removeEventListener("click", hide)
    if($opener) $opener.removeEventListener("click", show)
    if($btnClose) $btnClose.removeEventListener("click", hide)
    if($title) $title.innerHTML = ""
    if($body) $body.innerHTML = ""
    return null
  }

  (() => {
    //configuro los listeners
    $modal.addEventListener("click", hide)
    if ($dialog) $dialog.addEventListener("click", e => e.stopPropagation())
    if ($opener) $opener.addEventListener("click", show)
    if ($btnClose) $btnClose.addEventListener("click", hide)
  })()

}//ModalRaw