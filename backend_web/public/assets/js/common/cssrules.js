const get_cssrules = hrefs => {
  const styleSheets = Array.from(document.styleSheets).filter(obj => {
    const href = obj?.href ?? ""
    //return href.includes("/themes/valex/")
    const include = hrefs.some(src => href.includes(src))
    //console.log(href,"include?",include)
    return include
  }).map(style => {
    //console.log("style",style,"style-values:",Object.values(style.cssRules), "csstext:",Object.values(style.cssRules).map(rule => rule.cssText))
    return Object.values(style.cssRules).map(rule => rule.cssText).join("\n")
  })
  return Object.values(styleSheets).join("\n")
}

export default get_cssrules