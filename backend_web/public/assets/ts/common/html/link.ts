export const get_link_local = (urlhref: string, text:string): string => {
  let url: string = urlhref.trim()
  if (!url) return ""
  if (!url.startsWith("/")) url = "/".concat(url)
  return `<a href="${url}" target="_blank" class="link-info">${text}</a>`
}

export const get_link = (urlhref: string, text:string): string => {
  const url = urlhref.trim()
  if (!url) return ""
  if (!(url.includes("https://") || url.includes("http://"))) return ""

  return `<a href="${url}" target="_blank" class="link-info">${text}</a>`
}

export const get_img_link = (urlhref: string): string => {
  const url = urlhref.trim()
  if (!url) return ""
  if (!(url.includes("https://") || url.includes("http://"))) return ""

  return `<a href="${url}" target="_blank" class="link-info">
      <img src="${url}" class="img-thumbnail wd-30p">
    </a>`
}