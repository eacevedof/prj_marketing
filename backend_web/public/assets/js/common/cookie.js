export const get_cookie = name => {
  const nameEQ = name.concat("=")
  const parts = document.cookie.split(";")

  for(let i=0; i < parts.length; i++) {
    let c = parts[i];
    while (c.charAt(0) === " ")
      c = c.substring(1, c.length)

    if (c.indexOf(nameEQ) === 0)
        return c.substring(nameEQ.length,c.length);
  }

  return null
}

const set_cookie = (name, value, days) => {
  let strexpire = ""
  if (days) {
    const date = new Date();
    date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000))
    strexpire = "; expires=" + date.toUTCString()
  }

  document.cookie = name.concat("=")
    .concat(value || "").concat(strexpire).concat("; path=/")
}

export const erase_cookie = name => document.cookie = name + "=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;"

export default set_cookie

/*
rodrigo carla jorge
natalia maria xavi
eduardo mayte
undefined

 */