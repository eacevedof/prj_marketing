import {load_asset_css} from "/assets/js/common/utils.js"
const PATH = "common/spinner"
load_asset_css([PATH])
let $wrapper = null

const spinnertpl = `    
<div class="spinner" approle="spinner">
    <div class="spinner-loader"></div>
</div>
`

const _remove_spinner = () => {
  const $spinner = $wrapper.querySelector(`[approle="spinner"]`)
  if($spinner) $spinner.parentNode.removeChild($spinner)
}

const _add_spinner = () => {
  $wrapper.innerHTML = ""
  $wrapper.innerHTML = spinnertpl
}

const _render_spinner = $cont => {
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


