const _get_error = error => {
  console.error("from-server:",error)
  return {
    errors: ["Bad response from server"]
  }
}

const _get_response = response => {
  let msg = response?.data?.message
  let code = response?.data?.code
  if(msg && code)
    return {errors:[msg]}

  if (Object.keys(response.data).length>0)
    return response.data

  msg = response?.message
  if(msg)
    return {errors:[msg]}

  msg = response?.errors
  if(msg)
    return {errors: response?.errors}
  return response
}

const injson = {
  async get(url) {
    let resp = null
    try {
      resp = await fetch(url, {
        headers: {
          "Accept": "application/json, text/plain, */*",
          "Content-Type": "application/json"
        }
      })
      resp = await resp?.json()
      return _get_response(resp)
    } catch (error) {
      //este error sería del tipo: error.message "Unexpected token < in JSON at position 0"
      return _get_error(error.message)
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
      return _get_response(resp)
    } catch (error) {
      console.log("ERROR:",error)
      return _get_error(error.message)
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
      return _get_response(resp)
    } catch (error) {
      console.log("ERROR:",error)
      return _get_error(error.message)
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
      return _get_response(resp)
    } catch (error) {
      console.log("ERROR:",error)
      return _get_error(error.message)
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
      return _get_response(resp)
    } catch (error) {
      console.log("ERROR:",error)
      return _get_error(error.message)
    }
  }
}

const text = {
  async get(url) {
    let resp = null
    try {
      resp = await fetch(url)
      resp = await resp?.text()
      return _get_response(resp)
    } catch (error) {
      return _get_error(error.message)
    }
  },
}

export default injson