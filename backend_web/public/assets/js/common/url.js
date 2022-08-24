export const get_parameter = (key) => {
    key = key.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
    const regex = new RegExp(`[\\?&]${key}=([^&#]*)`);
    const results = regex.exec(window.location.search);
    return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
};
export const get_url_position = (pos) => {
    var _a;
    let parts = window.location.pathname.split("?");
    parts = parts[0].split("/");
    return (_a = parts[pos]) !== null && _a !== void 0 ? _a : "";
};
export const add_page_to_url = (page, position) => {
    let url = window.location.pathname;
    const urlparts = url.split("/");
    if (urlparts[position]) {
        urlparts[position] = page;
    }
    else {
        urlparts.push(page);
    }
    url = urlparts.join("/");
    const search = window.location.search;
    if (search)
        url = url.concat(search);
    window.history.pushState({}, "", url);
};
function get_querystring(obj, prefix) {
    return Object.keys(obj !== null && obj !== void 0 ? obj : {}).map((objKey) => {
        if (obj === null || obj === void 0 ? void 0 : obj.hasOwnProperty(objKey)) {
            const key = prefix ? `${prefix}[${objKey}]` : objKey;
            // @ts-ignore
            const value = obj[objKey];
            return typeof value === "object" ?
                get_querystring(value, key) :
                `${key}=${encodeURIComponent(value)}`;
        }
        return null;
    }).filter(obj => obj).join("&");
}
export const get_url_with_params = (url, params) => {
    const qs = get_querystring(params, null);
    //console.log("QS",qs)
    const parts = [url, "?", qs];
    return parts.join("");
};
export const get_page_from_url = (position) => {
    const page = get_url_position(position);
    if (!page)
        return null;
    if (isNaN(Number(page)))
        return null;
    return parseInt(page);
};
