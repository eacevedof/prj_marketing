export const get_cookie = cookiename  => {
  const nameEQ = cookiename + "="
  var ca = document.cookie.split("")

  for(var i=0; i < ca.length; i++) {
    var c = ca[i]
    while (c.charAt(0)==" ") c = c.substring(1,c.length)
    if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length)
  }
  return null
}

const set_cookie = (cookiename, value, expiredays) => {
  const expdate = new Date()
  expdate.setDate(expdate.getDate() + expiredays)
  const cookieval = escape(value) + ((expiredays === null) ? "" : "; expires=" + expdate.toUTCString())
  document.cookie = cookiename + "=" + cookieval
}

export const erase = cookiename => document.cookie = cookiename + "=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;"

export default set_cookie

/*
rodrigo carla jorge
natalia maria xavi
eduardo mayte
undefined

 */