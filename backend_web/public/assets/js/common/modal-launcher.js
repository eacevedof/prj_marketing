import {reqtxt} from "/assets/js/common/req.js"
import spinner from "/assets/js/common/spinner.js"
import {SNACK} from "/assets/js/common/snackbar.js"

const _open_modal_by_get = async (url) => {
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

export default () => {
  const url = window.location.search;
  if (!url) return
  const urlparams = new URLSearchParams(url)

  const uuid = urlparams.get("uuid")
  if (!uuid) return

  const view = urlparams.get("view")?.trim() ?? "info"
  if (!VIEWS.includes(view)) return

  const tab = urlparams.get("tab")?.trim() ?? "main"
  const pathname = window.location.pathname.split("/").slice(0,-1).join("/")
  let final = pathname.concat(`/${view}`).concat(`/${uuid}`).concat(`?tab=${tab}`)
  //http://localhost:900/restrict/users/info/620d471857bc
  //http://localhost:900/restrict/users/edit/620d471857bc4
  //alert(final)
  //final = "/restrict/users/info/620d471857bc4".concat(url)
  _open_modal_by_get(final)
}

export const show_tab = () => {
  const url = window.location.search;
  if (!url) return
  const urlparams = new URLSearchParams(url)
  const tab = urlparams.get("tab")
  if (!tab) return

  const $tab = document.querySelector(`a[href="#${tab}"]`)
  console.log($tab, "TAB modal-launcher")
  if (!$tab) return;
  $tab.click()
}