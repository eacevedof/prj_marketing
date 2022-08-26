// @ts-ignore
import {reqtxt, is_get_200} from "/assets/js/common/req.js"
// @ts-ignore
import spinner from "/assets/js/common/spinner.js"
// @ts-ignore
import {SNACK} from "/assets/js/common/snackbar.js"

declare global {
  interface Window {
    snack: any,
    modalraw: any
  }
}

const _open_modal_by_get = async (url: string): Promise<void> => {
  spinner.render()
  const r = await reqtxt.get(url)
  spinner.remove()
  if (r.errors)
    return window.snack.set_color(SNACK.ERROR).set_time(5).set_inner(r.errors[0]).show()

  window.modalraw.opts({
    bgclick: false,
    body: r,
  }).show()
}

const VIEWS = ["info", "edit"]

//modal_launcher()
export default ():void => {
  const url: string = window.location.search
  if (!url) return
  const urlparams:URLSearchParams = new URLSearchParams(url)

  const uuid: string|null = urlparams.get("uuid")
  if (!uuid) return

  const view: string = urlparams.get("view")?.trim() ?? "info"
  if (!VIEWS.includes(view)) return

  const tab: string = urlparams.get("tab")?.trim() ?? "main"
  const pathname: string = window.location.pathname.split("/").slice(0,-1).join("/")
  let final: string = pathname.concat(`/${view}`).concat(`/${uuid}`).concat(`?tab=${tab}`)
  //http://localhost:900/restrict/users/info/620d471857bc
  //http://localhost:900/restrict/users/edit/620d471857bc4
  //alert(final)
  //final = "/restrict/users/info/620d471857bc4".concat(url)
  _open_modal_by_get(final)
}

export const show_restrict_url = async (): Promise<void> => {
  const is_valid_url = async (url: string): Promise<boolean> => {
    if (url.match(/\/restrict[\/]*$/)) return false
    const is200:boolean = await is_get_200(url)
    if (url.startsWith("/restrict/") && is200)
      return true
    return false
  }

  const url:string = window.location.search
  if (!url) return
  const urlparams:URLSearchParams = new URLSearchParams(url)
  const inmodal:string = urlparams.get("in-modal")?.trim() ?? ""
  if (!inmodal) return

  const isvalid:boolean = await is_valid_url(inmodal)
  if (!isvalid) {
    if (window?.snack)
      window.snack
      .set_color(SNACK.ERROR)
      .set_time(5)
      .set_inner("wrong in-modal param")
      .show()
    return
  }
  _open_modal_by_get(inmodal)
}


/*
* en una vista con pestañas habilitar una pestaña concreta definia en la url
*/
export const show_tab = () => {
  const url:string = window.location.search
  if (!url) return
  const urlparams:URLSearchParams = new URLSearchParams(url)
  const tab:string = urlparams.get("tab") ?? ""
  if (!tab || tab==="main") return

  const $tab: HTMLAnchorElement | null = document.querySelector(`a[href="#${tab}"]`)
  if (!$tab) return
  $tab.click()
}