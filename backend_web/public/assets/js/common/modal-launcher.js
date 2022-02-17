import {reqtxt} from "/assets/js/common/req.js"
import spinner from "/assets/js/common/spinner.js"
import {SNACK} from "/assets/js/common/snackbar.js"

const _open_modal = async (url) => {
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

export default () => {
  const url = window.location.search;
  if (!url) return
  const urlparams = new URLSearchParams(url)

  const uuid = urlparams.get("uuid")
  if (!uuid) return

  const view = urlparams.get("view") ?? "info"
  const tab = urlparams.get("tab") ?? "main"

  const pathname = window.location.pathname
  const final = pathname.concat(`/${view}`).concat(`/${uuid}`)
  //http://localhost:900/restrict/users/info/620d471857bc
  //http://localhost:900/restrict/users/edit/620d471857bc4
  alert(final)

}