import {load_asset_css} from "/assets/js/common/utils.js"
const PATH = "spinner"
load_asset_css([PATH])

let $wrapper = null

const spinner = `    
<div class="spinner" approle="spinner">
    <div class="spinner-loader"></div>
</div>
`

export const remove_spinner = () => {
  const $spinner = $wrapper.querySelector(`[approle="spinner"]`)
  if($spinner) $spinner.parentNode.removeChild($spinner)
}

const add_spinner = () => {
  $wrapper.innerHtml = ""
  $wrapper.innerHtml = spinner
}

const render_spinner = $cont => {
  $wrapper = $cont
  if(!$wrapper) return
  console.log("wrapper:",$wrapper)
  remove_spinner()
  add_spinner()
}

export default render_spinner


