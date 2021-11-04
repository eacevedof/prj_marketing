export const get_cookie = name => {
  const parts = document.cookie.split("; ")
  const obj = parts
                .map(str => str.split("="))
                .filter(ar => ar[0] === name)
                .map(ar => ar[1])
  return obj.length ? obj[0] : null
}

const set_cookie = (name, value, days) => {
  let strexpire = ""
  if (days) {
    const date = new Date();
    date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000))
    strexpire = "; expires=" + date.toUTCString()
  }

  document.cookie = name.concat("=")
                      .concat(value || "")
                      .concat(strexpire)
                      .concat("; path=/")
}

export const erase_cookie = name => document.cookie = name + "=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;"

export default set_cookie