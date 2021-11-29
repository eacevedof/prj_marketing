const get_error = error => ({
  error: error
})

export const is_2xx = resp => {
  let r = resp?.code ?? null
  if (!r) return true
  r = parseInt(r)
  return (r>199 && r<300)
}

const req = {
  async get(url) {
    let resp = null
    try {
      resp = await fetch(url, {
        method: "POST",
        headers: {
          "Accept": "application/json, text/plain, */*",
          "Content-Type": "application/json"
        }
      })
      resp = await resp?.json()
      return resp
    } catch (error) {
      console.log("ERROR:",error)
      return get_error(error)
    }
  },
  
  async post(url, data) {
    let resp = null
    try {
      resp = await fetch(url, {
        method: "POST",
        headers: {
          "Accept": "application/json, text/plain, */*",
          "Content-Type": "application/json"
        },
        body: JSON.stringify(data)
      })
      resp = await resp?.json()
      return resp
    } catch (error) {
      console.log("ERROR:",error)
      return get_error(error)
    }
  },

  async put(url, data) {
    let resp = null
    try {
      resp = await fetch(url, {
        method: "PUT",
        headers: {
          "Accept": "application/json, text/plain, */*",
          "Content-Type": "application/json"
        },
        body: JSON.stringify(data)
      })
      resp = await resp?.json()
      return resp
    } catch (error) {
      console.log("ERROR:",error)
      return get_error(error)
    }
  },

  async del(url, data) {
    let resp = null
    try {
      resp = await fetch(url, {
        method: "DELETE",
        headers: {
          "Accept": "application/json, text/plain, */*",
          "Content-Type": "application/json"
        },
        body: JSON.stringify(data)
      })
      resp = await resp?.json()
      return resp
    } catch (error) {
      console.log("ERROR:",error)
      return get_error(error)
    }
  },

  async patch(url, data) {
    let resp = null
    try {
      resp = await fetch(url, {
        method: "PATCH",
        headers: {
          "Accept": "application/json, text/plain, */*",
          "Content-Type": "application/json"
        },
        body: JSON.stringify(data)
      })
      resp = await resp?.json()
      return resp
    } catch (error) {
      console.log("ERROR:",error)
      return get_error(error)
    }
  }
}

export default req