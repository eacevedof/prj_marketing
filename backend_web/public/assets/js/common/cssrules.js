const get_cssrules = hrefs => {
  const styleSheets = Array.from(document.styleSheets).filter(obj => {
    const href = obj?.href ?? ""
    //return href.includes("/themes/valex/")
    return hrefs.some(src => href.includes(src))
  }).map(style => {
    //console.log("style",style,"style-values:",Object.values(style.cssRules), "csstext:",Object.values(style.cssRules).map(rule => rule.cssText))
    return Object.values(style.cssRules).map(rule => rule.cssText).join("\n")
  })
  return Object.values(styleSheets).join("\n")
}

export default get_cssrules