import {run_js, load_css} from "/assets/js/common/utils.js"

export default function ModalRaw(idModal, idOpener=null) {

  let bgclick = true
  const $modal = document.getElementById(idModal)
  if(!$modal) return console.log("no modal found!",idModal)

  const $dialog = $modal.querySelector("[role='modal-dialog']")
  const $title = $dialog.querySelector("[role='title']")
  const $btnClose = $dialog.querySelector("[role='btn-close']")
  const $body = $dialog.querySelector("[role='body']")
  const $opener = idOpener ? document.getElementById(idOpener) : null
  const $mainbody = document.querySelector("body")

  const _show = () => {
    $modal.classList.remove("modal-hide")
    $modal.classList.add("modal-show")
    $mainbody.style.overflow = "hidden";
  }

  const _hide = ev => {
    if(ev?.target?.id === idModal && !bgclick) return
    $modal.classList.add("modal-hide")
    $mainbody.style.overflow = "auto";
  }

  this.show = function (fnBefore, fnAfter) {
    if (fnBefore) {
      const abort = fnBefore()
      if (abort) return this
    }

    _show()
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
    _hide()
    if(fnAfter) fnAfter()
    return this
  }

  this.set_body = function (html) {
    if(!html || !$body) return this
    $body.innerHTML = ""
    const $eltmp = document.createElement("div")
    $eltmp.innerHTML = html
    $body.innerHTML = html
    run_js($eltmp)
    load_css($eltmp)
    return this
  }

  this.set_title = function (html) {
    if(!html || !$title) return this
    $title.innerHTML = ""
    $title.innerHTML = html
    return this
  }

  this.destroy = () => {
    if($modal) $modal.removeEventListener("click", _hide)
    if($opener) $opener.removeEventListener("click", _show)
    if($btnClose) $btnClose.removeEventListener("click", _hide)
    if($title) $title.innerHTML = ""
    if($body) $body.innerHTML = ""
    return null
  }

  this.disable_bgclick = (on=true) => {
    bgclick = !on
    return this
  }

  (() => {
    //configuro los listeners
    $modal.addEventListener("click", _hide)
    if ($dialog) $dialog.addEventListener("click", ev => ev.stopPropagation())
    if ($opener) $opener.addEventListener("click", _show)
    if ($btnClose) $btnClose.addEventListener("click", _hide)
  })()

}//ModalRaw