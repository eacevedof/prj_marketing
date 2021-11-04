export const get_cookie = name => {
  const parts = document.cookie.split("; ")
  const obj = parts
                .map(str => str.split("="))
                .filter(ar => ar[0] === name)
                .map(ar => ar[1])
  return obj.length ? obj[0] : null
}

const set_cookie = (name, value, days) => {
  const pieces = [
    `${name}=${value.toString()}`
  ]

  if (days) {
    const date = new Date()
    date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000))
    pieces.push(`expires=${date.toUTCString()}`)
  }

  pieces.push("path=/")
  console.log(pieces)
  document.cookie = pieces.join("; ")
}

export const erase_cookie = name => {
  const pieces = [
    `${name}=`,
    "path=/",
    "expires=Thu, 01 Jan 1970 00:00:01 GMT"
  ]

  document.cookie = pieces.join("; ")
}

export default set_cookie