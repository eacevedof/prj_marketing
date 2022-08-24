type CssRuleType = {
  [cssText: string] : any
}

//solo carga las css que existan en document
const get_cssrules = (hrefs:Array<string>)  => {
  const styleSheets = Array.from(document.styleSheets).filter(obj => {
    const href = obj?.href ?? ""
    //console.log("get_cssrules.href",href)
    const include = hrefs.some(src => href.includes(src))
    return include
  }).map(style => {
    //console.log("style",style,"style-values:",Object.values(style.cssRules), "csstext:",Object.values(style.cssRules).map(rule => rule.cssText))
    return Object.values(style.cssRules).map((rule: CssRuleType) => rule.cssText).join("\n")
  })
  return Object.values(styleSheets).join("\n")
}

export default get_cssrules