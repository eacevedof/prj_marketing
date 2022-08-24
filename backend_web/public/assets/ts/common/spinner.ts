// @ts-ignore
import {load_asset_css} from "/assets/js/common/utils.js"
const PATH:string = "common/spinner"
load_asset_css([PATH])
let $wrapper:HTMLElement|null = null

const spinnertpl: string = `
<div class="spinner" approle="spinner">
    <div class="spinner-loader"></div>
</div>
`

const _remove_spinner = (): void => {
  const $spinner: HTMLElement|null|undefined = $wrapper?.querySelector(`[approle="spinner"]`)
  if($spinner) $spinner?.parentNode?.removeChild($spinner)
}

const _add_spinner = (): void => {
  if (!$wrapper) return
  $wrapper.innerHTML = ""
  $wrapper.innerHTML = spinnertpl
}

const _render_spinner = ($cont: HTMLElement|null) => {
  if(!$cont) $cont = document.getElementById("spinner-global")
  if(!$cont) return
  $wrapper = $cont
  _remove_spinner()
  _add_spinner()
}

export default {
  render: _render_spinner,
  remove: _remove_spinner,
}


