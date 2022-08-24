export const onDocumentready = (fnOnEvent) => {
    if (document.readyState != "loading")
        fnOnEvent(event);
    else
        document.addEventListener("DOMContentLoaded", fnOnEvent);
};
export const debounce = (func, timeout = 300) => {
    let timer;
    return (...args) => {
        clearTimeout(timer);
        timer = setTimeout(() => { func.apply(this, args); }, timeout);
    };
};
export const include_js = (pathjs, type = "text/javascript") => {
    //const $head = document.getElementsByTagName("head")[0]
    const $body = document.body;
    const $script = document.createElement("script");
    $script.src = pathjs;
    $script.type = type;
    $script.setAttribute("approle", "jsmodal");
    //$head.appendChild($script)
    $body.appendChild($script);
};
export const async_import = async (src) => {
    const { default: defaultFunc } = await import(src);
    defaultFunc();
};
export const get_as_element = html => {
    const d = document;
    let i, a = d.createElement("div"), b = d.createDocumentFragment();
    a.innerHTML = html;
    while (i = a.firstChild)
        b.appendChild(i);
    return b;
};
export const run_js = $jswrapper => {
    const scripts = $jswrapper.querySelectorAll("script");
    if (!scripts)
        return;
    const doc = document;
    const atrribs = ["type", "src", "nonce", "noModule"];
    scripts.forEach($script => {
        const $docscript = doc.createElement("script");
        $docscript.text = $script.textContent;
        atrribs.forEach(attr => {
            const val = $script.getAttribute(attr);
            if (val)
                $docscript.setAttribute(attr, val);
        });
        doc.head.appendChild($docscript).parentNode.removeChild($docscript);
    });
};
const _append_css = href => {
    const $link = document.createElement("link");
    $link.type = "text/css";
    $link.rel = "stylesheet";
    $link.href = href;
    $link.media = "all";
    document.head.appendChild($link).parentNode.removeChild($link);
};
export const load_css = $wrapper => {
    const links = $wrapper.querySelectorAll("link");
    //console.log("load_css.links",links,"type",typeof links)
    if (!links)
        return;
    const doc = document;
    const atrribs = ["type", "rel", "href", "media"];
    links.forEach($link => {
        //console.log("load_css.link", $link)
        const $doclink = doc.createElement("link");
        atrribs.forEach(attr => {
            const val = $link.getAttribute(attr);
            if (val)
                $doclink.setAttribute(attr, val);
        });
        doc.head.appendChild($doclink).parentNode.removeChild($doclink);
    });
};
export const load_asset_css = paths => {
    if (!paths)
        return;
    let links = document.head.querySelectorAll("link");
    links = Array.from(links).map(link => link.href);
    //console.log("links",links)
    if (typeof paths === "string" || paths instanceof String) {
        const url = `/assets/css/${paths}.css`;
        if (links.filter(href => href.includes(url)).length > 0)
            return;
        const $link = document.createElement("link");
        $link.href = url;
        $link.rel = "stylesheet";
        //si lo quito deja de funcionar
        //con el remove el spinner no se ve
        document.head.appendChild($link); //.parentNode.removeChild($link)
        return;
    }
    paths.forEach(path => {
        const url = `/assets/css/${path}.css`;
        if (links.filter(href => href.includes(url)).length > 0)
            return;
        const $link = document.createElement("link");
        $link.href = url;
        $link.rel = "stylesheet";
        //con el remove el spinner no se ve
        document.head.appendChild($link); //.parentNode.removeChild($link)
    });
};
