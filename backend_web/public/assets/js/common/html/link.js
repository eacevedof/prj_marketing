export const get_link_local = (urlhref, text) => {
    let url = urlhref.trim();
    if (!url)
        return "";
    if (!url.startsWith("/"))
        url = "/".concat(url);
    return `<a href="${url}" target="_blank" class="link-info">${text}</a>`;
};
export const get_link = (urlhref, text) => {
    const url = urlhref.trim();
    if (!url)
        return "";
    if (!(url.includes("https://") || url.includes("http://")))
        return "";
    return `<a href="${url}" target="_blank" class="link-info">${text}</a>`;
};
export const get_img_link = (urlhref) => {
    const url = urlhref.trim();
    if (!url)
        return "";
    if (!(url.includes("https://") || url.includes("http://")))
        return "";
    return `<a href="${url}" target="_blank" class="link-info">
      <img src="${url}" class="img-thumbnail wd-30p">
    </a>`;
};
