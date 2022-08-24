// @ts-ignore
import { load_asset_css } from "/assets/js/common/utils.js";
const PATH = "common/spinner";
load_asset_css([PATH]);
let $wrapper = null;
const spinnertpl = `
<div class="spinner" approle="spinner">
    <div class="spinner-loader"></div>
</div>
`;
const _remove_spinner = () => {
    var _a;
    const $spinner = $wrapper === null || $wrapper === void 0 ? void 0 : $wrapper.querySelector(`[approle="spinner"]`);
    if ($spinner)
        (_a = $spinner === null || $spinner === void 0 ? void 0 : $spinner.parentNode) === null || _a === void 0 ? void 0 : _a.removeChild($spinner);
};
const _add_spinner = () => {
    if (!$wrapper)
        return;
    $wrapper.innerHTML = "";
    $wrapper.innerHTML = spinnertpl;
};
const _render_spinner = ($cont) => {
    if (!$cont)
        $cont = document.getElementById("spinner-global");
    if (!$cont)
        return;
    $wrapper = $cont;
    _remove_spinner();
    _add_spinner();
};
export default {
    render: _render_spinner,
    remove: _remove_spinner,
};
