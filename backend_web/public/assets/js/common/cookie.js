export const get_cookie = cookiename  => {
  let name = cookiename.concat("=")
}

const set_cookie = (cookiename, value, expiredays) => {
  const expdate = new Date();
  expdate.setDate(expdate.getDate() + expiredays);
  const cookieval = escape(value) + ((expiredays === null) ? "" : "; expires=" + expdate.toUTCString());
  document.cookie = cookiename + "=" + cookieval;
}

export const erase = cookiename => document.cookie = cookiename + "=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;"

export default set_cookie

/*
rodrigo carla jorge
natalia maria xavi
eduardo mayte
undefined

 */