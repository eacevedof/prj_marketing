import {run_js} from "/assets/js/common/utils.js"

const CSS = {
  HIDE: "mod-hide",
}

export default function ModalRaw(opts={}) {

  let modal = {
    id_modal: "",
    $modal: null,
    $dialog: null,
    $btnclose: null,
    $body: null,
    bgclick: true,
    id_opener: "",
    $opener: null,
  }

  modal = {...modal, ...opts}

  const $modal = document.getElementById(modal.id_modal)
  if(!$modal) return console.log("no modal found!",modal.id_modal)

  modal.$modal = $modal
  modal.$dialog = modal.$modal.querySelector("[role='modal-dialog']")
  modal.$btnclose = modal.$dialog.querySelector("[role='btn-close']")
  modal.$body = modal.$dialog.querySelector("[role='body']")
  modal.$opener = modal.id_opener ? document.getElementById(modal.id_opener) : null
  let $docbody = document.querySelector("body")

  const _show = () => {
    $modal.classList.remove(CSS.HIDE)
    $docbody.style.overflow = "hidden";
  }

  const _hide = ev => {
    if(ev?.target?.id === modal.id_modal && !modal.bgclick) return
    $modal.classList.add(CSS.HIDE)
    $docbody.style.overflow = "auto"
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
    modal.$body.innerHTML = ""
    _hide()
    if(fnAfter) fnAfter()
    return this
  }

  this.body = function (html) {
    if(!html || !modal.$body) return this
    modal.$body.innerHTML = ""
    const $eltmp = document.createElement("div")
    $eltmp.innerHTML = html

    modal.$body.innerHTML = html
    run_js($eltmp)
    return this
  }

  this.destroy = () => {
    if($docbody) $docbody = null
    if(modal.$modal) modal.$modal.removeEventListener("click", _hide)
    if(modal.$opener) modal.$opener.removeEventListener("click", _show)
    if(modal.$btnclose) modal.$btnclose.removeEventListener("click", _hide)
    if(modal.$body) modal.$body.innerHTML = ""
    return null
  }

  this.no_bgclick = (on=true) => {
    modal.bgclick = !on
    return this
  }

  (() => {
    //configuro los listeners
    modal.$modal.addEventListener("click", _hide)
    if (modal.$dialog) modal.$dialog.addEventListener("click", ev => ev.stopPropagation())
    if (modal.$opener) modal.$opener.addEventListener("click", _show)
    if (modal.$btnclose) modal.$btnclose.addEventListener("click", _hide)
  })()

}//ModalRaw